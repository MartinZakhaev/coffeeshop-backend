<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Orders Overview';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = '1/2';
    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $completedOrders = $this->getOrdersDataByStatus('completed');
        $pendingOrders = $this->getOrdersDataByStatus('pending');
        $processingOrders = $this->getOrdersDataByStatus('processing');
        $labels = $this->getLastDaysLabels(14);

        return [
            'datasets' => [
                [
                    'label' => 'Completed Orders',
                    'data' => $completedOrders,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Pending Orders',
                    'data' => $pendingOrders,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.7)',
                    'borderColor' => 'rgb(245, 158, 11)',
                ],
                [
                    'label' => 'Processing Orders',
                    'data' => $processingOrders,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOrdersDataByStatus(string $status): array
    {
        $data = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $data[] = Order::whereDate('created_at', $date->toDateString())
                ->where('status', $status)
                ->count();
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