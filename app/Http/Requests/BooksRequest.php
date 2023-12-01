<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BooksRequest extends FormRequest
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

            'author_artist' => 'required|string',
            'genre' => 'required|string',
            'format' => 'required|string',
            'condition' => 'required|string',
            'edition' => 'required|string',
            'isbn_upc' => 'required|string',
            'description' => 'required|string',

            'image_urls.*' => 'image|mimes:jpeg,png,jpg,webp',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
