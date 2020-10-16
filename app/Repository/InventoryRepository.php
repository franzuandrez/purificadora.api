<?php


namespace App\Repository;


use App\CarboyMovement;
use App\Inventory;
use Illuminate\Http\Request;

class InventoryRepository
{


    public function summary($request)
    {

        $movements = Inventory::with('visit_document')
            ->with('visit_document.customer')
            ->orderByDesc('id')
            ->get();


        $returned = $movements->whereIn('type', ['R'])
            ->sum('quantity');
        $borrowed = $movements->whereIn('type', ['B'])
            ->sum('quantity');
        $local = $movements->whereIn('type', ['I'])
            ->sum('quantity');
        $with_customers = $borrowed - $returned;
        $in_store = $local + $returned - $borrowed;


        return [
            'movements' => $movements,
            'with_customers' => $with_customers,
            'in_store' => ($in_store)
        ];


    }

}
