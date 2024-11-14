<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // اگر متد درخواست POST باشد (ایجاد)، تمام فیلدها اجباری خواهند بود
        // در غیر اینصورت (برای به‌روزرسانی)، فیلدها اختیاری هستند.
        $rules = [
            'street' => $this->isMethod('post') ? 'required|string|max:255' : 'sometimes|string|max:255',
            'city' => $this->isMethod('post') ? 'required|string|max:100' : 'sometimes|string|max:100',
            'state' => $this->isMethod('post') ? 'required|string|max:100' : 'sometimes|string|max:100',
            'postal_code' => $this->isMethod('post') ? 'required|string|max:10' : 'sometimes|string|max:10',
            'country' => $this->isMethod('post') ? 'required|string|max:100' : 'sometimes|string|max:100',
        ];

        return $rules;
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
