<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\UserRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;

class UserController extends Controller
{
    var $repository;
    var $paginationService;

    public function __construct(UserRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
    }

    public function health_check()
    {
        $users = User::count();
        return response()->json([
            'Status' => 'Ok',
            'NrUsers' => $users,
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100|unique:users',
            'name' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        $remember_token = $this->repository->generateRememberToken();

        $item['password'] = '';
        $item['access_token'] = '';
        $item['remember_token'] = $remember_token;

        $user = $this->repository->create($item);

        if ($user == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        Mail::to($user->email)->send(new ResetPassword($remember_token, $user));

        return response()->json(['created' => true], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        $token = $this->repository->login($item);

        if (empty($token)) {
            return response()->json(['password' => ['incorrect login']], 404);
        }

        return response()->json($token, 200);
    }

    public function logout(Request $request)
    {
        $token = $this->repository->logout($request->user->access_token);

        return response()->json($token, 200);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user, 200);
    }

    public function changePassword(Request $request)
    {
        if (empty($request->user)) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:100',
                'password' => 'required|max:10',
                'remember_token' => 'required|min:6|max:6'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'password' => 'required|max:10',
            ]);
        }

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        if (!empty($request->user)) {
            $user = $request->user;
        } else {
            $user = $this->repository->getUserByEmailAndRememberToken($item);
        }

        if ($user == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        $pass = User::HashPass($item['password']);
        $this->repository->update($user, ['password' => $pass, 'remember_token' => '']);

        return response()->json(['update_password' => true], 200);
    }

    public function sendResetPasswordToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        $user = $this->repository->getUserByEmail($item['email']);
        if ($user == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        $remember_token = $this->repository->generateRememberToken();
        $this->repository->update($user, ['remember_token' => $remember_token]);

        Mail::to($user->email)->send(new ResetPassword($remember_token, $user));

        return response()->json(['mail_sent' => true], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
