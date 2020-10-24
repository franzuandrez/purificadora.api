<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Debts;
use App\Payment;
use App\Repository\VisitRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DebtsController extends Controller
{
    //


    private $visitRepository;

    public function __construct(VisitRepository $visitRepository)
    {
        $this->visitRepository = $visitRepository;

    }

    public function show($id)
    {


        $debts = Debts::select('debts.*', \DB::raw('convert(quantity,SIGNED) as quantity '),
            \DB::raw('(select CONVERT(ifnull(sum(quantity),0),SIGNED) from payments where debt_id = debts.id) as paid_out '))
            ->where('customer_id', $id)
            ->where('status', 'pendiente')
            ->get();

        return response([
            'success' => true,
            'data' => $debts
        ]);

    }

    public function pay(Request $request)
    {


        $payment = $this->visitRepository->pay($request);


        return response([
            'success' => true,
            'data' => $payment
        ]);


    }


    public function summary()
    {

        $debts = Debts:: without('visit')
            ->with('customer')
            ->select('debts.*', \DB::raw('convert(quantity,SIGNED) as quantity '),
                \DB::raw('(select CONVERT(ifnull(sum(quantity),0),SIGNED) from payments where debt_id = debts.id) as paid_out '))
            ->where('status', 'pendiente')
            ->get();

        $total = $debts->sum(function ($item) {
            return ($item->quantity - $item->paid_out) * $item->price;
        });

        $customers = $debts
            ->groupBy('customer_id')
            ->map(function ($item) {
                return (object) [
                    'customer' => $item->first()->customer,
                    'total' => $item->sum(function ($item) {
                        return ($item->quantity - $item->paid_out) * $item->price;
                    })
                ];
            });



        return response([
            'success' => true,
            'data' => [
                'debts' => $debts,
                'total' => $total,
                'customers' => $customers
            ]
        ]);

    }
}
