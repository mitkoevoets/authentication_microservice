<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'membership_id' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'username_forum' => 'required|unique:users',
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
            'membership_id.required' => 'user.credentials',
            'membership_id.max:255' => 'membership_id.max',
            'email.required' => 'email.required',
            'email.email' => 'email.invalid',
            'email.max' => 'email.max',
            'email.unique' => 'email.unique',
            'password.required' => 'password.required',
            'password.min' => 'password.min',
            'username_forum.required' => 'username_forum.required',
            'username_forum.unique' => 'username_forum.unique',
        ];
    }
}