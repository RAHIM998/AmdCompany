<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'nom'=>['required', 'min:2', 'unique:categories,nom'],
            'description'=>['required', 'min:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la catégory est obligatoire !!',
            'nom.min' => 'Le nom de cette catégory doit être minimum de 2 caractères !!',
            'nom.unique' => 'Le nom de cette catégorie existe déjà !',
            'description.required' => 'La description de la catégory est obligatoire !!',
            'description.min' => 'La description de cette catégory doit être minimum de 2 caractères !!',
        ];
    }
}
