<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;

class ArticlesRepository
{

    public function create($item)
    {
        return Article::create($item);
    }

    public function search(string $category_id = null)
    {
        $query = Article::query();

        if (!empty($category_id)) {
            $query = $query->where('category_id', $category_id);
        }
        return $query;
    }

    public function update(Article $item, $data)
    {
        return $item->update($data);
    }

    public function delete(Article $item)
    {
        $item->delete();
    }

    public function categories()
    {
        return Category::query();
    }
}
