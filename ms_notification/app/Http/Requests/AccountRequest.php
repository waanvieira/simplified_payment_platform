<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
        switch ($this->getMethod()) {
            case 'POST':
                return [
                    'name' => 'required|min:2|max:255',
                    'cpf_cnpj' => 'required|unique:users|min:11|max:18',
                    'email' => 'required|min:10|max:255|unique:users|email',
                    'password' => 'required',
                ];
            case 'PUT':
                return [
                    'name' => 'required|min:2|max:255',
                    'cpf_cnpj' => 'required|min:11|max:18',
                    'email' => 'required|min:10|max:255|email',
                ];
        }
    }
}
