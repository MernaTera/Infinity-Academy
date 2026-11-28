<?php

namespace App\Http\Controllers;

use App\Models\Academic\Patch;
use App\Models\Finance\CertificateSetting;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\RevenueSplit;
use App\Models\Finance\ServiceSale;
use App\Models\Finance\TestFeeSetting;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\EnrollmentMaterial;
use App\Models\Enrollment\Material;
use App\Models\HR\Employee;
use App\Models\Leads\Lead;
use App\Services\LeadActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ServiceSaleController extends Controller
{
    private const METHOD_MAP = [
        'Cash'          => 'Cash',
        'Instapay'      => 'Transfer',
        'Vodafone_Cash' => 'Online',
    ];

    public function store(Request $request, $leadId)
    {
        $data = $request->validate([
            'service_type'     => 'required|in:Test,Certificate,Material',
            'item_id'          => 'required|integer',
            'enrollment_id'    => 'nullable|integer',
            'payment_method'   => 'required|in:Cash,Instapay,Vodafone_Cash',
            'reference_number' => 'nullable|string|max:255',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $employee = auth()->user()->employee;
        if (!$employee) {
            return response()->json(['message' => 'No employee profile found.'], 403);
        }

        $lead = Lead::find($leadId);
        $canServe = $lead && (
            (int) $lead->owner_cs_id === (int) $employee->employee_id
            || ($lead->status === 'Registered'
                && (!$employee->branch_id || !$lead->branch_id || (int) $lead->branch_id === (int) $employee->branch_id))
        );
        if (!$canServe) {
            return response()->json(['message' => 'This lead is not assigned to you.'], 403);
        }

        $type       = $data['service_type'];
        $isTest     = $type === 'Test';
        $isMaterial = $type === 'Material';
        $label      = $isMaterial ? 'material' : ($isTest ? 'test' : 'certificate');

        $item = match ($type) {
            'Test'        => TestFeeSetting::active()->find($data['item_id']),
            'Certificate' => CertificateSetting::active()->find($data['item_id']),
            'Material'    => Material::where('is_active', true)->find($data['item_id']),
        };

        if (!$item) {
            return response()->json(['message' => 'The selected ' . $label . ' is not available.'], 422);
        }

        $price = round((float) ($isMaterial ? $item->price : $item->fee), 2);
        if ($price <= 0) {
            return response()->json(['message' => 'The selected ' . $label . ' has no price set. Please contact the admin.'], 422);
        }

        $enrollment = null;
        if ($isMaterial) {
            if (!$lead->student_id) {
                return response()->json(['message' => 'This lead has no registered student yet.'], 422);
            }
            $enrollment = Enrollment::withoutGlobalScope('branch')
                ->where('student_id', $lead->student_id)
                ->when(!empty($data['enrollment_id']), fn($q) => $q->where('enrollment_id', $data['enrollment_id']))
                ->orderByDesc('enrollment_id')
                ->first();
            if (!$enrollment) {
                return response()->json(['message' => 'No enrollment found for this student.'], 422);
            }
        }

        $patch = Patch::active()->latest('start_date')->first();
        if (!$patch) {
            return response()->json(['message' => 'There is no active patch right now. Please contact the admin.'], 422);
        }

        $branchId = $employee->branch_id ?? $lead->branch_id ?? $patch->branch_id;
        if (!$branchId) {
            return response()->json(['message' => 'Your account has no branch assigned. Please contact the admin.'], 422);
        }

        $lock = Cache::lock('service-sale-lead-' . $lead->lead_id, 15);
        if (!$lock->get()) {
            return response()->json(['message' => 'This sale is already being saved. Please wait a moment.'], 409);
        }

        try {
            $itemColumn = match ($type) {
                'Test'        => 'test_fee_setting_id',
                'Certificate' => 'certificate_setting_id',
                'Material'    => 'material_id',
            };

            $duplicate = ServiceSale::where('lead_id', $lead->lead_id)
                ->where('service_type', $type)
                ->where($itemColumn, $isMaterial ? $item->material_id : $item->id)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->exists();

            if ($duplicate) {
                return response()->json(['message' => 'This ' . $label . ' was just recorded for this lead.'], 422);
            }

            $extraNotes = trim((string) ($data['notes'] ?? ''));
            $reference  = trim((string) ($data['reference_number'] ?? ''));

            $sale = DB::transaction(function () use ($lead, $employee, $item, $type, $isTest, $isMaterial, $price, $patch, $branchId, $data, $extraNotes, $reference, $enrollment) {
                $sale = ServiceSale::create([
                    'branch_id'              => $branchId,
                    'patch_id'               => $patch->patch_id,
                    'lead_id'                => $lead->lead_id,
                    'student_id'             => $lead->student_id,
                    'enrollment_id'          => $enrollment?->enrollment_id,
                    'client_name'            => $lead->full_name,
                    'client_phone'           => $lead->phone,
                    'service_type'           => $type,
                    'test_fee_setting_id'    => $isTest ? $item->id : null,
                    'certificate_setting_id' => $type === 'Certificate' ? $item->id : null,
                    'material_id'            => $isMaterial ? $item->material_id : null,
                    'item_name'              => $item->name,
                    'price'                  => $price,
                    'payment_method'         => self::METHOD_MAP[$data['payment_method']],
                    'reference_number'       => $reference !== '' ? $reference : null,
                    'notes'                  => $extraNotes !== '' ? $extraNotes : null,
                    'created_by_cs_id'       => $employee->employee_id,
                ]);

                $tx = FinancialTransaction::create([
                    'enrollment_id'          => $enrollment?->enrollment_id,
                    'service_sale_id'        => $sale->service_sale_id,
                    'patch_id'               => $patch->patch_id,
                    'branch_id'              => $branchId,
                    'transaction_type'       => 'Payment',
                    'transaction_category'   => $type,
                    'amount'                 => $price,
                    'payment_method'         => self::METHOD_MAP[$data['payment_method']],
                    'reference_number'       => $sale->reference_number,
                    'notes'                  => $sale->receipt_number . ' · ' . $item->name . ' · ' . $lead->full_name,
                    'created_by_employee_id' => $employee->employee_id,
                ]);

                if ($isMaterial) {
                    EnrollmentMaterial::create([
                        'enrollment_id' => $enrollment->enrollment_id,
                        'material_id'   => $item->material_id,
                        'price'         => $price,
                        'status'        => 'Paid',
                    ]);
                }

                $sharedIds = [];
                if ($isMaterial && $item->revenue_type === 'Shared') {
                    $sharedIds = Employee::whereHas('user.role',
                            fn($q) => $q->whereIn('role_name', ['Customer Service', 'CS Leader']))
                        ->where('status', 'Active')
                        ->where('branch_id', $branchId)
                        ->pluck('employee_id')
                        ->all();
                    if (empty($sharedIds)) {
                        $sharedIds = [$employee->employee_id];
                    }
                }

                if ($sharedIds) {
                    $n   = count($sharedIds);
                    $acc = 0;
                    foreach (array_values($sharedIds) as $i => $empId) {
                        $alloc = ($i === $n - 1) ? round($price - $acc, 2) : round($price / $n, 2);
                        $acc  += $alloc;
                        RevenueSplit::create([
                            'transaction_id'   => $tx->transaction_id,
                            'employee_id'      => $empId,
                            'branch_id'        => $branchId,
                            'patch_id'         => $patch->patch_id,
                            'amount_allocated' => $alloc,
                            'allocation_type'  => 'Shared',
                            'source'           => 'Service',
                        ]);
                    }
                } else {
                    RevenueSplit::create([
                        'transaction_id'   => $tx->transaction_id,
                        'employee_id'      => $employee->employee_id,
                        'branch_id'        => $branchId,
                        'patch_id'         => $patch->patch_id,
                        'amount_allocated' => $price,
                        'allocation_type'  => 'Direct',
                        'source'           => 'Service',
                    ]);
                }

                $line = '[' . now()->format('d M Y') . '] Added ' . $type . ': '
                    . $item->name . ' (' . number_format($price, 2) . ' LE)'
                    . ((int) $lead->owner_cs_id !== (int) $employee->employee_id ? ' — by ' . $employee->full_name : '')
                    . ($extraNotes !== '' ? ' — ' . $extraNotes : '');

                $currentNotes = trim((string) $lead->notes);
                $lead->notes  = $currentNotes === '' ? $line : $currentNotes . "\n" . $line;
                $lead->save();

                LeadActivityLogger::for($lead)
                    ->action($type . '_Purchased')
                    ->notes($line)
                    ->record();

                return $sale;
            });
        } finally {
            $lock->release();
        }

        return response()->json([
            'message'     => $type . ' added successfully.',
            'receipt_url' => route('service-sales.receipt', $sale->service_sale_id),
            'receipt_no'  => $sale->receipt_number,
        ]);
    }

    public function receipt($id)
    {
        $sale = ServiceSale::with(['createdByCs', 'branch'])->findOrFail($id);

        return view('service-sales.receipt', compact('sale'));
    }
}
