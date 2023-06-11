<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RunsRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Run;
use Illuminate\Http\JsonResponse;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        return $this->search($request, 1, $request->user->id);
    }

    public function search(Request $request, $page = 1, $user_id) : JsonResponse
    {
        $query = $this->repository->search($user_id);
        $query = $query->orderBy('startTime', 'desc');
        $pagination = $this->paginationService->applyPagination($query, $page);

        return response()->json($pagination, 200);
    }

    public function sync(Request $request)
    {       
        $user_id = $request->user->id;
        $validator = Validator::make($request->all(), [
            '*.operation' => ['required', Rule::in(['insert', 'delete'])],
            '*.id' => ['exclude_if:*.operation,insert', 'required', Rule::exists('runs', 'id')],
            '*.startTime' => ['exclude_if:*.operation,delete', 'required', 'date'],
            '*.endTime' => ['exclude_if:*.operation,delete', 'required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $operations = $validator->validated();
        foreach ($operations as &$operation) {
            switch ($operation['operation']) {
                case 'delete':
                    $item = Run::find($operation['id']);
                    $this->repository->delete($item);
                    break;
                case 'insert':
                    $operation['user_id'] = $user_id;
                    $operation['distance'] = 0;
                    $operation['avgSpeed'] = 0;
                    $dbOperation = $this->repository->create($operation);
                    $operation = $dbOperation;
                    break;
            }
        }

        return response()->json($operations, 200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $Id) : JsonResponse
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $Id) : JsonResponse
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
