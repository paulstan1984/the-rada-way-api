<?php

namespace App\Http\Controllers;

use App\Services\PaginationService;
use App\Services\ArticlesRepository;
use Illuminate\Http\JsonResponse;

class ArticlesController extends Controller
{
    var $repository;
    var $paginationService;

    public function __construct(ArticlesRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
        $this->paginationService->PageSize = 500;
    }

    public function search($category_id = null): JsonResponse
    {
        $query = $this->repository->search($category_id);
        $query = $query->orderBy('id', 'desc');
        $pagination = $this->paginationService->applyPagination($query, 1);

        return response()->json($pagination, 200);
    }

    public function categories(): JsonResponse
    {
        $categs = $this->repository->categories();
        
        return response()->json($categs, 200);
    }
}
