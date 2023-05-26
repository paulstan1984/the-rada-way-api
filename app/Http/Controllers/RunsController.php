<?php

namespace App\Http\Controllers;

use App\Models\PhisicalResource;
use Illuminate\Http\Request;
use App\Services\RunsRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Run;

class RunsController extends Controller
{
    var $repository;
    var $paginationService;

    public function __construct(RunsRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->search($request, 1, $request->user->id);
    }

    public function search(Request $request, $page = 1, $user_id)
    {
        $query = $this->repository->search($user_id);
        $query = $query->orderBy('startTime', 'desc');
        $pagination = $this->paginationService->applyPagination($query, $page);

        return $pagination;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PhisicalResource  $phisicalResource
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $Id)
    {
        $item = Run::find($Id);

        if ($item == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        return response()->json($item, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PhisicalResource  $phisicalResource
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $Id)
    {
        $item = Run::find($Id);

        if ($item == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        if ($item->user_id != $request->user->id) {
            return response()->json(['error' => 'not found'], 400);
        }

        $this->repository->delete($item);

        return response()->json(true, 200);
    }
}
