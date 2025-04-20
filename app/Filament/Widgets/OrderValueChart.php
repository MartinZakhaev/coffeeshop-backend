<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class OrderValueChart extends ChartWidget
{
    protected static ?string $heading = 'Order Value Analysis';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = '1/2';
    protected static ?string $maxHeight = '400px'; // Increased from 200px to 400px

    protected function getData(): array
    {
        $averageValues = $this->getAverageOrderValues();
        $totalValues = $this->getTotalOrderValues();
        $labels = $this->getLastWeeksLabels(8);

        return [
            'datasets' => [
                [
                    'label' => 'Average Order Value (IDR)',
                    'data' => $averageValues,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'yAxisID' => 'y',
                    'type' => 'line',
                    'fill' => false,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Total Order Value (IDR)',
                    'data' => $totalValues,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 1,
                    'yAxisID' => 'y1',
                    'type' => 'bar',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Changed from 'scatter' to 'bar'
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'layout' => [
                'padding' => [
                    'top' => 20,
                    'right' => 25,
                    'bottom' => 20,
                    'left' => 25,
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Average Value (IDR)',
                    ],
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Total Value (IDR)',
                    ],
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
            ],
        ];
    }

    protected function getAverageOrderValues(): array
    {
        $data = [];
        for ($i = 7; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $avg = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->avg('total_amount') ?? 0;
            
            $data[] = round($avg, 2);
        }
        return $data;
    }

    protected function getTotalOrderValues(): array
    {
        $data = [];
        for ($i = 7; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $total = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('total_amount') ?? 0;
            
            $data[] = round($total, 2);
        }
        return $data;
    }

    protected function getLastWeeksLabels(int $weeks): array
    {
        $labels = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek()->format('M d');
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek()->format('M d');
            $labels[] = "{$startDate} - {$endDate}";
        }
        return $labels;
    }
}