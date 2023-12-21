<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'pname' => 'required|string|max:255',
            'description' => 'required|string',
            'Province' => 'required|string',
            'District' => 'required|string',
            'Municipality' => 'required|string',
            'price' => 'required|integer',

            'type_of_item' => 'required|string',
            'era_period' => 'required|string',
            'material' => 'required|string',
            'condition' => 'required|string',
            'provenance_location' => 'required|string',
            'rarity' => 'required|string',
            'historical_significance' => 'required|string',
            'certification' => 'nullable|string',

            'brand' => 'required|string',

            'author_artist' => 'required|string',
            'genre' => 'required|string',
            'format' => 'required|string',
            'edition' => 'required|string',
            'isbn_upc' => 'required|string',

            'model' => 'required|string',
            'year' => 'required|integer',
            'mileage' => 'required|integer',
            'km_driven' => 'required|string',
            'color' => 'required|string',
            'used_time' => 'required|string',
            'fuel_type' => 'required|string',
            'owner' => 'required|string',
            'transmission_type' => 'required|string',

            'type_of_clothing_accessory' => 'required|string',
            'size' => 'required|string',
            'care_instructions' => 'required|string',

            'type_of_electronic' => 'required|string',
            'warranty_information' => 'nullable|string',

            'image_urls.*' => 'image|mimes:jpeg,png,jpg,webp',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
