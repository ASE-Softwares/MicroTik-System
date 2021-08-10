<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddWiredClientRequest extends FormRequest
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
            "ip" => 'required|ipv4',
            "client_name" => "required|string",
            "email" => 'nullable|email|unique:users,email',
            'package_id' => 'required|integer|exists:packages,id'
        ];
    }
}
