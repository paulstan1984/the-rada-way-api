<?php

namespace App\Http\Controllers;

use App\Services\PaginationService;
use App\Services\ArticlesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Article;

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
            if (strpos($article['imagelink'], 'https://') === false && strpos($article['imagelink'], 'http://') === false) {
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:articles|max:100',
            'description' => 'required|max:1000',
            'imagelink' => 'required|max:100',
            'link' => 'required|max:100',
            'category_id' => ['required', Rule::exists('categories', 'id')]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $this->repository->create($validator->validated());

        return response()->json($item, 200);
    }

    /**
     * Updates the specified item.
     *
     * @param  \App\Models\Article $article
     * @return \Illuminate\Http\Response
     */
    public function update(int $Id, Request $request)
    {
        $article = Article::find($Id);

        if ($article == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        $validator = Validator::make($request->all(), [
            'title' => [
                'required', 
                'max:100',
                Rule::unique('articles')
                    ->ignore($article->id)
            ],
            'description' => 'required|max:1000',
            'imagelink' => 'required|max:100',
            'link' => 'required|max:100',
            'category_id' => ['required', Rule::exists('categories', 'id')]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $this->repository->update($article, $validator->validated());

        return response()->json($article, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $Id)
    {
        $article = Article::find($Id);

        if ($article == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        $this->repository->delete($article);

        return response()->json(true, 200);
    }
}
