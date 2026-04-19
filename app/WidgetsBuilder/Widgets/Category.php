<?php

namespace App\WidgetsBuilder\Widgets;
use App\PageBuilder\Fields\MultiSelect;
use App\PageBuilder\Fields\Text;
use App\PageBuilder\Fields\TranslatableText;
use App\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use App\WidgetsBuilder\WidgetBase;
use App\PageBuilder\Fields\Number;
use App\PageBuilder\Fields\Select;
use App\Category as CategoryModel;

class Category extends WidgetBase
{
    use LanguageFallbackForPageBuilder;

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();

        $output .= TranslatableText::get([
            'settings' => $widget_saved_values,
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title'] ?? null,
        ]);

        $categories = CategoryModel::where('status', 1)->pluck('name', 'id')->toArray();

        $output .= MultiSelect::get([
            'name' => 'selected_categories',
            'label' => __('Select Categories'),
            'options' => $categories,
            'value' => $widget_saved_values['selected_categories'] ?? [],
            'info' => __('You can select multiple categories.'),
        ]);
        $output .= Select::get([
            'name' => 'order_by',
            'label' => __('Order By'),
            'options' => [
                'id' => __('ID'),
                'created_at' => __('Date'),
            ],
            'value' => $widget_saved_values['order_by'] ?? null,
            'info' => __('set order by')
        ]);
        $output .= Select::get([
            'name' => 'order',
            'label' => __('Order'),
            'options' => [
                'asc' => __('Accessing'),
                'desc' => __('Decreasing'),
            ],
            'value' => $widget_saved_values['order'] ?? null,
            'info' => __('set order')
        ]);
        $output .= Number::get([
            'name' => 'items',
            'label' => __('Items'),
            'value' => $widget_saved_values['items'] ?? null,
            'info' => __('enter how many item you want to show in frontend'),
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

   public function frontend_render()
{
    $settings = $this->get_settings();
    $title_text = purify_html($settings['title'] ?? '');
    $order_by = purify_html($settings['order_by'] ?? 'id');
    $IDorDate = purify_html($settings['order'] ?? 'asc');
    $items = purify_html($settings['items'] ?? '');
    $selected_categories = $settings['selected_categories'] ?? [];
    
    // Query with selected categories if provided
    $query = CategoryModel::whereHas('services')->where('status', 1);
    
    if (!empty($selected_categories)) {
        $query->whereIn('id', $selected_categories);
    }
    
    $categories = $query->select('id', 'name', 'slug')
        ->orderBy($order_by, $IDorDate)
        ->take($items)
        ->get();
        
    $route = route('service.list.category');

    $category_markup = '';

    foreach ($categories as $cat) {
        $category_name = $cat->name;
        $category_slug = $cat->slug;
        $category_markup.= <<<CATEGORY
    <li class="list"><a href="{$route}/{$category_slug}">{$category_name}</a></li>
CATEGORY;
    }

    return <<<HTML
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="footer-widget widget">
            <h6 class="widget-title">{$title_text}</h6>
            <div class="footer-inner">
                <ul class="footer-link-list">
                    {$category_markup}
                </ul>
            </div>
        </div>
    </div>
HTML;
}

    public function widget_title()
    {
        return __('Category');
    }

}