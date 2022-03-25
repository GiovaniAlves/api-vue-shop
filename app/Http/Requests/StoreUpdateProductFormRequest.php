<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateProductFormRequest extends FormRequest
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
            'name' => 'required',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'quantity' => 'required|integer',
            'category' => 'required|string',
            'image_url' => 'required|image',
            'specifications' => 'required',
            'specifications.*.id' => 'required|exists:specifications,id'
        ];
    }
}