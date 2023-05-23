<?php

namespace App\Services;

use App\Models\Run;

class RunsRepository
{

    public function create($item)
    {
        return Run::create($item);
    }

    public function search(string $keyword = null)
    {
        $query = Run::query();

        if (!empty($keyword)) {
            $query = $query->where('name', 'like', '%' . $keyword . '%');
        }
        return $query;
    }

    public function update(Run $item, $data)
    {
        return $item->update($data);
    }

    public function delete(Run $item)
    {
        $item->delete();
    }
}
