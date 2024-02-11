<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\UserRepository;
use App\Services\RunsRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use App\Services\MessagesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    var $repository;
    var $runsRepository;
    var $messageRepository;
    var $paginationService;

    public function __construct(
        UserRepository $repository,
        RunsRepository $runsRepository,
        MessagesRepository $messageRepository,
        PaginationService $paginationService
    ) {
        $this->repository = $repository;
        $this->runsRepository = $runsRepository;
        $this->messageRepository = $messageRepository;
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
            return response()->json(['password' => ['Parolă greșită.']], 404);
        }

        return response()->json($token, 200);
    }

    public function logout(Request $request)
    {
        $response = $this->repository->logout($request->user->access_token);

        return response()->json(['response' => $response], 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user;
        $user->runs = $this
            ->runsRepository
            ->search($user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($user, 200);
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
            $user = User::find($request->user->id);
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


    public function search(Request $request, $page = 1, $keyword = null): JsonResponse
    {
        $user_id = $request->user->id;

        $my_last_messages = null;
        if (!empty($user_id)) {
            $my_last_messages = $this->messageRepository
                ->search_my_last_messages($user_id);
        }

        $query = $this->repository->search($keyword, $my_last_messages);
        $query = $query->where('users.id', '<>', $request->user->id);

        if (!empty($user_id)) {
            $query = $query->orderByRaw('IFNULL(last_message_read, 1)');
            $query = $query->orderBy('last_message_date', 'desc');
        }
        else {
            $query = $query->orderBy('name', 'asc');
            $query = $query->distinct();
        }

        $pagination = $this->paginationService->applyPagination($query, $page);

        foreach($pagination['results'] as $user) {
            $this->repository->updateUserStats($user->id);
        }

        return response()->json($pagination, 200);
    }

    public function count_unread_messages(Request $request): JsonResponse
    {
        $user_id = $request->user->id;

        $count = $this->messageRepository
            ->count_unread_messages($user_id);

        return response()->json($count, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json('Ok', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json('Ok', 200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): JsonResponse
    {
        $user = User::find($id);

        if ($user == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        $user->runs = $this->runsRepository->search($user->id)->get();

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $user = $request->user;

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'sex' => ['required', Rule::in(['M', 'F', 'N'])],
            'dob' => ['required', 'date'],
            'weight' => ['required', 'integer'],
            'height' => ['required', 'integer'],
            'runGoal' => ['required', 'integer'],
            'base64_encoded_image' => ['string', 'nullable']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        $this->repository->update($user, $item);

        return response()->json($user, 200);
    }

    public function update_running(Request $request, int $running): JsonResponse
    {
        $user = $request->user;

        $this->repository->update($user, ['running' => $running]);

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user): JsonResponse
    {
        return response()->json('Ok', 200);
    }
}
