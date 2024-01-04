<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RunsRepository;
use App\Services\UserRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Run;
use Illuminate\Http\JsonResponse;

class RunsController extends Controller
{
    var $repository;
    var $userRepository;
    var $paginationService;

    public function __construct(RunsRepository $repository, UserRepository $userRepository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->paginationService = $paginationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->search($request, 1, $request->user->id);
    }

    public function search(Request $request, $page = 1, $user_id): JsonResponse
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
            '*.operation' => ['required', Rule::in(['insert', 'delete', 'update'])],
            '*.id' => ['exclude_if:*.operation,insert', 'required', Rule::exists('runs', 'id')],
            '*.startTime' => ['exclude_if:*.operation,delete', 'required', 'date'],
            '*.endTime' => ['exclude_if:*.operation,delete', 'required', 'date'],
            '*.distance' => ['exclude_if:*.operation,delete', 'required_if:*.operation,update', 'decimal:0,17'],
            '*.avgSpeed' => ['exclude_if:*.operation,delete', 'required_if:*.operation,update', 'decimal:0,17'],
            '*.locations' => ['exclude_if:*.operation,delete', 'string', 'nullable'],
            '*.base64_encoded_images' => ['exclude_if:*.operation,delete', 'string', 'nullable'],
            '*.running' => ['exclude_if:*.operation,delete', 'required_if:*.operation,update'],
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
                    if (empty($operation['distance'])) {
                        $operation['distance'] = 0;
                    }
                    if (empty($operation['avgSpeed'])) {
                        $operation['avgSpeed'] = 0;
                    }
                    if (empty($operation['locations'])) {
                        $operation['locations'] = '';
                    }
                    if (empty($operation['base64_encoded_images'])) {
                        $operation['base64_encoded_images'] = '';
                    }
                    if (empty($operation['running'])) {
                        $operation['running'] = 1;
                    }

                    $dbOperation = $this->repository->create($operation);
                    $operation = $dbOperation;
                    break;
                case 'update':
                    $operation['user_id'] = $user_id;
                    $item = Run::find($operation['id']);
                    if (empty($operation['locations'])) {
                        $operation['locations'] = '';
                    }
                    if (empty($operation['base64_encoded_images'])) {
                        $operation['base64_encoded_images'] = '';
                    }
                    $this->repository->update($item, $operation);
                    $operation = $item;
                    break;
            }
        }

        $this->userRepository->updateUserStats($user_id);

        return response()->json($operations, 200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $Id): JsonResponse
    {
        $item = Run::find($Id);

        if ($item == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        return response()->json($item, 200);
    }

    /**
     * Updates the specified item.
     *
     * @param  \App\Models\Article $run
     * @return \Illuminate\Http\Response
     */
    public function update(int $Id, Request $request)
    {
        $run = Run::find($Id);

        if ($run == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        $validator = Validator::make($request->all(), [
            'locations' => []
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $validator = Validator::make($request->all(), [
            'locations' => ['string', 'nullable'],
            'base64_encoded_images' => ['string', 'nullable']
        ]);

        $validated_data = $validator->validated();
        if (array_key_exists('locations', $validated_data) && $validated_data['locations'] == null) {
            unset($validated_data['locations']);
        }
        if (array_key_exists('base64_encoded_images', $validated_data) && $validated_data['base64_encoded_images'] == null) {
            unset($validated_data['base64_encoded_images']);
        }

        $this->repository->update($run, $validated_data);

        return response()->json($run, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $Id): JsonResponse
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

    public function update_running(Request $request, int $run_id, int $running): JsonResponse
    {
        $item = Run::find($run_id);

        if ($item == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        if ($item->user_id != $request->user->id) {
            return response()->json(['error' => 'not found'], 400);
        }

        $this->repository->update($item, ['running' => $running]);
        $this->userRepository->update($request->user, ['running' => $running]);

        if ($running == 0) {
            $this->repository->update($item, ['endTime' => date('Y-m-d H:i:s')]);
        }

        return response()->json($item, 200);
    }
}
