<?php

namespace App\Http\Requests;

use App\Enum\Config\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Response;

class BaseRequest extends FormRequest
{

    public function failedValidation(Validator $validator){
        $resource = ApiResource::error($validator->errors(), Common::ERROR_MESSAGE, Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new HttpResponseException($resource);
    }
}
