<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProductPopularityChart extends ChartWidget
{
    protected static ?string $heading = 'Product Popularity';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = '1/2';
    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $popularProducts = $this->getPopularProducts();
        
        return [
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => array_column($popularProducts, 'quantity'),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                    ],
                    'borderColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => array_column($popularProducts, 'name'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.label + ': ' + context.raw + ' units';
                        }",
                    ],
                ],
            ],
        ];
    }

    protected function getPopularProducts(): array
    {
        return OrderItem::select('product_id', DB::raw('SUM(quantity) as quantity'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('quantity')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown Product',
                    'quantity' => $item->quantity,
                ];
            })
            ->toArray();
    }
}