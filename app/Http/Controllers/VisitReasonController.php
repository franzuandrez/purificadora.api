<?php

namespace App\Http\Controllers;

use App\Repository\ReasonRepository;
use Illuminate\Http\Request;

class VisitReasonController extends Controller
{
    //


    private $reasonRepository;

    public function __construct(ReasonRepository $reasonRepository)
    {
        $this->reasonRepository = $reasonRepository;
    }


    public function index()
    {

        return response([
            'success' => true,
            'data' => $this->reasonRepository->all()
        ]);
    }

    public function web(Request $request)
    {

        return response([
            'success' => true,
            'data' => $this->reasonRepository->web($request)
        ]);
    }

    public function store(Request $request)
    {


        if ($request->id == "") {
            $visitReason = $this->reasonRepository->storeFromRequest($request);
        } else {
            $visitReason = $this->reasonRepository->updateFromRequest($request, $request->id);
        }


        return response([
            'success' => true,
            'data' => $visitReason
        ]);

    }

    public function update(Request $request, $id)
    {

        $visitReason = $this->reasonRepository->updateFromRequest($request, $id);
        return response([
            'success' => true,
            'data' => $visitReason
        ]);
    }

    public function show($id)
    {

        $visitReason = $this->reasonRepository->findById($id);
        return response([
            'success' => true,
            'data' => $visitReason
        ]);
    }
}
