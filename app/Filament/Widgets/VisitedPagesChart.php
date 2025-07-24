<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Visit;

class VisitedPagesChart extends ChartWidget
{
    protected static ?string $heading = 'Halaman Paling Sering Dikunjungi';

    protected function getData(): array
    {
        // Ambil 5 halaman teratas berdasarkan jumlah kunjungan
        $topPages = Visit::selectRaw('path, COUNT(*) as total')
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kunjungan',
                    'data' => $topPages->pluck('total'),
                    'backgroundColor' => [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                    ],
                ],
            ],
            'labels' => $topPages->pluck('path'),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // bisa juga 'pie' atau 'doughnut'
    }
}
