<?php

namespace App\Http\Requests\User\Catalogue;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
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
            'id' => 'required|exists:catalogues,id',
            'name' => 'sometimes|required|string',
            'canonical' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('user_catalogues')->ignore($this->route('user_catalogue'))
            ],
            'publish' => 'sometimes|required|min:1, max:2',
            'user_id' => 'sometimes|required|exists:users,id'
        ];
    }
}
