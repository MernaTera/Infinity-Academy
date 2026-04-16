<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentCareService;
use App\Models\Enrollment\WaitingList;
use App\Models\Enrollment\Enrollment;

class StudentCareController extends Controller
{
    protected $service;

    public function __construct(StudentCareService $service)
    {
        $this->service = $service;
    }

public function waitingList()
{
    $waiting = WaitingList::with([
        'enrollment.student',
        'enrollment.courseTemplate',
        'enrollment.level',
        'enrollment.sublevel'
    ])
    ->where('status', 'Active')
    ->latest()
    ->get();

    return view('student-care.waiting-list', compact('waiting'));
}
}
