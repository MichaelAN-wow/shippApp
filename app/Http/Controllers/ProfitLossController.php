<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\OverheadEntry;
use App\Models\ManualExpense;
use Carbon\Carbon;
use DB;
use PDF;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            abort(403);
        }

        $companyId = $user->company_id;
        $year = $request->get('year', now()->year);
        $view = $request->get('view', 'monthly');

        $monthlyData = [];
        $manualExpenses = ManualExpense::where('company_id', $companyId)->get();

        foreach (range(1, 12) as $month) {
            $start = Carbon::create($year, $month)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $sales = Sale::where('company_id', $companyId)
                         ->whereBetween('created_at', [$start, $end])
                         ->sum('total');

            $supplies = DB::table('order_materials')
                          ->join('orders', 'order_materials.order_id', '=', 'orders.id')
                          ->where('orders.company_id', $companyId)
                          ->whereBetween('orders.received_at', [$start, $end])
                          ->select(DB::raw('SUM(order_materials.unit_price * order_materials.quantity) as total'))
                          ->value('total') ?? 0;

            $laborMinutes = TimeLog::where('company_id', $companyId)
                                   ->whereBetween('start_time', [$start, $end])
                                   ->sum(DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)'));

            $labor = ($laborMinutes / 60) * 15;

            $overhead = OverheadEntry::where('company_id', $companyId)
                                     ->whereMonth('date', $month)
                                     ->whereYear('date', $year)
                                     ->sum('amount');

            $manualOverhead = 0;
            foreach ($manualExpenses as $expense) {
                if ($expense->frequency === 'monthly') {
                    $manualOverhead += $expense->amount;
                } elseif ($expense->frequency === 'yearly' && $month == 1) {
                    $manualOverhead += $expense->amount;
                } elseif ($expense->frequency === 'quarterly' && in_array($month, [1, 4, 7, 10])) {
                    $manualOverhead += $expense->amount;
                }
            }

            $monthlyData[$month] = [
                'label' => $start->format('F'),
                'sales' => $sales,
                'supplies' => $supplies,
                'labor' => round($labor, 2),
                'overhead' => round($overhead + $manualOverhead, 2),
                'net' => round($sales - $supplies - $labor - $overhead - $manualOverhead, 2),
            ];
        }

        ksort($monthlyData);

        // ✅ Supplies per Revenue Dollar Change Arrow
        $currentMonth = now()->month;
        $prevMonth = $currentMonth - 1;

        $currentRatio = $monthlyData[$currentMonth]['sales'] > 0
            ? $monthlyData[$currentMonth]['supplies'] / $monthlyData[$currentMonth]['sales']
            : 0;

        $prevRatio = $prevMonth > 0 && $monthlyData[$prevMonth]['sales'] > 0
            ? $monthlyData[$prevMonth]['supplies'] / $monthlyData[$prevMonth]['sales']
            : 0;

        $changeArrow = '';
        $changeAmount = round($currentRatio - $prevRatio, 2);

        if ($changeAmount > 0) {
            $changeArrow = "↗️ Up $" . number_format(abs($changeAmount), 2) . " from last month";
        } elseif ($changeAmount < 0) {
            $changeArrow = "↘️ Down $" . number_format(abs($changeAmount), 2) . " from last month";
        } else {
            $changeArrow = "No change from last month";
        }

        // ✅ Best & Worst Months by total cost
        $costsPerMonth = [];
        foreach ($monthlyData as $monthNum => $data) {
            $costsPerMonth[$monthNum] = $data['supplies'] + $data['labor'] + $data['overhead'];
        }

        $bestMonthNum = array_keys($costsPerMonth, min($costsPerMonth))[0];
        $worstMonthNum = array_keys($costsPerMonth, max($costsPerMonth))[0];

        $bestMonth = Carbon::create()->month($bestMonthNum)->format('F');
        $worstMonth = Carbon::create()->month($worstMonthNum)->format('F');

        // ✅ Totals
        $totalRevenue = array_sum(array_column($monthlyData, 'sales'));
        $totalSupplies = array_sum(array_column($monthlyData, 'supplies'));
        $totalLabor = array_sum(array_column($monthlyData, 'labor'));
        $totalOverhead = array_sum(array_column($monthlyData, 'overhead'));
        $totalNet = array_sum(array_column($monthlyData, 'net'));

        // ✅ Insight Metrics
        $grossMargin = $totalRevenue > 0 ? (($totalRevenue - $totalSupplies - $totalLabor - $totalOverhead) / $totalRevenue) * 100 : 0;
        $overheadPercentage = $totalRevenue > 0 ? ($totalOverhead / $totalRevenue) * 100 : 0;
        $suppliesPerDollar = $totalRevenue > 0 ? $totalSupplies / $totalRevenue : 0;

        $mostProfitable = collect($monthlyData)->sortByDesc('net')->first();
        $leastProfitable = collect($monthlyData)->sortBy('net')->first();

        // ✅ Overhead Breakdown
        $overheadByMonth = [];
        foreach ($monthlyData as $monthNum => $data) {
            $overheadByMonth[] = [
                'label' => Carbon::create()->month($monthNum)->format('F'),
                'value' => $data['overhead'],
            ];
        }
        usort($overheadByMonth, fn($a, $b) => $b['value'] <=> $a['value']);
        $highestOverheadMonths = array_slice($overheadByMonth, 0, 3);

        // ✅ Years for dropdown
        $availableYears = Sale::where('company_id', $companyId)
                              ->selectRaw('YEAR(created_at) as year')
                              ->distinct()
                              ->pluck('year')
                              ->sortDesc();

        return view('profit_loss.index', compact(
            'monthlyData', 'year', 'view', 'availableYears',
            'totalRevenue', 'totalSupplies', 'totalLabor', 'totalOverhead', 'totalNet',
            'grossMargin', 'overheadPercentage', 'suppliesPerDollar',
            'mostProfitable', 'leastProfitable',
            'bestMonth', 'worstMonth',
            'highestOverheadMonths', 'changeArrow'
        ));
    }

    public function exportCSV($year)
    {
        $companyId = Auth::user()->company_id;
        $manualExpenses = ManualExpense::where('company_id', $companyId)->get();
        $monthlyData = $this->buildMonthlyData($companyId, $year, $manualExpenses);

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=profit_loss_' . $year . '.csv',
        ];

        $callback = function () use ($monthlyData) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Month', 'Sales', 'Supplies', 'Labor', 'Overhead', 'Net']);
            foreach ($monthlyData as $row) {
                fputcsv($handle, [
                    $row['label'], $row['sales'], $row['supplies'],
                    $row['labor'], $row['overhead'], $row['net']
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPDF($year)
    {
        $companyId = Auth::user()->company_id;
        $manualExpenses = ManualExpense::where('company_id', $companyId)->get();
        $monthlyData = $this->buildMonthlyData($companyId, $year, $manualExpenses);

        $pdf = PDF::loadView('profit_loss.pdf', [
            'monthlyData' => $monthlyData,
            'year' => $year
        ]);

        return $pdf->download("profit_loss_{$year}.pdf");
    }

    private function buildMonthlyData($companyId, $year, $manualExpenses)
    {
        $monthlyData = [];

        foreach (range(1, 12) as $month) {
            $start = Carbon::create($year, $month)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $sales = Sale::where('company_id', $companyId)
                         ->whereBetween('created_at', [$start, $end])
                         ->sum('total');

            $supplies = Purchase::where('company_id', $companyId)
                                ->whereBetween('created_at', [$start, $end])
                                ->sum('total_cost');

            $laborMinutes = TimeLog::where('company_id', $companyId)
                                   ->whereBetween('start_time', [$start, $end])
                                   ->sum(DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)'));

            $labor = ($laborMinutes / 60) * 15;

            $overhead = OverheadEntry::where('company_id', $companyId)
                                     ->whereMonth('date', $month)
                                     ->whereYear('date', $year)
                                     ->sum('amount');

            $manualOverhead = 0;
            foreach ($manualExpenses as $expense) {
                if ($expense->frequency === 'monthly') {
                    $manualOverhead += $expense->amount;
                } elseif ($expense->frequency === 'yearly' && $month == 1) {
                    $manualOverhead += $expense->amount;
                } elseif ($expense->frequency === 'quarterly' && in_array($month, [1, 4, 7, 10])) {
                    $manualOverhead += $expense->amount;
                }
            }

            $monthlyData[$month] = [
                'label' => $start->format('F'),
                'sales' => $sales,
                'supplies' => $supplies,
                'labor' => round($labor, 2),
                'overhead' => round($overhead + $manualOverhead, 2),
                'net' => round($sales - $supplies - $labor - $overhead - $manualOverhead, 2),
            ];
        }

        ksort($monthlyData);
        return $monthlyData;
    }
}