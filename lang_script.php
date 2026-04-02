<?php

use App\Language;

Language::firstOrCreate(
    ['slug' => 'ar'],
    ['name' => 'Arabic (AR)', 'direction' => 'rtl', 'status' => 'publish', 'default' => 0]
);

Language::firstOrCreate(
    ['slug' => 'it'],
    ['name' => 'Italian (IT)', 'direction' => 'ltr', 'status' => 'publish', 'default' => 0]
);

$backend_default_lang_data = file_get_contents(resource_path('lang/') . 'default.json');
if(!file_exists(resource_path('lang/') . 'ar.json')) {
    file_put_contents(resource_path('lang/') . 'ar.json', $backend_default_lang_data);
}
if(!file_exists(resource_path('lang/') . 'it.json')) {
    file_put_contents(resource_path('lang/') . 'it.json', $backend_default_lang_data);
}

echo "Languages added successfully.\n";
