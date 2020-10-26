<?php


namespace App\Repository;

use App\Debts;
use App\Employee;
use App\Payment;
use App\Product;
use App\Sales;
use App\SalesDetail;
use App\UpcomingVisit;
use App\Visit;
use App\VisitReason;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


class SummaryRepository
{


    public function index(Request $request)
    {


        $visits = $this->getVisitsBetweenDate(
            $request->get('start_date'),
            $request->get('end_date'),
            $request->get('user_id'));

        $carboys = $visits->map->carboys_movements->filter(function ($item) {
            return $item != null;
        });


        $customers_visited = $this->getTotalCustomersVisited($visits);
        $customers_to_visit = $this->getTotalCustomersToVisit($request->get('start_date'),
            $request->get('end_date'),
            $request->get('user_id')
        );
        $returned_carboys = $this->getTotalReturnedCarboys($carboys);
        $borrowed_carboys = $this->getTotalBorrowedCarboys($carboys);

        $sales = $this->getSales($visits);

        $summary_by_product = $this->getSummaryByProduct($visits, $sales);
        $sum_sales = $sales->sum('total');
        $sum_payments = $summary_by_product->reduce(
            function ($carry, $item) {
                return $carry + $item['payments_total'];
            }
        );


        return [
            'visits' => $visits,
            'customers_visited' => $customers_visited,
            'customers_to_visit' => $customers_to_visit,
            'total_visits' => $visits->count(),
            'borrowed_carboys' => $borrowed_carboys,
            'returned_carboys' => $returned_carboys,
            'total_sales' => $sales->count(),
            'sum_sales' => $sum_sales,
            'sum_payments' => $sum_payments,
            'summary_by_product' => $summary_by_product,
            'summary_by_reason' => $this->getSummaryByReason($visits)
        ];


    }


    private function getTotalCustomersVisited($visits)
    {

        return $visits->groupBy('customer_id')->count();

    }


    private function getDateRanges($start_date, $end_date)
    {
        if ($start_date === null) {
            $start_date = Carbon::today();
        } else {
            $start_date = Carbon::createFromFormat('m/d/Y h:i:s', $start_date . ' 00:00:00');
        }
        if ($end_date === null) {
            $end_date = Carbon::tomorrow();
        } else {
            $end_date = Carbon::createFromFormat('m/d/Y h:i:s', $end_date . ' 00:00:00')->addDay()->subSecond();
        }
        return [
            $start_date,
            $end_date
        ];
    }

    private function getTotalCustomersToVisit($start_date = null, $end_date = null, $user_id = null)
    {
        $ranges = $this->getDateRanges($start_date, $end_date);


        $total = UpcomingVisit::whereBetween('next_visit_date', $ranges)
            ->groupBy('customer_id');

        if ($user_id !== null && $user_id !== '') {
            $total = $total->where('employee_id', Employee::find($user_id)->user_id);
        }
        $total = $total->get();

        return count($total);
    }

    private function getTotalBorrowedCarboys($carboys)
    {
        return $this->getTotalCarboysByStatus($carboys, 'B');
    }

    private function getTotalReturnedCarboys($carboys)
    {
        return $this->getTotalCarboysByStatus($carboys, 'R');
    }

    private function getTotalCarboysByStatus($carboys, $status = 'B')
    {
        return $carboys->filter(function ($item) use ($status) {
            return $item->type == $status;
        })->sum('quantity');
    }


    private function getVisitsBetweenDate($start_date = null, $end_date = null, $user_id = null)
    {

        $ranges = $this->getDateRanges($start_date, $end_date);

        $visits = Visit::whereBetween('visited_date', $ranges);


        if ($user_id !== null) {
            $visits = $visits->where('employee_id', $user_id);
        }
        $visits = $visits->with('sales')
            ->with('customer')
            ->with('reason')
            ->get();

        return $visits;
    }

    private function getSales($visits)
    {

        return $visits->filter(function ($item) {
            return $item->sales !== null;
        })->map(function ($item) {
            return $item->sales;
        });
    }


    private function getSummaryByProduct($visits, $sale)
    {

        $ids = $visits->whereIn('reason_id', [2, 5, 6])->pluck('visit_id')->toArray();


        $debts = \DB::table('debts as summary')
            ->select(
                'product.description',
                \DB::raw('summary.product_id as id'),
                \DB::raw('(quantity) as debts_quantity'),
                \DB::raw('(quantity* summary.price) as debts_total'),
                \DB::raw('0 as payments_quantity '),
                \DB::raw('0 as payments_total '),
                \DB::raw('0 as sales_quantity '),
                \DB::raw('0 as sales_total ')
            )->join('product', 'product.product_id', '=', 'summary.product_id')
            ->whereIn('visit_id', $ids)
            ->get();


        $payments = \DB::table('payments as summary')->select(
            'product.description',
            \DB::raw('summary.product_id as id'),
            \DB::raw('0 as debts_quantity '),
            \DB::raw('0 as debts_total '),
            \DB::raw('(quantity) as payments_quantity'),
            \DB::raw('total as payments_total'),
            \DB::raw('0 as sales_quantity '),
            \DB::raw('0 as sales_total ')
        )
            ->join('product', 'product.product_id', '=', 'summary.product_id')
            ->whereIn('visit_id', $ids)
            ->get();


        $sales = \DB::table('sales_detail as summary')->select(
            'product.description',
            \DB::raw('summary.product_id as id '),
            \DB::raw('0 as debts_quantity '),
            \DB::raw('0 as debts_total '),
            \DB::raw('0 as payments_quantity '),
            \DB::raw('0 as payments_total '),
            \DB::raw('(quantity) as sales_quantity'),
            \DB::raw('(quantity*summary.price) as sales_total')
        )->join('product', 'product.product_id', '=', 'summary.product_id')
            ->whereIn('sales_id', $sale->pluck('sales_id')->toArray())
            ->get();


        $collect = collect([])
            ->push($debts)
            ->push($payments)
            ->push($sales)
            ->collapse()
            ->groupBy('description')
            ->map(function ($item) {
                return [
                    'debts_quantity' => $item->reduce(function ($carry, $item) {
                        return $carry + $item->debts_quantity;
                    }, 0),
                    'debts_total' => $item->reduce(function ($carry, $item) {
                        return $carry + $item->debts_total;
                    }, 0),
                    'payments_quantity' => $item->reduce(function ($carry, $item) {
                        return $carry + $item->payments_quantity;
                    }, 0),
                    'payments_total' => $item->reduce(function ($carry, $item) {
                        return $carry + $item->payments_total;
                    }, 0),
                    'sales_quantity' => $item->reduce(function ($carry, $item) {
                        return $carry + $item->sales_quantity;
                    }, 0),
                    'sales_total' => $item->reduce(function ($carry, $item) {
                        return $carry + $item->sales_total;
                    }, 0)
                ];
            });

        return $collect;

    }

    private function getSummaryByReason($visits)
    {


        $summary = collect([]);
        if (count($visits) > 0) {
            $summary = $visits->countBy(function ($visit) {
                return $visit->reason->description;
            });
        }

        return $summary;

    }

}
