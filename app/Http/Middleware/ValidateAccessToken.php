<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\UserRepository;

class ValidateAccessToken
{
    var $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $token = $request->header('Authorization');
        if (empty($token)) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $user = $this->repository->getUserByToken($token);

        if (empty($user)) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        if (!empty($role)) {
            if ($user->role != $role) {
                return response()->json(['error' => 'unauthorized'], 401);
            }
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
