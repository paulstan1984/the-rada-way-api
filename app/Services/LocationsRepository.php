<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Run;

class LocationsRepository
{

    public function create($item)
    {
        return Location::create($item);
    }

    public function search(string $run_id = null)
    {
        $query = Location::query();

        if (!empty($run_id)) {
            $query = $query->where('run_id', $run_id);
        }
        return $query;
    }

    public function update(Location $item, $data)
    {
        return $item->update($data);
    }

    public function delete(Location $item)
    {
        $item->delete();
    }

    public function update_run_stats($run_id)
    {
        Run::update_run_stats($run_id);
        return Run::find($run_id);
    }
}
