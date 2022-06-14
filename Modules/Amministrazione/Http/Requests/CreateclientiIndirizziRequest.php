<?php

namespace Modules\Amministrazione\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreateclientiIndirizziRequest extends BaseFormRequest
{
    public function rules()
    {
        return [];
    }

    public function translationRules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }

    public function translationMessages()
    {
        return [];
    }
}
