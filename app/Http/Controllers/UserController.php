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

        $initial_password = $this->repository->generateRandomPassword();
        $item['password'] = User::HashPass($initial_password);
        $item['access_token'] = '';

        $user = $this->repository->create($item);

        if ($user == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        Mail::to($user->email)->send(new ResetPassword($initial_password, $user));

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

    //aici - mecanism de autentificare resetare parola
    public function setNewPasword(Request $request)
    {
        $user = $request->user;

        $initial_password = $this->repository->generateRandomPassword();
        
        $item = array(
            'password' => User::HashPass($initial_password),
            'access_token' => ''
        );

        $this->repository->update($user, $item);

        if ($user == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        Mail::to($user->email)->send(new ResetPassword($initial_password, $user));

        return response()->json(['updated' => true], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();

        $user = $request->user;

        $pass = User::HashPass($item['password']);
        $this->repository->update($user, ['password' => $pass]);

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
