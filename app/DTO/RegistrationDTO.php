<?php

namespace App\DTOs;

class RegistrationDTO
{
    public static function fromRequest($request)
    {
        return [
            'full_name' => $request->input('full_name'),
            'birthdate' => $request->input('birthdate'),
            'degree' => $request->input('degree'),
            'location' => $request->input('location'),
            'email' => $request->input('email'),

            'phones' => $request->input('phones'),

            'level_id' => $request->input('level_id'),
            'sublevel_id' => $request->input('sublevel_id'),

            'type' => $request->input('type'),
            'mode' => $request->input('mode'),

            'patch_id' => $request->input('patch_id'),
            'payment_plan_id' => $request->input('payment_plan_id'),

            'test_score' => $request->input('test_score'),
            'test_fee' => $request->input('test_fee'),
        ];
    }
}
