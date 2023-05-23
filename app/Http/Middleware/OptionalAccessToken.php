<?php

namespace App\Http\Middleware;

use App\Services\UserRepository;
use Closure;
use Illuminate\Http\Request;

class OptionalAccessToken
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
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if(!empty($token)){
            $user = $this->repository->getUserByToken($token);
            $request->merge(['user' => $user]);
        }
        
        return $next($request);
    }
}
