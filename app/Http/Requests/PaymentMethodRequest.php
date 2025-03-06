<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
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
    public function rules(): array
    {
        $payment_methods_Id=$this->route('id');

        return [
            'name' => 'required','string','max:255',
            Rule::unique('payment_methods')->ignore($payment_methods_Id), // نادیده گرفتن ایمیل کاربر فعلی در صورت ویرایش
            'description' => 'nullable|string|max:1000', // توضیحات اختیاری
            'is_active' => 'boolean', // وضعیت فعال بودن باید مقدار صحیح/نادرست داشته باشد
            'type'=>'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // اعتبارسنجی فایل تصویری
        ];
    }

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
