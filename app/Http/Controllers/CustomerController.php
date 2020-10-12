<?php

namespace App\Http\Controllers;

use App\Repository\CustomerRepository;
use App\Repository\VisitRepository;
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


    public function index()
    {
        return response([
            'success' => true,
            'customers' => $this
                ->customerRepository
                ->all()
        ]);
    }

    public function show($id)
    {
        return response([
            'success' => true,
            'data' => $this
                ->customerRepository
                ->getWithSalesInfo($id)
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        try {
            $customer = $this
                ->customerRepository
                ->storeFromRequest($request);

            if ($customer == null) {
                throw new \Exception("Error al intentar crear cliente");
            }

            return response([
                'success' => true,
                'customer' => $customer
            ]);
        } catch (\Exception $ex) {

            return response([
                'success' => false,
                'message' => 'Lo siento, su petición no ha podido ser procesada',
                'error' => $ex->getMessage()
            ], 500);
        }

    }
}
