<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduitRequest extends FormRequest
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
            'categorie_id' => 'required|integer|exists:categories,id',
            'libelle' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ];
    }

    public function messages():array
    {
        return [
            'categorie_id.required' => 'L\id du catégory est obligatoire et doit être un id valide  !!',
            'libelle.required' => 'Le libelle est obligatoire et ne doit pas dépasser 255 caractère !!',
            'prix.required' => 'Le prix est obligatoire et est toujours supérieur à 0 !!',
            'stock.required' => 'Le stock est obligatoire et est toujours supérieur à 0 !!',
            'image.min' => 'La taille de l\'image ne doit pas dépasser 2048 octet!!',
        ];
    }
}
