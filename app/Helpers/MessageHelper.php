<?php
namespace App\Helpers;

class MessageHelper
{
    /**
     * بازیابی پیام با جایگزینی متغیرها
     *
     * @param string $key
     * @param array $variables
     * @return string
     */
    public static function getMessage(string $key, array $variables = []): string
    {
        // دریافت پیام از فایل زبان
        $message = trans('messages.' . $key);

        // جایگزینی متغیرها
        foreach ($variables as $variable => $value) {
            $message = str_replace("{{ $variable }}", $value, $message);
        }

        return $message;
    }
}
