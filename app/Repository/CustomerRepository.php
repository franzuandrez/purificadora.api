<?php


namespace App\Repository;


use App\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerRepository
{


    public function all()
    {
        $customers = Customer::all();

        return $customers;
    }

    public function storeFromRequest(Request $request)
    {
        $customer = new Customer();
        $customer = $this->save($request, $customer);

        return $customer;
    }

    private function save(Request $request, Customer $customer)
    {
        $customer->name = $request->name;
        $customer->last_name = $request->last_name;
        $customer->nickname = $request->nickname;
        $customer->address = $request->address;
        $customer->latitude = $request->latitude;
        $customer->longitude = $request->longitude;
        $customer->last_date_visited = Carbon::now();
        $customer->save();
        return $customer;
    }

}
