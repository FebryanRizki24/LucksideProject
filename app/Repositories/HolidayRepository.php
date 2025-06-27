<?php

namespace App\Repositories;

use App\Models\Holiday;
use Yajra\DataTables\Facades\DataTables;

class HolidayRepository
{
    public function getDatatables()
    {
        $query = Holiday::query();

        return DataTables::of($query)->make(true);
    }

    public function create($data)
    {
        return Holiday::create($data);
    }

    public function update($id, $data)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->update($data);
        return $holiday;
    }

    public function delete($id)
    {
        $holiday = Holiday::findOrFail($id);
        return $holiday->delete();
    }

    public function findById($id)
    {
        return Holiday::findOrFail($id);
    }
}
