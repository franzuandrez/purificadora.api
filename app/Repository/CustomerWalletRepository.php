<?php


namespace App\Repository;


use App\Customer;
use App\CustomerWallet;
use App\Employee;
use  Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CustomerWalletRepository
{


    public function byUserAuthenticated()
    {

        $employee = Employee::where('user_id', Auth::id())
            ->with('wallets')
            ->first();
        $wallets = $employee->wallets;

        return $wallets;
    }

    public function all(Request $request)
    {
        $search = $request->get('search');

        return CustomerWallet::where(function ($query) use ($search) {
            return $query->whereHas('employees', function (Builder $q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhere('wallet', 'LIKE', '%' . $search . '%');
        })
            ->with('employees')->get();
    }

    public function storeFromRequest(Request $request)
    {
        $wallet = new CustomerWallet();
        $wallet = $this->save($request, $wallet);

        return $wallet;
    }

    public function updateFromRequest(Request $request, $id)
    {
        $wallet = $this->findById($id);
        $wallet = $this->save($request, $wallet);

        return $wallet;
    }

    private function save(Request $request, CustomerWallet $wallet)
    {
        $customers = $request->customers ?? [];
        $employees = $request->employees ?? [];

        Customer::whereIn('customer_id', $customers)
            ->update(['status' => 2]);
        $wallet->customers()->sync($customers);
        $wallet->employees()->sync($employees);
        $wallet->wallet = $request->wallet;
        $wallet->save();

        return $wallet->fresh();
    }

    public function findById($id)
    {
        return CustomerWallet::with('customers')
            ->with('employees')
            ->findOrFail($id);
    }

    public function associate($request)
    {
        $wallet = $this->findById($request->wallet_id);
        $customer = Customer::find($request->customer_id);
        $wallet->customers()->attach($customer);
        $customer->status = 1;
        $customer->save();
        return $wallet->refresh();
    }
}
