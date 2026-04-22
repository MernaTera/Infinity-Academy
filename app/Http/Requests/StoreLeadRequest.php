<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'phone' => [
                'required',
                'regex:/^[0-9]{11,15}$/',
                Rule::unique('lead', 'phone')->ignore($this->lead, 'lead_id'),
            ],
            'birthdate'                      => 'nullable|date|before:today',
            'location'                       => 'nullable|string|max:255',
            'source'                         => 'required|in:Facebook,Website,Friend,Walk_In,Google,Other',
            'degree'                         => 'required|in:Student,Graduate',
            'status'                         => 'nullable|in:Waiting,Call_Again,Scheduled_Call,Registered,Not_Interested,Archived',
            'start_preference_type'          => 'nullable|in:Current Patch,Next Patch,Specific Date',
            'start_preference_date'          => 'nullable|date|required_if:start_preference_type,Specific Date',
            'next_call_at'                   => 'nullable|date',
            'interested_course_template_id'  => 'nullable|exists:course_template,course_template_id',
            'interested_level_id'            => 'nullable|exists:level,level_id',
            'interested_sublevel_id'         => 'nullable|exists:sublevel,sublevel_id',
            'notes'                          => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            // Full Name
            'full_name.required' => 'Full name is required.',
            'full_name.string'   => 'Full name must be text.',
            'full_name.min'      => 'Full name must be at least 3 characters.',
            'full_name.max'      => 'Full name cannot exceed 255 characters.',

            // Phone
            'phone.required' => 'Phone number is required.',
            'phone.regex'    => 'Phone must contain a number of 11 digits.',
            'phone.unique'   => 'This phone number is already registered in the system.',

            // Birthdate
            'birthdate.date'   => 'Please enter a valid date.',
            'birthdate.before' => 'Birthdate must be in the past.',

            // Location
            'location.max' => 'Location cannot exceed 255 characters.',

            // Source
            'source.required' => 'Please select how the lead heard about us.',
            'source.in'       => 'Invalid source selected.',

            // Degree
            'degree.required' => 'Please select the lead\'s degree.',
            'degree.in'       => 'Degree must be either Student or Graduate.',

            // Status
            'status.in' => 'Invalid status selected.',

            // Start Preference
            'start_preference_type.in'          => 'Invalid start preference selected.',
            'start_preference_date.required_if' => 'Please enter the specific start date.',
            'start_preference_date.date'        => 'Please enter a valid date for start preference.',

            // Next Call
            'next_call_at.date' => 'Please enter a valid date and time for the next call.',

            // Course & Level
            'interested_course_template_id.exists' => 'Selected course does not exist.',
            'interested_level_id.exists'            => 'Selected level does not exist.',
            'interested_sublevel_id.exists'         => 'Selected sublevel does not exist.',

            // Notes
            'notes.max' => 'Notes cannot exceed 2000 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name'                     => 'Full Name',
            'phone'                         => 'Phone Number',
            'birthdate'                     => 'Birthdate',
            'location'                      => 'Location',
            'source'                        => 'Lead Source',
            'degree'                        => 'Degree',
            'status'                        => 'Status',
            'start_preference_type'         => 'Start Preference',
            'start_preference_date'         => 'Specific Start Date',
            'next_call_at'                  => 'Next Call Date',
            'interested_course_template_id' => 'Course',
            'interested_level_id'           => 'Level',
            'interested_sublevel_id'        => 'Sublevel',
            'notes'                         => 'Notes',
        ];
    }
}