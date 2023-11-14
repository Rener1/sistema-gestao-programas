<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramaUpdateFormRequest extends FormRequest
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
                "servidors" => 'required|array|min:1',
                "descricao"=>"required"
            ];
        }


    public function messages(){
        return [
            "required" => "O campo :attribute é obrigatório.",
            "nome.max" => "O campo nome não pode ter mais que 45 caracteres."
        ];
    }
}
