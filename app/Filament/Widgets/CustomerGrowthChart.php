<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class CustomerGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Growth';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = '1/2';
    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $data = $this->getCustomerGrowthData();
        $labels = $this->getLastDaysLabels(30);

        return [
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getCustomerGrowthData(): array
    {
        // Get customer growth for the last 30 days
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $data[] = Customer::whereDate('created_at', $date->toDateString())->count();
        }
        return $data;
    }

    protected function getLastDaysLabels(int $days): array
    {
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subDays($i)->format('M d');
        }
        return $labels;
    }
}