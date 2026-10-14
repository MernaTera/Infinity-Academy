<?php

namespace App\Http\Controllers\Admin;

use App\Support\BranchContext;

use App\Http\Controllers\Controller;
use App\Models\Finance\Offer;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AuditService;

class OffersController extends Controller
{
    public function index()
    {
        $offers  = Offer::with(['courseTemplates', 'createdBy'])
            ->orderByDesc('created_at')
            ->get();

        $courses = CourseTemplate::where('is_active', true)->get();

        $today = now()->toDateString();

        $stats = [
            'total'   => $offers->count(),
            'active'  => $offers->filter(fn($o) => $o->isActive())->count(),
            'expired' => $offers->filter(fn($o) =>
                $o->end_date && $o->end_date < $today)->count(),
            'upcoming'=> $offers->filter(fn($o) =>
                $o->start_date && $o->start_date > $today)->count(),
        ];

        return view('admin.offers.index', compact('offers', 'courses', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'offer_name'        => 'required|string|max:150',
            'discount_type'     => 'required|in:Percentage,Fixed',
            'discount_value'    => 'required|numeric|min:0.01',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'course_ids'        => 'required|array|min:1',
            'course_ids.*'      => 'exists:course_template,course_template_id',
        ]);

        $conflict = $this->overlappingCourse($request->course_ids, $request->start_date, $request->end_date);
        if ($conflict) {
            return back()->withInput()->with('error', "Course '{$conflict}' already has an active offer in this period.");
        }

        $adminId = Employee::where('user_id', auth()->id())->first()?->employee_id;

        DB::transaction(function () use ($request, $adminId) {
            $offer = Offer::create([
                'offer_name'           => $request->offer_name,
                'discount_type'        => $request->discount_type,
                'discount_value'       => $request->discount_value,
                'start_date'           => $request->start_date,
                'end_date'             => $request->end_date,
                'is_active'            => true,
                'created_by_admin_id'  => $adminId,
            ]);
            AuditService::created('offer', $offer->offer_id, 'offer_name', $offer->offer_name);
            $offer->courseTemplates()->attach($request->course_ids);
        });

        return back()->with('success', 'Offer created successfully.');
    }

    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);

        $request->validate([
            'offer_name'     => 'required|string|max:150',
            'discount_value' => 'required|numeric|min:0.01',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
        ]);

        if ($offer->is_active) {
            $courseIds = $request->has('course_ids')
                ? (array) $request->course_ids
                : $offer->courseTemplates()->pluck('course_template.course_template_id')->all();

            $conflict = $this->overlappingCourse($courseIds, $request->start_date, $request->end_date, $offer->offer_id);
            if ($conflict) {
                return back()->withInput()->with('error', "Course '{$conflict}' already has an active offer in this period.");
            }
        }

        $offer->update($request->only([
            'offer_name', 'discount_type', 'discount_value', 'start_date', 'end_date'
        ]));

        if ($request->has('course_ids')) {
            $offer->courseTemplates()->sync($request->course_ids);
        }

        return back()->with('success', 'Offer updated.');
    }

    public function toggle($id)
    {
        $offer = Offer::findOrFail($id);

        if (!$offer->is_active) {
            $courseIds = $offer->courseTemplates()->pluck('course_template.course_template_id')->all();
            $conflict  = $this->overlappingCourse(
                $courseIds,
                \Carbon\Carbon::parse($offer->start_date)->toDateString(),
                \Carbon\Carbon::parse($offer->end_date)->toDateString(),
                $offer->offer_id
            );
            if ($conflict) {
                return back()->with('error', "Cannot enable this offer — course '{$conflict}' already has an active offer in this period.");
            }
        }

        AuditService::updated('offer', $id, 'is_active', $offer->is_active, !$offer->is_active);
        $offer->update(['is_active' => !$offer->is_active]);
        return back()->with('success', 'Offer ' . ($offer->is_active ? 'enabled' : 'disabled') . '.');
    }

    private function overlappingCourse(array $courseIds, $startDate, $endDate, ?int $excludeOfferId = null): ?string
    {
        foreach ($courseIds as $courseId) {
            $overlap = DB::table('offer')
                ->join('offer_course_template', 'offer.offer_id', '=', 'offer_course_template.offer_id')
                ->where('offer_course_template.course_template_id', $courseId)
                ->when(BranchContext::currentBranchId() !== null, fn($q) => $q->where('offer.branch_id', BranchContext::currentBranchId()))
                ->when($excludeOfferId, fn($q) => $q->where('offer.offer_id', '!=', $excludeOfferId))
                ->where('offer.is_active', true)
                ->where('offer.start_date', '<=', $endDate)
                ->where('offer.end_date', '>=', $startDate)
                ->exists();

            if ($overlap) {
                return CourseTemplate::find($courseId)?->name ?? 'Selected course';
            }
        }

        return null;
    }
}
