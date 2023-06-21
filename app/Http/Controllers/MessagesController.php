<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessagesRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Message;
use Illuminate\Http\JsonResponse;

class MessagesController extends Controller
{
    var $repository;
    var $paginationService;

    public function __construct(MessagesRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
    }

    public function search(Request $request, $page = 1, $friend_id): JsonResponse
    {
        $user_id = $request->user->id;
        $query = $this->repository->search($user_id, $friend_id);
        $query = $query->orderBy('created_at', 'desc');
        $pagination = $this->paginationService->applyPagination($query, $page);

        return response()->json($pagination, 200);
    }

    public function getMessages(Request $request, $friend_id, $type = 'older', $lastId = null): JsonResponse
    {
        $user_id = $request->user->id;
        $query = $this->repository->getMessages($user_id, $friend_id, $type, $lastId);

        if ($type == 'newer') {
            $query = $query->orderBy('id', 'asc');
        }

        if ($type == 'older') {
            $query = $query->orderBy('id', 'desc');
        }

        $pagination = $this->paginationService->getPagePagination($query);

        return response()->json($pagination, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user;

        $validator = Validator::make($request->all(), [
            'text' => 'required|max:1000',
            'receiver_id' => ['required', Rule::exists('users', 'id')],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $validator->validated();
        $item['sender_id'] = $user->id;

        $dbItem = $this->repository->create($item);

        return response()->json(Message::find($dbItem['id']), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $Id): JsonResponse
    {
        $user_id = $request->user->id;
        $item = Message::find($Id);

        if ($item == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        if ($item['receiver_id'] != $user_id && $item['sender_id'] != $user_id) {
            return response()->json(['error' => 'not found'], 400);
        }

        $this->repository->delete($item);

        return response()->json(true, 200);
    }
}
