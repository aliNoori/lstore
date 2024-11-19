<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PermissionRoleRequest extends FormRequest
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
        return [
            'role' => [
                'required_without:permission',
                'string',
                'exists:roles,name',
                function ($attribute, $value, $fail) {
                    if ($this->filled('permission')) {
                        $fail("The $attribute is prohibited when permission is present.");
                    }
                },
            ],
            'permission' => [
                'required_without:role',
                'string',
                'exists:permissions,name',
                function ($attribute, $value, $fail) {
                    if ($this->filled('role')) {
                        $fail("The $attribute is prohibited when role is present.");
                    }
                },
            ],
        ];
    }

    /* public function rules()
     {
         return [
             //'role_id' => 'nullable|required_without:permissions|integer|exists:roles,id',
             'role' => 'required_without:permission|string|exists:roles,name|prohibited_with:permission',
             'permission' => 'required_without:role|string|exists:permissions,name|prohibited_with:role',
             //'permissions' => 'nullable|required_without:role_id|array',
             //'permissions.*' => 'integer|exists:permissions,id',
         ];
     }*/

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
