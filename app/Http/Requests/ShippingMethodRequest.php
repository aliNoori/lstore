<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ShippingMethodRequest extends FormRequest
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
        $shipping_method_Id=$this->route('id');

        return [
            'name' => 'required',
                'string',
                'max:255',
                Rule::unique('shipping_methods')->ignore($shipping_method_Id), // نادیده گرفتن ایمیل کاربر فعلی در صورت ویرایش
            'description' => 'nullable|string|max:1000', // توضیحات اختیاری
            'cost' => 'required|numeric|min:0', // هزینه باید عددی و بزرگتر یا مساوی صفر باشد
            'delivery_time' => 'nullable|string|max:255', // زمان تحویل اختیاری
            'is_active' => 'boolean', // وضعیت فعال بودن باید مقدار صحیح/نادرست داشته باشد
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
