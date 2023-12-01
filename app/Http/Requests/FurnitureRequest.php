<?php

namespace App\Http\Requests;

use App\Rules\ValidDimensions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FurnitureRequest extends FormRequest
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
            'price' => 'required|integer|max:100000000',

            'type_of_furniture' => 'required|string',
            'material' => 'required|string',
            'dimensions' => ['required', 'string', new ValidDimensions],
            'color' => 'required|string',
            'style' => 'required|string',
            'condition' => 'required|string',
            'assembly_required' => 'required|boolean',

            'image_urls.*' => 'image|mimes:jpeg,png,jpg,webp',

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
