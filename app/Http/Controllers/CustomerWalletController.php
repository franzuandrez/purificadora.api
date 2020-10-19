<?php

namespace App\Http\Controllers;

use App\Customer;
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

    public function wallets(Request $request)
    {
        return response([
            'success' => true,
            'data' => $this->customerWalletRepository->all($request),
        ]);

    }

    public function store(Request $request)
    {

        if ($request->id === null) {
            $wallet = $this->customerWalletRepository->storeFromRequest($request);
        } else {
            $wallet = $this->customerWalletRepository->updateFromRequest($request, $request->id);
        }


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

    public function associate(Request $request)

    {
        $wallet = $this->customerWalletRepository->associate($request);

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
