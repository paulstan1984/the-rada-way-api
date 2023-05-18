<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class PaginationService
{

    var $PageSize;

    function __construct()
    {
        $this->PageSize = (int)env('PAGE_SIZE', 20);
    }

    function applyPagination(Builder $query, $page)
    {

        $count = $query->count();

        return [
            'count'    => $count,
            'page'     => $page,
            'page_size' => $this->PageSize,
            'nr_pages' => ceil($count / $this->PageSize),
            'results'  => $query
                ->skip(($page - 1) * $this->PageSize)
                ->take($this->PageSize)
                ->get()
        ];
    }
}
