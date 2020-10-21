<?php

namespace App\Http\Controllers;

use App\Repository\CustomerRepository;
use App\Repository\SalesRepository;
use App\Repository\VisitRepository;
use App\VisitReason;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    //


    private $customerRepository;
    private $salesRepository;

    public function __construct(CustomerRepository $customerRepository, SalesRepository $salesRepository)
    {

        $this->customerRepository = $customerRepository;
        $this->salesRepository = $salesRepository;
    }


    public function store(Request $request)
    {


        $sales = $this->customerRepository->generate_sales($request);

        return response([
            'success' => true,
            'data' => $sales
        ]);
    }


    public function summary(Request $request)
    {

        $sales = $this->salesRepository->summary($request);

        return response([
            'success' => true,
            'data' => $sales
        ]);
    }

    public function show($id)
    {
        $sale = $this->salesRepository->findById($id);

        return response([
            'success' => true,
            'data' => $sale
        ]);
    }
}
