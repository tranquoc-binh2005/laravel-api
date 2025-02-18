<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Lang;

class PasswordConfirmRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6|required_with:password|same:password',
        ];
    }
    public function attributes(): array
    {
        return [
            'password' => 'Mật khẩu',
            'password_confirmation' => 'Xác nhận mật khẩu',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => Lang::get('validation.required'),
            'password_confirmation.required' => Lang::get('validation.required'),
            'password.same' => Lang::get('validation.same_password'),
            'password_confirmation.same' => Lang::get('validation.same_password'),
        ];
    }

}
