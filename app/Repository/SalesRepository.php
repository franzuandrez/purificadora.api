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


}
