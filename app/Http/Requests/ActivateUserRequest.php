<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class ActivateUserRequest extends FormRequest
{
    /**
     * Users can apply for membership without authentication
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
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'token.required' => 'token.required',
        ];
    }
}