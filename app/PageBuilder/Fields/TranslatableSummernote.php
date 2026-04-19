<?php

namespace App\PageBuilder\Fields;

use App\Helpers\LanguageHelper;
use App\PageBuilder\Helpers\Traits\FieldInstanceHelper;
use App\PageBuilder\PageBuilderField;
use Illuminate\Support\Str;

class TranslatableSummernote extends PageBuilderField
{
    use FieldInstanceHelper;

    public function render()
    {
        $output = '';
        $output .= $this->field_before();
        $output .= $this->label();

        $all_languages = LanguageHelper::all_languages();
        $random_id = Str::random(8);
        $name = $this->name();
        $settings = $this->args['settings'] ?? [];

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
            $value = $settings[$name.'_'.$lang->slug] ?? ($settings[$name] ?? '');
            
            $output .= '<div class="tab-pane fade '.$active.'" id="tab_'.$name.'_'.$random_id.'_'.$lang->slug.'" role="tabpanel">';
            $output .= '<textarea name="'.$name.'_'.$lang->slug.'" class="summernote '.$this->field_class().'">'.$value.'</textarea>';
            if(!empty($this->args['info'])) {
                $output .= '<small class="info-text">'.$this->args['info'].'</small>';
            }
            $output .= '</div>';
        }
        $output .= '</div>';

        $output .= $this->field_after();

        return $output;
    }
}
