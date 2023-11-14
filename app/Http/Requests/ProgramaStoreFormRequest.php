<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramaStoreFormRequest extends FormRequest
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
                "nome"=>"required|max:45",
                "descricao"=>"required"
            ];
        }


    public function messages(){
        return [
            "required" => "O campo :attribute é obrigatório.",
            "nome.max" => "O campo nome deve ter no máximo 35 caracteres."
        ];
    }
}
