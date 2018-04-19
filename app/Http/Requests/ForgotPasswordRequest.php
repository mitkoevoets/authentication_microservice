<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'email' => 'required|email|exists:users',
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
            'email.required'    => 'email.required',
            'email.email'    => 'email.email',
            'email.exists'    => 'email.exists',
        ];
    }
}