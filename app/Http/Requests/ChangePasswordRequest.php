<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class ChangePasswordRequest extends ApiRequest
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
            'user_id' => 'required',
            'password_new' => 'required|min:6',
            'password_old' => 'required',
        ];
    }
}