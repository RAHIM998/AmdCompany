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
            'image' => 'nullable|image|max:2048', // Validation pour l'image
            'description' => 'nullable|string',
        ];
    }


    public function messages(): array
    {
        return [
            'categorie_id.required' => 'L\'id du catégorie est obligatoire et doit être un id valide !!',
            'libelle.required' => 'Le libellé est obligatoire et ne doit pas dépasser 255 caractères !!',
            'prix.required' => 'Le prix est obligatoire et doit être supérieur à 0 !!',
            'stock.required' => 'Le stock est obligatoire et doit être supérieur à 0 !!',
            'image.max' => 'La taille de l\'image ne doit pas dépasser 2048 octets !!', // Message pour la taille de l'image
        ];
    }

}
