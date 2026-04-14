<?php


namespace App\PageBuilder\Addons\FeatureService;

use App\PageBuilder\Fields\ColorPicker;
use App\Service;
use App\PageBuilder\Fields\Number;
use App\PageBuilder\Fields\Slider;
use App\PageBuilder\Fields\Text;
use App\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use Str;

class FeatureServiceThree extends \App\PageBuilder\PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'home_three/feature_service_3.jpg';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();


        $output .= $this->admin_language_tab();
        $output .= $this->admin_language_tab_start();

        $all_languages = \App\Helpers\LanguageHelper::all_languages();
        foreach ($all_languages as $key => $lang) {
            $output .= $this->admin_language_tab_content_start([
                'class' => $key == 0 ? 'tab-pane fade show active' : 'tab-pane fade',
                'id' => 'nav-home-' . $lang->slug
            ]);

            $output .= Text::get([
                'name' => 'title_'.$lang->slug,
                'label' => __('Title'),
                'value' => $widget_saved_values['title_'.$lang->slug] ?? null,
            ]);
            $output .= Text::get([
                'name' => 'explore_all_'.$lang->slug,
                'label' => __('Explore Text'),
                'value' => $widget_saved_values['explore_all_'.$lang->slug] ?? null,
            ]);

            $output .= Text::get([
                'name' => 'book_appointment_'.$lang->slug,
                'label' => __('Book Appointment Button Text'),
                'value' => $widget_saved_values['book_appointment_'.$lang->slug] ?? 'Book Now',
            ]);

            $output .= $this->admin_language_tab_content_end();
        }
        $output .= $this->admin_language_tab_end();

        $output .= Number::get([
            'name' => 'items',
            'label' => __('Items'),
            'value' => $widget_saved_values['items'] ?? null,
            'info' => __('enter how many item you want to show in frontend'),
        ]);
        $output .= Slider::get([
            'name' => 'padding_top',
            'label' => __('Padding Top'),
            'value' => $widget_saved_values['padding_top'] ?? 260,
            'max' => 500,
        ]);
        $output .= Slider::get([
            'name' => 'padding_bottom',
            'label' => __('Padding Bottom'),
            'value' => $widget_saved_values['padding_bottom'] ?? 190,
            'max' => 500,
        ]);
        $output .= ColorPicker::get([
            'name' => 'section_bg',
            'label' => __('Background Color'),
            'value' => $widget_saved_values['section_bg'] ?? null,
            'info' => __('select color you want to show in frontend'),
        ]);
        $output .= ColorPicker::get([
            'name' => 'btn_color',
            'label' => __('Button Background Color'),
            'value' => $widget_saved_values['btn_color'] ?? null,
            'info' => __('select color you want to show in frontend'),
        ]);

        $output .= ColorPicker::get([
            'name' => 'button_text_color',
            'label' => __('Button Text Color'),
            'value' => $widget_saved_values['button_text_color'] ?? null,
            'info' => __('select color you want to show in frontend'),
        ]);


        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }
    

    public function frontend_render() : string
    {
        
        $settings = $this->get_settings();
        $current_lang = app()->getLocale();
        $section_title = $settings['title_'.$current_lang] ?? $settings['title'] ?? '';
        $explore_text = $settings['explore_all_'.$current_lang] ?? $settings['explore_all'] ?? '';
        $items =$settings['items'];
        $padding_top = $settings['padding_top'] ?? '';
        $padding_bottom = $settings['padding_bottom'] ?? '';
        $section_bg = $settings['section_bg'] ?? '';
        $btn_color = $settings['btn_color'] ?? '';
        $button_text_color = $settings['button_text_color'] ?? '';
        $book_appoinment = $settings['book_appointment_'.$current_lang] ?? $settings['book_appointment'] ?? 'Book Now';


        //static text helpers
        $static_text = static_text();

        $services = Service::select('id','title','image','description','price','slug','seller_id', 'service_city_id','is_service_online')
        ->where(['status'=>1,'is_service_on'=>1,'featured'=>1])
        ->when(subscriptionModuleExistsAndEnable('Subscription'),function($q){
            $q->whereHas('seller_subscription');
        })
        ->take($items)
        ->inRandomOrder()
        ->get();

     return $this->renderBlade('features.feature-three',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'section_bg' => $section_bg,
            'section_title' => $section_title,
            'explore_text' => $explore_text,
            'services' => $services,
            'book_appoinment' => $book_appoinment,
            'btn_color' => $btn_color,
            'button_text_color' => $button_text_color,
             'static_text' => $static_text
        ]);
}

    public function addon_title()
    {
        return __('Featured Service: 03');
    }
}