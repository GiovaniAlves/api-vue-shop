<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleFormRequest extends FormRequest
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
            'total' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'status' => 'required',
            'products' => 'required',
            'products.*.id' => 'required|exists:products,id',
            'products.*.price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'products.*.quantity' => 'required|integer'
        ];
    }
}
