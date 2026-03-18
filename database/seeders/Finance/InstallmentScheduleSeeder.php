<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\FinancialTransaction;

class InstallmentScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = FinancialTransaction::all();

        $data = [];

        foreach ($transactions as $t) {
            for ($i = 1; $i <= 3; $i++) {
                $data[] = [
                    'enrollment_id' => $t->enrollment_id,
                    'transaction_id' => $t->transaction_id,
                    'installment_number' => $i,
                    'due_date' => now()->addDays($i * 7),
                    'amount' => $t->amount / 3,
                    'status' => 'Pending',
                ];
            }
        }

        InstallmentSchedule::insert($data);
    }
}
