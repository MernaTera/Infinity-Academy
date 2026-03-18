<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finance\RevenueSplit;
use App\Models\Finance\FinancialTransaction;
use App\Models\HR\Employee;

class RevenueSplitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = FinancialTransaction::all();
        $employee = Employee::first();

        $data = [];

        foreach ($transactions as $t) {
            $data[] = [
                'transaction_id' => $t->transaction_id,
                'employee_id' => $employee->employee_id,
                'branch_id' => $t->branch_id,
                'patch_id' => $t->patch_id,
                'amount_allocated' => $t->amount,
                'allocation_type' => 'Direct',
            ];
        }

        RevenueSplit::insert($data);
    }
}
