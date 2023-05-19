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
            'NewProp' => true,
        ], 200);
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
        $validator = Validator::make($request->all(), [
            'token' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        $token = $this->repository->logout($item['token']);

        return response()->json($token, 200);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user, 200);
    }

    public function getForgotPasswordToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        $user = $this->repository->getUserByEmail($item['email']);

        if (empty($user)) {
            return response()->json(['email' => ['not found']], 400);
        }

        $user->remember_token = md5(date('Y-m-d H:i:s'));
        $this->repository->update($user, ['remember_token' => $user->remember_token]);

        //to do: send the email notification
        Mail::to($user->email)->send(new ResetPassword(env('ADMINAPP_URL') . '/reset-password/' . $user->remember_token));

        return response()->json(['remember_token' => $user->remember_token], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remember_token' => 'required',
            'password' => 'required|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        $user = $this->repository->getUserByRememberToken($item['remember_token']);

        if (empty($user)) {
            return response()->json(['error' => 'not found'], 400);
        }

        $pass = User::HashPass($item['password']);
        $this->repository->update($user, ['password' => $pass, 'remember_token' => '']);

        return response()->json(['update_password' => true], 200);
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
