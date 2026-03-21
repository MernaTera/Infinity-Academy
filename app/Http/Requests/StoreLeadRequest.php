<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'phone' => ['required',Rule::unique('lead','phone')->ignore($this->lead, 'lead_id')],
            'birthdate' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'source' => 'required',
            'degree' => 'required',
            'start_preference_type'          => 'nullable|in:Current Patch,Next Patch,Specific Date', // ← موجود؟
            'start_preference_date' => 'nullable|date|required_if:start_preference_type,Specific Date',
            'next_call_at'                   => 'nullable|date',   
            'interested_course_template_id' => 'nullable|exists:course_template,course_template_id',
            'interested_level_id' => 'nullable|exists:level,level_id',
            'interested_sublevel_id' => 'nullable|exists:sublevel,sublevel_id',
            'notes' => 'nullable|string'
        ];
    }
}
