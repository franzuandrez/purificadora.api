<?php

namespace App\Http\Controllers;

use App\Repository\SummaryRepository;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    //

    protected $summaryRepository;

    public function __construct(SummaryRepository $summaryRepository)
    {
        $this->summaryRepository = $summaryRepository;
    }

    public function index()
    {


        return response([
            'success' => true,
            'data' => $this->summaryRepository->index()
        ]);

    }
}
