<?php


namespace App\Repository;


use App\Employee;
use Illuminate\Support\Facades\Auth;

class CustomerWalletRepository
{


    public function all()
    {

        $employee = Employee::where('user_id', Auth::id())
            ->with('wallets')
            ->first();
        $wallets = $employee->wallets;

        return $wallets;
    }
}
