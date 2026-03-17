<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $leadId = $this->route('lead');

        return [

            'full_name' => 'required|string|max:255',

            'phone' => [
                'required',
                'string',
                'max:50',
                Rule::unique('lead','phone')->ignore($leadId,'lead_id')
            ],

            'source' => 'required',

            'degree' => 'required',

            'interested_course_template_id'
                => 'nullable|exists:course_template,course_template_id',

            'interested_level_id'
                => 'nullable|exists:level,level_id',

            'interested_sublevel_id'
                => 'nullable|exists:sublevel,sublevel_id',

            'next_call_at' => 'nullable|date',

            'notes' => 'nullable|string'

        ];
    }
}
