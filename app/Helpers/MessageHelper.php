<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

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
        // بررسی وجود پیام
        if (!$message || !is_string($message)) {
            return "پیام با کلید '{$key}' پیدا نشد.";
        }
        // جایگزینی متغیرها
        foreach ($variables as $variable => $value) {
            $message = str_replace("{{ $variable }}", $value, $message);
            Log::info('for',[$message]);
        }

        return $message;
    }

}
