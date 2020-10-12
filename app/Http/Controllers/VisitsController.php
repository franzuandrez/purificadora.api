<?php

namespace App\Http\Controllers;

use App\Repository\VisitRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitsController extends Controller
{
    //

    protected $visitRepository;

    public function __construct(VisitRepository $visitRepository)
    {
        $this->visitRepository = $visitRepository;
    }

    public function index(Request $request)
    {

        return response([
            'success' => true,
            'data' => $this->visitRepository->all()
        ]);


    }

    public function store(Request $request)
    {


        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $reason_id = $request->get('reason_id');
        $customer_id = $request->get('customer_id');


        return response([
            'success' => true,
            'data' => $this
                ->visitRepository
                ->visit_by_reason(
                    $latitude,
                    $longitude,
                    $reason_id,
                    $customer_id
                )
        ]);
    }

    public function show($id)
    {
        return response([
            'success' => true,
            'data' => $this->visitRepository->findById($id)
        ]);

    }


}
