<?php

namespace App\Http\Controllers;

use App\Repository\SummaryRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    //

    protected $summaryRepository;
    protected $userRepository;

    public function __construct(SummaryRepository $summaryRepository, UserRepository $userRepository)
    {
        $this->summaryRepository = $summaryRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request  $request)
    {


        return response([
            'success' => true,
            'data' => [
                'summary' => $this->summaryRepository->index($request),
                'users' => $this->userRepository->all()
            ]
        ]);

    }
}
