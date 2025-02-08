<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PaymentGatewayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $paymentGatewayId = $this->route('id'); // شناسه پرداخت که در مسیر موجود است را دریافت می‌کنیم

        return [
            'gateway' => [
                'required',
                'string',
                'max:100',
                Rule::unique('payment_gateways')->ignore($paymentGatewayId), // نادیده گرفتن نام در صورت ویرایش
            ],
            'type' => 'nullable|string|max:100', // نوع به صورت اختیاری
            'description' => 'nullable|string|max:1000', // توضیحات اختیاری
            'is_active' => 'boolean', // وضعیت فعال بودن باید مقدار صحیح/نادرست داشته باشد
            'terminal_id' => [
                'required',
                'string',
                'max:100',
                Rule::unique('payment_gateways')->ignore($paymentGatewayId), // نادیده گرفتن شناسه ترمینال در صورت ویرایش
            ],
            'wsdl' => 'nullable|url|max:2048', // آدرس WSDL اختیاری با محدودیت 2048 کاراکتر
            'wsdl_confirm' => 'nullable|url|max:255', // آدرس تایید WSDL اختیاری با محدودیت 255 کاراکتر
            'wsdl_reverse' => 'nullable|url|max:255', // آدرس برگشت WSDL اختیاری با محدودیت 255 کاراکتر
            'wsdl_multiplexed' => 'nullable|url|max:255', // آدرس WSDL برای پرداخت‌های چندگانه
            'payment_gateway'=>'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // فایل تصویری اختیاری با حداکثر 2 مگابایت
        ];
    }

    /**
     * Custom message for validation errors.
     *
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'gateway.required' => 'The gateway name is required.',
            'gateway.unique' => 'This gateway name is already taken.',
            'terminal_id.required' => 'The terminal ID is required.',
            'terminal_id.unique' => 'This terminal ID is already registered.',
            'file.image' => 'The file must be an image.',
            'file.mimes' => 'The image must be in jpeg, png, jpg, gif, or webp format.',
            'file.max' => 'The image size must be under 2MB.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
