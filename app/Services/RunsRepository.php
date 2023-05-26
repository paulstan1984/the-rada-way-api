<?php

namespace App\Services;

use App\Models\Run;

class RunsRepository
{

    public function create($item)
    {
        return Run::create($item);
    }

    public function search(string $user_id = null)
    {
        $query = Run::query();

        if (!empty($user_id)) {
            $query = $query->where('user_id', $user_id);
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
