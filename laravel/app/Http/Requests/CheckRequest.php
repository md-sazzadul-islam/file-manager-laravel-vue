<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Storage;
class CheckRequest extends FormRequest
{
    public function rules()
    {
        return [
            // 
            'path' => [
                'sometimes',
                'string',
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !Storage::disk($this->input('disk'))->exists($value)
                    ) {
                        return $fail('pathNotFound');
                    }
                },
            ],
        ];
    }
    
    public function message()
    {
        return 'notFound';
    }
    


}
