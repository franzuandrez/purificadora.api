<?php

namespace App\Http\Controllers;


use App\Repository\EmployeeRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\User;

class UsersController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function index()
    {

        return response([
            'success' => true,
            'data' => $this->userRepository->all()
        ]);
    }

    //
    public function login()
    {
        if (Auth::attempt(['username' => request('username'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $success,
                'user' => $user
            ]);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Invalid Username or Password',
            ], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'password' => 'required',
            'name' => 'required',
            'last_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $user = $this->userRepository->storeFromRequest($request);
        $success['token'] = $user->createToken('appToken')->accessToken;

        return response()->json([
                'success' => true,
                'token' => $success,
                'user' => $user->refresh()
            ]
        );
    }

    public function logout(Request $res)
    {
        if (Auth::user()) {
            $user = Auth::user()->token();
            $user->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logout successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to Logout'
            ]);
        }
    }

    public function show($id)
    {
        $user = $this->userRepository->findById( $id);

        return response([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {


        $user = $this->userRepository->updateFromRequest($request, $id);

        return response([
            'success' => true,
            'data' => $user
        ]);
    }
}
