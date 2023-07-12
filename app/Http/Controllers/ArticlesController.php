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

        foreach ($pagination['results'] as $article) {
            if (strpos($article['imagelink'], 'https://') === false) {
                $article['imagelink'] = env('APP_URL') . $article['imagelink'];
            }
        }

        return response()->json($pagination, 200);
    }

    public function categories(): JsonResponse
    {
        $categs = $this->repository->categories();
        $categs = $categs->orderBy('position')->get();

        foreach ($categs as $categ) {
            $categ['imagelink'] = env('APP_URL') . $categ['imagelink'];
        }

        return response()->json($categs, 200);
    }
}
