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
        return $employee->wallets->map(function ($wallet) {
            return [
                "wallet" => $wallet->wallet,
                "customers" => ($wallet->customers->map(function ($customer) {
                    $nextVisit = $customer->hasToVisitToday;
                    $hasToVisitToday = count($nextVisit) > 0;
                    $hasBeenVisitedToday = count($customer->hasBeenVisitedToday) > 0;
                    if ($hasToVisitToday) {
                        if ($hasBeenVisitedToday) {
                            $color = '#2bce95';
                            $status = 'Visitado';
                            $order = 1;
                        } else {
                            $color = '#ffed4a';
                            $status = 'Por Visitar';
                            $order = 2;
                        }
                    } else {
                        if ($hasBeenVisitedToday) {
                            $color = '#2bce95';
                            $status = 'Visitado';
                            $order = 1;
                        } else {
                            $color = '#ffffff';
                            $status = '';
                            $order = 0;
                        }
                    }
                    return [
                        'customer_id' => $customer->customer_id,
                        'name' => $customer->name ?? '',
                        'last_name' => $customer->last_name ?? '',
                        'nickname' => $customer->nickname ?? '',
                        'address' => $customer->address,
                        'latitude' => $customer->latitude,
                        'longitude' => $customer->longitude,
                        'color' => $color,
                        'status' => $status,
                        'order' => $order
                    ];
                }))
                    ->sortByDesc('order')
                    ->values()
                    ->all()
            ];
        });
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
        $wallet->customers()->update(['status' => 2]);
        Customer::whereIn('customer_id', $customers)
            ->update(['status' => 1]);
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
