<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('employee.{employeeId}', function ($user, $employeeId) {
    $employee = \App\Models\HR\Employee::where('user_id', $user->id)->first();
    return $employee && (int) $employee->employee_id === (int) $employeeId;
});