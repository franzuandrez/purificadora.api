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
            'data' => $this->customerWalletRepository->byUserAuthenticated(),
        ]);

    }

    public function wallets(Request  $request)
    {
        return response([
            'success' => true,
            'data' => $this->customerWalletRepository->all($request),
        ]);

    }

    public function store(Request $request)
    {

        $wallet = $this->customerWalletRepository->storeFromRequest($request);

        return response([
            'success' => true,
            'data' => $wallet,
        ]);
    }

    public function update(Request $request, $id)
    {

        $wallet = $this->customerWalletRepository->updateFromRequest($request, $id);
        return response([
            'success' => true,
            'data' => $wallet,
        ]);
    }

    public function show($id)
    {
        $wallet = $this->customerWalletRepository->findById($id);
        return response([
            'success' => true,
            'data' => $wallet,
        ]);
    }
}
