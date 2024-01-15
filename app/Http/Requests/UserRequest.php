<?php

namespace App\Http\Requests;

use App\Http\Helpers\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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


    public function rules()
    {
                  return [
                 'f_name' => 'required',
                 'email' => 'required',
                 'password' => 'required',
                 'phone' => 'required',
                 'user_id' => 'required',
                 'role_id' => 'required',
                 'status' => 'required',
                 'file' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        // send error message
        Helper::sendError('validation error',$validator->errors());
    }
}
