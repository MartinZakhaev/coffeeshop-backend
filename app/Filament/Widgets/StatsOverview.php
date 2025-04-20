<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
                ->description('Registered customers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart($this->getCustomerGrowthData()),
                
            Stat::make('Total Orders', Order::count())
                ->description(Order::where('status', 'completed')->count() . ' completed')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart($this->getOrderTrendData()),
                
            Stat::make('Average Order Value', function () {
                $avg = Order::where('status', 'completed')->avg('total_amount') ?? 0;
                return 'IDR' . number_format($avg, 2);
            })
                ->description('From completed orders')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->chart($this->getAverageOrderValueData()),
        ];
    }

    protected function getCustomerGrowthData(): array
    {
        // Get customer growth for the last 7 days
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $data[] = Customer::whereDate('created_at', $date->toDateString())->count();
        }
        return $data;
    }

    protected function getOrderTrendData(): array
    {
        // Get order trend for the last 7 days
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $data[] = Order::whereDate('created_at', $date->toDateString())->count();
        }
        return $data;
    }

    protected function getAverageOrderValueData(): array
    {
        // Get average order value trend for the last 7 days
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $avg = Order::whereDate('created_at', $date->toDateString())
                ->where('status', 'completed')
                ->avg('total_amount') ?? 0;
            $data[] = round($avg, 2);
        }
        return $data;
    }
}
