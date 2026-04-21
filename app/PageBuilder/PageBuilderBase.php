<?php


namespace App\PageBuilder;


use App\Helpers\LanguageHelper;
use App\PageBuilder;
use App\PageBuilder\Traits\LanguageFallbackForPageBuilder;

abstract class PageBuilderBase
{
    use LanguageFallbackForPageBuilder,PageBuilder\Traits\RenderViewString;
    protected $args;
    protected $rand_number;
    public $base_preview_image_path;

    public function __construct(array $args=[])
    {
        $defaults = [
            'id' => '',
            'name' => $this->addon_name(),
            'type' =>'',
            'order' =>'',
            'location' =>'',
            'page_id' =>'',
            'page_type' =>'',
            'before' => true,
            'after' => true
        ];
        $this->args = array_merge($defaults,$args);
        $this->base_preview_image_path = asset('assets/backend/page-builder');
        try {
            $this->rand_number = random_int(999, 99999);
        } catch (\Exception $e) {

        }
    }

    /**
     * preview_image
     * this method must have to implement by all widget to show a preview image at admin panel so that user know about the design which he want to use
     * @since 1.0.0
     * */
    abstract public function preview_image();

    /**
     * admin_render
     * this method must have to implement by all widget to render admin panel widget content
     * @since 1.0.0
     * */
    abstract public function admin_render();

    /**
     * frontend_render
     * this method must have to implement by all widget to render frontend widget content
     * @since 1.0.0
     * */
    abstract public function frontend_render();
    /**
     * widget_title
     * this method must have to implement by all widget to register widget title
     * @since 1.0.0
     * */
    abstract public function addon_title();
    /**
     * addon_name
     * get addon name from class name
     * @since 1.0.0
     * */
    public function addon_name()
    {
        return substr(strrchr(static::class, "\\"),1);
    }
    /**
     * addon_namespace
     *  get addon class namespace
     * @since 1.0.0
     * */
    public function addon_namespace()
    {
        return static::class;
    }
    /**
     * widget_name
     * this method must have to implement by all widget to register widget name
     * @since 1.0.0
     * */
    public function enable() : bool
    {
        // TODO: Implement widget_name() method.
        return true;
    }

    /**
     * default_fields
     * this method will return all the default field required by any widget
     * @since 1.0.0
     * */
    public function default_fields(): string
    {
        //all initial field
        $output = '';
        $output .= !empty($this->args['id']) ? '<input type="hidden" name="id" value="' . $this->args['id'] . '">' : '';
        $output .= '<input type="hidden" value="' . $this->args['name'] . '" name="addon_name">';
        $output .= '<input type="hidden" value="' . base64_encode($this->args['namespace']) . '" name="addon_namespace">';
        $output .= '<input type="hidden" value="' . $this->args['type'] . '" name="addon_type">';
        $output .= '<input type="hidden" value="' . $this->args['location'] . '" name="addon_location">';
        $output .= '<input type="hidden" value="' . $this->args['order'] . '" name="addon_order">';
        $output .= '<input type="hidden" value="' . $this->args['page_id'] . '" name="addon_page_id">';
        $output .= '<input type="hidden" value="' . $this->args['page_type'] . '" name="addon_page_type">';

        return $output;
    }
    /**
     * get_settings
     * this method will return all the settings value saved for widget
     * @since 1.0.0
     * */

    public function get_settings()
    {
        $widget_data = !empty($this->args['id']) ? PageBuilder::find($this->args['id']) : [];
        $widget_data = !empty($widget_data) ? unserialize($widget_data->addon_settings,['class' => false]) : [];

        if (request()->is('admin-home') || request()->is('admin-home/*')) {
            return $widget_data;
        }

        $user_lang    = LanguageHelper::user_lang_slug();
        $default_lang = LanguageHelper::default_slug();

        // Build a normalised array regardless of language so that plain keys
        // (e.g. 'title') are always populated from whatever variant is available.
        $mapped_data = [];
        foreach ((array) $widget_data as $key => $value) {
            $mapped_data[$key] = $value;
        }

        // For every key that ends with the default-lang suffix (e.g. title_it),
        // ensure the plain key (title) is also set – handles legacy stored data.
        foreach ((array) $widget_data as $key => $value) {
            if (str_ends_with($key, '_'.$default_lang)) {
                $plain_key = substr($key, 0, -(strlen($default_lang) + 1));
                if (empty($mapped_data[$plain_key]) && !empty($value)) {
                    $mapped_data[$plain_key] = $value;
                }
            }
        }

        // When the visitor is on a non-default language, overlay the lang-specific
        // values over the plain keys (e.g. title_en → title).
        if ($user_lang !== $default_lang && !empty($widget_data)) {
            foreach ((array) $widget_data as $key => $value) {
                if (isset($mapped_data[$key.'_'.$user_lang]) && !empty($mapped_data[$key.'_'.$user_lang])) {
                    $mapped_data[$key] = $mapped_data[$key.'_'.$user_lang];
                }
            }
        }

        return $mapped_data;
    }

    /**
     * get_repeater_data
     * Resolves multilingual repeater arrays for the visitor's current locale.
     *
     * When the Repeater field uses multi_lang=true it stores data under suffixed
     * keys like `title_it`, `title_en`.  frontend_render() methods expect the
     * canonical "no-lang" `title_` format.  This helper:
     *   1. Tries the current locale suffix  (e.g. title_en → title_)
     *   2. Falls back to the default locale  (e.g. title_it → title_)
     *   3. Falls back to the trailing-underscore key already present
     *
     * @param  string $repeater_key  The group ID used when registering the Repeater
     * @return array
     */
    public function get_repeater_data(string $repeater_key): array
    {
        $settings     = $this->get_settings();
        $raw          = $settings[$repeater_key] ?? [];
        if (empty($raw) || !is_array($raw)) {
            return [];
        }

        $user_lang    = LanguageHelper::user_lang_slug();
        $default_lang = LanguageHelper::default_slug();

        // Collect all field base names (strip trailing lang suffixes)
        $base_names = [];
        foreach (array_keys($raw) as $k) {
            // Keys can be:  title_it  title_en  icon_  (legacy no-lang trailing underscore)
            if (preg_match('/^(.+)_([a-z]{2})$/', $k, $m)) {
                $base_names[$m[1]] = true;   // e.g. "title"
            } elseif (str_ends_with($k, '_')) {
                $base_names[rtrim($k, '_')] = true;   // e.g. "icon"
            }
        }

        $resolved = [];
        foreach ($base_names as $base => $_) {
            $user_key    = $base . '_' . $user_lang;    // title_en
            $default_key = $base . '_' . $default_lang; // title_it
            $legacy_key  = $base . '_';                 // title_  (old storage)
            $plain_key   = $base . '_';                 // what frontend reads

            if (isset($raw[$user_key])) {
                $resolved[$plain_key] = $raw[$user_key];
            } elseif (isset($raw[$default_key])) {
                $resolved[$plain_key] = $raw[$default_key];
            } elseif (isset($raw[$legacy_key])) {
                $resolved[$plain_key] = $raw[$legacy_key];
            }

            // Keep all original keys too so nothing is lost
            foreach ($raw as $k => $v) {
                if (!isset($resolved[$k])) {
                    $resolved[$k] = $v;
                }
            }
        }

        return $resolved ?: $raw;
    }

    /**
     * widget_before
     * this method will add widget before html markup for widget in frontend
     * @since 1.0.0
     */
    public function addon_area_before($class = null)
    {
        return '<div class="'.$class.' '.$this->args['location'].'-addon '.$this->args['page_type'].'-page-builder-addon">';
    }

    /**
     * widget_after
     * this method will add widget after html markup for widget in frontend
     * @since 1.0.0
     */
    public function addon_area_after()
    {
        return '</div>';
    }

    /**
     * admin_form_start
     * this method will init form markup for admin panel
     * @since 1.0.0
     */

    public function admin_form_start(): string
    {
        return '<form method="post" action="' . route('admin.page.builder.' . $this->args['type']). '" enctype="multipart/form-data"><input type="hidden" value="' . csrf_token() . '" name="_token">';
    }

    /**
     * admin_form_end
     * this method will end tag form markup for admin panel
     * @since 1.0.0
     */
    public function admin_form_end(): string
    {
        return '</form>';
    }

    /**
     * admin_form_submit_button
     * this method will add a submit button for widget in admin panel
     * @since 1.0.0
     */

    public function admin_form_submit_button($text = null): string
    {
        $button_text = $text ?? __('Save Changes');
        return '<button class="btn btn-success btn-md widget_save_change_button">' . $button_text . '</button>';
    }

    /**
     * admin_form_submit_button
     * this method will add a submit button for widget in admin panel
     * @since 1.0.0
     */

    public function admin_language_tab(): string
    {
        $all_languages = LanguageHelper::all_languages();
        $output = '<nav><div class="nav nav-tabs" role="tablist">';
        foreach ($all_languages as $key => $lang) {
            $active_class = $key == 0 ? 'nav-item nav-link active' : 'nav-item nav-link';
            $output .= '<a class="' . $active_class . '"  data-toggle="tab" href="#nav-home-'. $lang->slug .$this->rand_number. '" role="tab"  aria-selected="true">' . $lang->name . '</a>';
        }
        $output .= '</div></nav>';
        return $output;
    }
    /**
     * admin_language_tab_start
     * this method will add language tab content start wrapper
     * @since 1.0.0
     * */

    public function admin_language_tab_start(){
        return '<div class="tab-content margin-top-30" >';
    }

    /**
     * admin_language_tab_end
     * this method will add language tab content end wrapper
     * @since 1.0.0
     * */
    public function admin_language_tab_end(){
        return '</div>';
    }

    /**
     * admin_language_tab_content_start
     * this method will add language tab panel start
     * @since 1.0.0
     * */

    public function admin_language_tab_content_start($args){
        return  '<div class="' . $args['class'] . '" id="'. $args['id'] .$this->rand_number .'" role="tabpanel">';
    }
    /**
     * admin_language_tab_content_end
     * this method will add language tab panel end
     * @since 1.0.0
     * */
    public function admin_language_tab_content_end(){
        return '</div>';
    }


    public function admin_form_before(){
        $markup = '';
        $settings = htmlspecialchars(json_encode($this->get_settings()), ENT_QUOTES, 'UTF-8');
        if ($this->args['before']){
            $markup .= '<li class="ui-state-default widget-handler" data-name="'.$this->addon_name().'" data-settings=\''.$settings.'\'>';
        }
        $markup .= '<h4 class="top-part"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$this->addon_title().'</h4>
        <span class="expand"><i class="ti-angle-down"></i></span>
        <span class="remove-widget"><i class="ti-close"></i></span>
        <div class="content-part">';
        return $markup;
    }
    public function admin_form_after(): string
    {
        $markup = '</div>';
        if ($this->args['after']){
            $markup .= '</li>';
        }
        return $markup;
    }

    public function get_preview_image($image_name): string
    {
        if (empty($image_name)){return '';}
        return '<a href="'.$this->base_preview_image_path.'/'.$image_name.'" title="'.__('preview of this addon').'" class="preview-image" target="_blank"><i class="fas fa-image"></i></a>';
    }
}