<?php


namespace App\Repository;


use App\Sales;
use App\Visit;
use Illuminate\Support\Collection;

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


    public function all()
    {

        return Sales::without('detail')
            ->with('visit')
            ->with('visit.employee')
            ->with('visit.customer')
            ->orderByDesc('sales_id')
            ->paginate(8);

    }

    public function findById($id)
    {
        return Sales::with('visit')
            ->with('visit.employee')
            ->with('visit.customer')
            ->findOrFail($id);
    }


}
