<?php

namespace App\Http\Requests;


class UserImportRequest extends ApiRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'membership_id' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required',
            'username_forum' => 'required',
        ];
    }
}
