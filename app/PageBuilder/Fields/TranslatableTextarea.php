<?php

namespace App\PageBuilder\Fields;

use App\Helpers\LanguageHelper;
use App\PageBuilder\Helpers\Traits\FieldInstanceHelper;
use App\PageBuilder\PageBuilderField;
use Illuminate\Support\Str;

class TranslatableTextarea extends PageBuilderField
{
    use FieldInstanceHelper;

    public function render()
    {
        $output = '';
        $output .= $this->field_before();
        $output .= $this->label();

        $all_languages  = LanguageHelper::all_languages();
        $default_slug   = LanguageHelper::default_slug();
        $random_id      = Str::random(8);
        $name           = $this->name();
        $settings       = $this->args['settings'] ?? [];

        $output .= '<ul class="nav nav-tabs" role="tablist">';
        foreach ($all_languages as $key => $lang) {
            $active = $key == 0 ? 'active' : '';
            $output .= '<li class="nav-item">
                            <a class="nav-link '.$active.'" data-toggle="tab" href="#tab_'.$name.'_'.$random_id.'_'.$lang->slug.'" role="tab">'.$lang->name.'</a>
                        </li>';
        }
        $output .= '</ul>';

        $output .= '<div class="tab-content margin-top-20">';
        foreach ($all_languages as $key => $lang) {
            $active = $key == 0 ? 'show active' : '';

            if ($lang->slug === $default_slug) {
                $value = $settings[$name] ?? ($settings[$name.'_'.$lang->slug] ?? '');
            } else {
                $value = $settings[$name.'_'.$lang->slug] ?? '';
            }

            $output .= '<div class="tab-pane fade '.$active.'" id="tab_'.$name.'_'.$random_id.'_'.$lang->slug.'" role="tabpanel">';

            if ($lang->slug === $default_slug) {
                // Default language: save under plain key so frontend_render() can read it directly
                $output .= '<textarea name="'.$name.'" class="'.$this->field_class().'">'.$value.'</textarea>';
                $output .= '<input type="hidden" value="'.htmlspecialchars($value).'" name="'.$name.'_'.$lang->slug.'" class="translatable-mirror-field" data-mirror-target="'.$name.'">';
            } else {
                $output .= '<textarea name="'.$name.'_'.$lang->slug.'" class="'.$this->field_class().'">'.$value.'</textarea>';
            }

            if (!empty($this->args['info'])) {
                $output .= '<small class="info-text">'.$this->args['info'].'</small>';
            }
            $output .= '</div>';
        }
        $output .= '</div>';

        $output .= $this->field_after();

        return $output;
    }
}
