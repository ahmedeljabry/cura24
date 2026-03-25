<?php

namespace App\PageBuilder\Fields;

class MultiSelect
{
    public static function get(array $args)
    {
        $name = $args['name'] ?? 'multiselect';
        $label = $args['label'] ?? 'Multi Select';
        $options = $args['options'] ?? [];
        $value = $args['value'] ?? [];
        $info = $args['info'] ?? '';
        $field_id = 'multiselect_' . uniqid(); // Ensure unique ID per widget instance

        // Normalize value to array
        $value = is_array($value) ? $value : json_decode($value, true) ?? [];

        $option_markup = '';
        foreach ($options as $option_value => $option_label) {
            $selected = in_array($option_value, $value) ? 'selected' : '';
            $option_markup .= "<option value='{$option_value}' {$selected}>{$option_label}</option>";
        }

        return <<<HTML
<div class="form-group">
    <label for="{$field_id}">{$label}</label>
    <select id="{$field_id}" name="{$name}[]" multiple class="form-control multiselect-choices">
        {$option_markup}
    </select>
    <small class="form-text text-muted">{$info}</small>
</div>
<!-- Include Choices.js library and CSS if not already loaded -->
<script>
if (typeof Choices === 'undefined') {
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js';
    script.async = false;
    document.head.appendChild(script);

    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css';
    document.head.appendChild(link);
}
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    if (!window.choicesInitialized_{$field_id} && typeof Choices !== 'undefined') {
        try {
            new Choices('#{$field_id}', {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: "Select options",
            });
            window.choicesInitialized_{$field_id} = true;
        } catch (e) {
            console.error('Failed to initialize Choices.js for #{$field_id}: ', e);
        }
    } else if (typeof Choices === 'undefined') {
        console.warn('Choices.js library not loaded for #{$field_id}');
    }
});
</script>
HTML;
    }
}