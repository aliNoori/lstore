<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
    public function rules(): array
    {
        return [
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        // ارسال پاسخ JSON سفارشی
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
