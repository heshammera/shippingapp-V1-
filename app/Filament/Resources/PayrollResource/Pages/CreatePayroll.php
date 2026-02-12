<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    protected function afterFill(): void
    {
        if (empty($this->data['items'])) {
            $employees = \App\Models\Employee::where('is_active', true)->get();
            $items = [];
            $total = 0;

            foreach ($employees as $employee) {
                $items[] = [
                    'employee_id' => $employee->id,
                    'basic_salary' => $employee->basic_salary,
                    'bonuses' => 0,
                    'deductions' => 0,
                    'notes' => '',
                ];
                $total += $employee->basic_salary;
            }

            $this->data['items'] = $items;
            $this->data['total_amount'] = $total;
        }
    }
}
