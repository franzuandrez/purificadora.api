<?php

namespace App\Http\Controllers;

use App\Repository\InventoryRepository;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    //


    private $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;

    }



    public function index(Request $request)
    {


        return response([
            'success' => true,
            'data' => $this
                ->inventoryRepository
                ->summary($request)
        ]);
    }


}
