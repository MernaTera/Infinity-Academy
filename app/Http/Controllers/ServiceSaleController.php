<?php

namespace App\Http\Controllers;

use App\Models\Academic\Patch;
use App\Models\Finance\CertificateSetting;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\RevenueSplit;
use App\Models\Finance\ServiceSale;
use App\Models\Finance\TestFeeSetting;
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
            'service_type'     => 'required|in:Test,Certificate',
            'item_id'          => 'required|integer',
            'payment_method'   => 'required|in:Cash,Instapay,Vodafone_Cash',
            'reference_number' => 'nullable|string|max:255',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $employee = auth()->user()->employee;
        if (!$employee) {
            return response()->json(['message' => 'No employee profile found.'], 403);
        }

        $lead = Lead::where('owner_cs_id', $employee->employee_id)->find($leadId);
        if (!$lead) {
            return response()->json(['message' => 'This lead is not assigned to you.'], 403);
        }

        $isTest = $data['service_type'] === 'Test';
        $item   = $isTest
            ? TestFeeSetting::active()->find($data['item_id'])
            : CertificateSetting::active()->find($data['item_id']);

        if (!$item) {
            return response()->json(['message' => 'The selected ' . ($isTest ? 'test' : 'certificate') . ' is not available.'], 422);
        }

        $price = round((float) $item->fee, 2);
        if ($price <= 0) {
            return response()->json(['message' => 'The selected ' . ($isTest ? 'test' : 'certificate') . ' has no price set. Please contact the admin.'], 422);
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
            $duplicate = ServiceSale::where('lead_id', $lead->lead_id)
                ->where('service_type', $data['service_type'])
                ->where($isTest ? 'test_fee_setting_id' : 'certificate_setting_id', $item->id)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->exists();

            if ($duplicate) {
                return response()->json(['message' => 'This ' . ($isTest ? 'test' : 'certificate') . ' was just recorded for this lead.'], 422);
            }

            $extraNotes = trim((string) ($data['notes'] ?? ''));
            $reference  = trim((string) ($data['reference_number'] ?? ''));

            $sale = DB::transaction(function () use ($lead, $employee, $item, $isTest, $price, $patch, $branchId, $data, $extraNotes, $reference) {
                $sale = ServiceSale::create([
                    'branch_id'              => $branchId,
                    'patch_id'               => $patch->patch_id,
                    'lead_id'                => $lead->lead_id,
                    'student_id'             => $lead->student_id,
                    'client_name'            => $lead->full_name,
                    'client_phone'           => $lead->phone,
                    'service_type'           => $data['service_type'],
                    'test_fee_setting_id'    => $isTest ? $item->id : null,
                    'certificate_setting_id' => $isTest ? null : $item->id,
                    'item_name'              => $item->name,
                    'price'                  => $price,
                    'payment_method'         => self::METHOD_MAP[$data['payment_method']],
                    'reference_number'       => $reference !== '' ? $reference : null,
                    'notes'                  => $extraNotes !== '' ? $extraNotes : null,
                    'created_by_cs_id'       => $employee->employee_id,
                ]);

                $tx = FinancialTransaction::create([
                    'enrollment_id'          => null,
                    'service_sale_id'        => $sale->service_sale_id,
                    'patch_id'               => $patch->patch_id,
                    'branch_id'              => $branchId,
                    'transaction_type'       => 'Payment',
                    'transaction_category'   => $data['service_type'],
                    'amount'                 => $price,
                    'payment_method'         => self::METHOD_MAP[$data['payment_method']],
                    'reference_number'       => $sale->reference_number,
                    'notes'                  => $sale->receipt_number . ' · ' . $item->name . ' · ' . $lead->full_name,
                    'created_by_employee_id' => $employee->employee_id,
                ]);

                RevenueSplit::create([
                    'transaction_id'   => $tx->transaction_id,
                    'employee_id'      => $employee->employee_id,
                    'branch_id'        => $branchId,
                    'patch_id'         => $patch->patch_id,
                    'amount_allocated' => $price,
                    'allocation_type'  => 'Direct',
                    'source'           => 'Service',
                ]);

                $line = '[' . now()->format('d M Y') . '] Added ' . $data['service_type'] . ': '
                    . $item->name . ' (' . number_format($price, 2) . ' LE)'
                    . ($extraNotes !== '' ? ' — ' . $extraNotes : '');

                $currentNotes = trim((string) $lead->notes);
                $lead->notes  = $currentNotes === '' ? $line : $currentNotes . "\n" . $line;
                $lead->save();

                LeadActivityLogger::for($lead)
                    ->action($isTest ? 'Test_Purchased' : 'Certificate_Purchased')
                    ->notes($line)
                    ->record();

                return $sale;
            });
        } finally {
            $lock->release();
        }

        return response()->json([
            'message'     => ($isTest ? 'Test' : 'Certificate') . ' added successfully.',
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
