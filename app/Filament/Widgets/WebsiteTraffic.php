<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Visit;
use Illuminate\Support\Carbon;

class WebsiteTraffic extends ChartWidget
{
    protected static ?string $heading = 'Traffic Pengunjung Website';

    protected function getData(): array
    {
        $data = collect();
        $labels = [];

        foreach (range(6, 0) as $day) {
            $date = Carbon::now()->subDays($day)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($day)->translatedFormat('d M');
            $data[] = Visit::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Visit',
                    'data' => $data,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34,197,94,0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
