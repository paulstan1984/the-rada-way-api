<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RunsRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

    public function sync(Request $request)
    {       
        $user_id = $request->user->id;
        $validator = Validator::make($request->all(), [
            '*.operation' => ['required', Rule::in(['insert', 'delete'])],
            '*.id' => ['exclude_if:*.operation,insert', 'required', Rule::exists('runs', 'id')],
            '*.startTime' => ['exclude_if:*.operation,delete', 'required', 'date'],
            '*.endTime' => ['exclude_if:*.operation,delete', 'required', 'date'],
            '*.distance' => ['exclude_if:*.operation,delete', 'required', 'decimal:0,2', 'min:0'],
            '*.avgSpeed' => ['exclude_if:*.operation,delete', 'required', 'decimal:0,2', 'min:0'],
            '*.locations' => ['exclude_if:*.operation,delete', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $operations = $validator->validated();
        foreach ($operations as $operation) {
            switch ($operation['operation']) {
                case 'delete':
                    $item = Run::find($operation['id']);
                    $this->repository->delete($item);
                    break;
                case 'insert':
                    $operation['user_id'] = $user_id;
                    $this->repository->create($operation);
                    break;
            }
        }

        return response()->json($operations, 200);
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
