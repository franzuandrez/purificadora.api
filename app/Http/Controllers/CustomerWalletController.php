<?php

namespace App\Http\Controllers;

use App\Repository\CustomerWalletRepository;
use Illuminate\Http\Request;

class CustomerWalletController extends Controller
{
    //

    private $customerWalletRepository;

    public function __construct(CustomerWalletRepository $customerWalletRepository)
    {
        $this->customerWalletRepository = $customerWalletRepository;
    }


    public function index(Request $request)
    {


        return response([
            'success' => true,
            'data' => $this->customerWalletRepository->all(),
        ]);

    }
}
