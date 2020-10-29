<?php

namespace App\Http\Controllers;

use App\UpcomingVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UpcomingVisitsController extends Controller
{
    //


    public function index(Request $request)
    {

        $day = $request->get('day');
        if ($day == null) {
            $day = Carbon::today();
        } else {
            $day = Carbon::createFromFormat('Y-m-d', $day);
        }


        $upcoming_visits = UpcomingVisit::select(
            'customer.*',
            'upcoming_visits.next_visit_date',
            \DB::raw('if(date_format(last_date_visited,"%Y-%m-%d")>=next_visit_date,1,0) as is_visited')
        )
            ->join('customer', 'customer.customer_id', '=', 'upcoming_visits.customer_id')
            ->where('next_visit_date', $day->format('Y-m-d'))
            ->groupBy('upcoming_visits.customer_id')
            ->get();

        return response([
            'success' => true,
            'data' => $upcoming_visits
        ]);

    }
}
