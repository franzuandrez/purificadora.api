<?php

namespace App\Http\Controllers;

use App\Repository\CustomerRepository;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //


    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $customer = $this
            ->customerRepository
            ->storeFromRequest($request);

        return response([
            'customer' => $customer
        ]);
    }
}
