<?php

use App\Models\Setting;

function setting($key, $default = null) {
    try {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    } catch (\Exception $e) {
        return $default;
    }
}

function set_setting($key, $value) {
    return Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    
}
if (! function_exists('set_setting')) {
    function set_setting($key, $value, $description = null) {
        return Setting::setValue($key, $value, $description);
    }
}
