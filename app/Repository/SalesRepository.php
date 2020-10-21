<?php


namespace App\Repository;


use App\Sales;
use App\Visit;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class SalesRepository
{


    /**
     * @param Visit $visit
     * @param Collection $sales_detail
     * @return Sales
     */
    public function generate(Visit $visit, Collection $sales_detail)
    {

        $sales = new Sales();
        $sales->visit_id = $visit->visit_id;
        $sales->total = $sales_detail->sum('total');
        $sales->save();


        $sales->detail()->saveMany($sales_detail);


        return $sales->fresh();

    }


    public function summary(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        Carbon::setLocale('es');
        CarbonPeriod::setLocale('es');

        if ($start_date === null) {
            $start_date = Carbon::today();
        } else {
            $start_date = Carbon::createFromFormat('m/d/Y h:i:s', $start_date . ' 00:00:00');
        }
        if ($end_date === null) {
            $end_date = Carbon::tomorrow()->subSecond();
        } else {
            $end_date = Carbon::createFromFormat('m/d/Y h:i:s', $end_date . ' 00:00:00')->addDay()->subSecond();
        }

        if ($end_date->greaterThan(Carbon::today())) {
            $end_date = Carbon::tomorrow()->subSecond();
        }

        if ($end_date->diffInDays($start_date) < 6) {
            $start_date = $end_date->copy()->subDays(6);

        }

        $period = collect(CarbonPeriod::create($start_date, $end_date)->toArray());

        $sales_between_dates = Visit::with('sales')
            ->with('employee')
            ->select(\DB::raw("date_format(visited_date,'%d/%m/%Y') as visited_at"), 'visit.*')
            ->whereBetween('visited_date', [$start_date, $end_date])
            ->get();


        $grouped_by_date = $sales_between_dates->groupBy('visited_at');

        $grouped_by_user = $sales_between_dates->groupBy('employee_id')->map(function ($item) {
            return [
                "total" => $item->filter(function ($ele) {
                    return $ele->sales != null;
                })->sum('sales.total'),
                "name" => $item->first()->employee->name
            ];
        });


        $linear_graphic = ($period->map(function ($item) use ($grouped_by_date) {
            $total = 0;
            if ($grouped_by_date->has($item->format('d/m/Y'))) {
                $total = $grouped_by_date->get($item->format('d/m/Y'))
                    ->filter(function ($ele) {
                        return $ele->sales != null;
                    })->sum('sales.total');
            }
            return [
                $item->format('d/m/Y') => $total
            ];
        }));


        $last_sales = Sales::without('detail')
            ->with('visit')
            ->with('visit.employee')
            ->with('visit.customer')
            ->orderByDesc('sales_id')
            ->limit(6)
            ->get();


        return [
            'linear_graphic' => $linear_graphic,
            'latest_visits' => $last_sales,
            'users' => $grouped_by_user
        ];


    }

    public function findById($id)
    {
        return Sales::with('visit')
            ->with('visit.employee')
            ->with('visit.customer')
            ->findOrFail($id);
    }


}
