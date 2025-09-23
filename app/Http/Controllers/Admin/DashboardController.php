<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MongoOrder;
use App\Models\MongoProduct;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $now   = now();
        $today = [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
        $last7 = $now->copy()->subDays(6)->startOfDay();
        $last30Start = $now->copy()->subDays(29)->startOfDay();
        $completedLike = ['processing','completed']; // counts for revenue/AOV

        // --- KPIs (cards) ---
        $revenueToday = (float) MongoOrder::whereIn('status', $completedLike)
            ->whereBetween('created_at', $today)->get()->sum('total');

        $newOrdersToday = MongoOrder::whereBetween('created_at', $today)->count();

        $orders7 = MongoOrder::whereIn('status', $completedLike)
            ->where('created_at', '>=', $last7)->get();
        $rev7 = (float) $orders7->sum('total');
        $count7 = max(1, $orders7->count());
        $aov7 = round($rev7 / $count7, 2);

        $lowStockCount = MongoProduct::where('inventory', '<=', 5)->count();

        // --- Pipeline counts ---
        $pipeline = [
            'pending'    => MongoOrder::where('status','pending')->count(),
            'processing' => MongoOrder::where('status','processing')->count(),
            'completed'  => MongoOrder::where('status','completed')->count(),
            'cancelled'  => MongoOrder::where('status','cancelled')->count(),
        ];

        // --- Revenue last 30 days (labels + data) ---
        $orders30 = MongoOrder::whereIn('status',$completedLike)
            ->where('created_at','>=',$last30Start)->get();

        $byDate = [];
        foreach ($orders30 as $o) {
            $key = ($o->created_at instanceof Carbon)
                ? $o->created_at->format('Y-m-d')
                : (string) $o->created_at;
            $byDate[$key] = ($byDate[$key] ?? 0) + (float) ($o->total ?? 0);
        }
        $labels = [];
        $series = [];
        for ($i=0; $i<30; $i++) {
            $d = $last30Start->copy()->addDays($i);
            $key = $d->format('Y-m-d');
            $labels[] = $d->format('M j');
            $series[] = round((float)($byDate[$key] ?? 0), 2);
        }

        // --- Top products (last 30d) ---
        $topAgg = [];
        foreach ($orders30 as $o) {
            $items = is_iterable($o->items ?? []) ? $o->items : [];
            foreach ($items as $it) {
                $pid = (string) ($it->product_id ?? $it->name ?? '—');
                if (!isset($topAgg[$pid])) {
                    $topAgg[$pid] = [
                        'product_id' => $it->product_id ?? null,
                        'name'       => $it->name ?? '—',
                        'units'      => 0,
                        'revenue'    => 0.0,
                    ];
                }
                $q = (int)($it->quantity ?? 0);
                $line = isset($it->total)
                    ? (float)$it->total
                    : (float)($it->price ?? 0) * $q;

                $topAgg[$pid]['units']   += $q;
                $topAgg[$pid]['revenue'] += $line;
            }
        }
        $topProducts = collect($topAgg)->sortByDesc('units')->take(5)->values();

        // --- Recent orders ---
        $recentOrders = MongoOrder::orderBy('_id','desc')->limit(10)->get();

        // --- Low stock list ---
        $lowStock = MongoProduct::where('inventory','<=',5)
            ->orderBy('inventory','asc')->limit(10)->get();

        $metrics = [
            'revenueToday'  => round($revenueToday, 2),
            'newOrdersToday'=> $newOrdersToday,
            'aov7'          => $aov7,
            'lowStockCount' => $lowStockCount,
        ];

        return view('admin.dashboard', [
            'metrics'     => $metrics,
            'pipeline'    => $pipeline,
            'labels'      => $labels,
            'series'      => $series,
            'topProducts' => $topProducts,
            'recentOrders'=> $recentOrders,
            'lowStock'    => $lowStock,
        ]);
    }
}
