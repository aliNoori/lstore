<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // دریافت ID کاربر در صورت وجود
        $userId = $this->route('id');

        // اگر درخواست به‌روزرسانی است (وجود userId) و کاربر لاگین کرده است
        if ($userId && auth()->check()) {
            return true; // اجازه دسترسی به درخواست داده می‌شود
        }

        // در سایر درخواست‌ها اجازه دسترسی داده نمی‌شود مگر اینکه برای ایجاد کاربر جدید باشد
        return !$userId; // اجازه به ایجاد کاربر جدید (بدون لاگین)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $userId = $this->route('id'); // دریافت id کاربر در صورت وجود

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId), // نادیده گرفتن ایمیل کاربر فعلی در صورت ویرایش
            ],
            //method 1
            //'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8', // در هنگام به‌روزرسانی، پسورد اختیاری است
            //'password2' => $userId ? 'nullable|string|min:8|same:password' : 'required|string|min:8|same:password', // شرط برابری پسوردها
            //method 2
            'password' => $userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed', // استفاده از confirmed
            'image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:6048', // برای اعتبارسنجی فایل تصویری
        ];
    }

    // متد سفارشی برای مدیریت خطاهای اعتبارسنجی
    protected function failedValidation(Validator $validator)
    {
        // ارسال پاسخ JSON سفارشی
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
