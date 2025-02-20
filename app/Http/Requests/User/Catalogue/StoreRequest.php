<?php

namespace App\Http\Requests\User\Catalogue;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
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
            'name' => 'required|string',
            'canonical' => 'required|string:unique:user_catalogue,canonical',
            'publish' => 'required|min:1, max:2',
            'user_id' => 'required|exists:users,id'
        ];
    }
}
