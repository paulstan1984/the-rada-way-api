<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocationsRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Run;
use Illuminate\Http\JsonResponse;

class LocationsController extends Controller
{
    var $repository;
    var $paginationService;

    public function __construct(LocationsRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
        $this->paginationService->PageSize = 30;
    }

    public function search(Request $request, $page, $run_id): JsonResponse
    {
        $query = $this->repository->search($run_id);
        $query = $query->orderBy('id', 'asc');
        $pagination = $this->paginationService->applyPagination($query, $page);

        return response()->json($pagination, 200);
    }

    public function sync(Request $request, $run_id)
    {
        $validator = Validator::make($request->all(), [
            '*.operation' => ['required', Rule::in(['insert', 'delete'])],
            '*.id' => ['exclude_if:*.operation,insert', 'required', Rule::exists('locations', 'id')],
            '*.lat' => ['exclude_if:*.operation,delete', 'required', 'decimal:0,17'],
            '*.lng' => ['exclude_if:*.operation,delete', 'required', 'decimal:0,17'],
            '*.distance' => ['exclude_if:*.operation,delete', 'required', 'decimal:0,17'],
            '*.speed' => ['exclude_if:*.operation,delete', 'required', 'decimal:0,17'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        if (Run::find($run_id) == null) {
            return response()->json(['run_id', 'Incorect run.'], 400);
        }

        $operations = $validator->validated();
        foreach ($operations as $operation) {
            switch ($operation['operation']) {
                case 'delete':
                    $item = Run::find($operation['id']);
                    $this->repository->delete($item);
                    break;
                case 'insert':
                    $operation['run_id'] = $run_id;
                    $this->repository->create($operation);
                    break;
            }
        }

        Run::update_stats($operation['id']);

        return response()->json($operations, 200);
    }
}
