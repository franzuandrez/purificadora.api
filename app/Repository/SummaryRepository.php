<?php


namespace App\Repository;

use App\Product;
use App\Sales;
use App\SalesDetail;
use App\Visit;
use App\VisitReason;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


class SummaryRepository
{


    public function index()
    {


        $visits = $this->getVisitsBetweenDate();

        $carboys = $visits->map->carboys_movements->filter(function ($item) {
            return $item != null;
        });


        $returned_carboys = $this->getTotalReturnedCarboys($carboys);
        $borrowed_carboys = $this->getTotalBorrowedCarboys($carboys);

        $sales = $this->getSales($visits);

        $sum_sales = $sales->map(function ($item) {
            return $item->first();
        })->sum('total');


        return [
            'total_visits' => $visits->count(),
            'borrowed_carboys' => $borrowed_carboys,
            'returned_carboys' => $returned_carboys,
            'total_sales' => $sales->count(),
            'sum_sales' => 'Q.' . $sum_sales,
            'summary_by_product' => $this->getSummaryByProduct($sales),
            'summary_by_reason' => $this->getSummaryByReason($visits)
        ];


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


    private function getVisitsBetweenDate($start_date = null, $end_date = null)
    {

        if ($start_date === null) {
            $start_date = Carbon::today();
        }
        if ($end_date === null) {
            $end_date = Carbon::tomorrow();
        }

        return Visit::whereBetween('visited_date', [$start_date, $end_date])
            ->with('sales')
            ->with('reason')
            ->get();
    }

    private function getSales($visits)
    {

        return $visits->filter(function ($item) {
            return $item->sales !== null;
        })->map(function ($item) {
            return $item->sales;
        });
    }


    private function getSummaryByProduct($sales)
    {

        $ids_sales = count($sales) > 0 ? $sales->pluck('sales_id')->toArray() : [0];

        return Product::selectRaw(
            '*,(select sum(quantity) from sales_detail where sales_detail.product_id = product.product_id and sales_detail.sales_id in(?)) as total',
            $ids_sales
        )
            ->get();

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
