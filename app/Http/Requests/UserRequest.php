<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'nom'=>['required', 'min:2'],
            'prenom'=>['required', 'min:2'],
            'telephone'=>['required'],
            'email'=>['required', 'email', 'unique:users,email'],
            'adresse'=>['required', 'min:2'],
            'role' => ['required', Rule::in(['admin', 'client'])],
            'password' => ['required', 'min:4'],
            'c_password' => ['required', 'same:password'],
        ];
    }


    public function messages():array
    {
        return [
            'nom.required' => 'Le nom est obligatoire !!',
            'nom.min' => 'Le nom doit être minimum de 2 caractères !!',
            'prenom.required' => 'Le nom est obligatoire',
            'prenom.min' => 'Le prenom doit être minimum de 2 caractères !!',
            'telephone' => 'Le téléphone est obligatoire !!',
            'email.required' => 'L\'email est obligatoire et doit être un mail valide !!',
            'email.unique' => 'Vous avez déja un compte ! Connectez vous svp !!',
            'adresse.required' => 'L\'adresse est obligatoire !!',
            'adresse.min' => 'L\'adresse doit comporter au moins 2 caractères !!',
            'role.required' => 'Le role est obligatoire !!',
            'role.in' => 'L\'utilisateur doit être soit client soit admin.',
            'password.required' => 'Le mot de passe doit être définie !!',
            'password.min' => 'Pour des raisons de sécurité, votre mot de passe doit faire :min caractères !!',
            'c_password.required' => 'Le mot de passe est obligatoire !!',
            'c_password.same' => 'Désolé les mots de passe ne sont pas identique !!',
        ];
    }
}
