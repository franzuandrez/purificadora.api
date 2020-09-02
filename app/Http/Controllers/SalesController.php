<?php

namespace App\Http\Controllers;

use App\Repository\CustomerRepository;
use App\Repository\VisitRepository;
use App\VisitReason;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    //


    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {

        $this->customerRepository = $customerRepository;
    }


    public function store(Request $request)
    {


        $sales = $this->customerRepository->generate_sales($request);

        return response([
            'success' => true,
            'data' => $sales
        ]);
    }
}
