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
        // Pegando o id para poder editar o campo url que é único em uma adição.
        $id = $this->segment(5);

        $rules = [
            'name' => 'required',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'quantity' => 'required|integer',
            'url' => "required|unique:products,url,{$id},id",
            'category' => 'required|string',
            'image' => 'required',
            'specifications' => 'required',
            'specifications.*.id' => 'required|exists:specifications,id'
        ];

        if ($this->method() == 'PUT') {
            $rules['image'] = ['nullable'];
        }

        return $rules;
    }
}
