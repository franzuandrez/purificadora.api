<?php


namespace App\Repository;


use App\CarboyMovement;
use App\Customer;
use App\Inventory;
use Illuminate\Http\Request;

class InventoryRepository
{


    public function summary($request)
    {

        $movements = Inventory::with('visit_document')
            ->with('visit_document.customer')
            ->orderByDesc('movement_date')
            ->get();


        $returned = $movements->whereIn('type', ['R'])
            ->sum('quantity');
        $borrowed = $movements->whereIn('type', ['B'])
            ->sum('quantity');
        $local = $movements->whereIn('type', ['I'])
            ->sum('quantity');
        $with_customers = $borrowed - $returned;
        $in_store = $local + $returned - $borrowed;


        $customers = Customer::select('customer.*')
            ->selectRaw("(sum(if(type = 'B', 1, -1) * quantity)) as total_borrowed")
            ->join('carboys_movements', 'carboys_movements.customer_id', '=', 'customer.customer_id')
            ->groupBy('carboys_movements.customer_id')
            ->having('total_borrowed', '>', 0)
            ->orderByDesc('total_borrowed')
            ->get();


        return [
            'movements' => $movements->take(7),
            'with_customers' => $with_customers,
            'in_store' => ($in_store),
            'customers' => $customers
        ];


    }

}
