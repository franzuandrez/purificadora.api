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





        return response([
            'success' => true,
            'data' => $this
                ->visitRepository
                ->visit_by_reason(
                    $request
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
