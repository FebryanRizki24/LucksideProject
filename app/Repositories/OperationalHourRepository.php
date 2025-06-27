<?php

namespace App\Repositories;

use App\Models\OperationalHour;
use Yajra\DataTables\Facades\DataTables;

class OperationalHourRepository
{
    public function getDatatables()
    {
        $query = OperationalHour::query();

        return DataTables::of($query)
        ->make(true);
    }

    public function create($data)
    {
        return OperationalHour::create($data);
    }

    public function update($id, $data)
    {
        $operationalHour = OperationalHour::findOrFail($id);
        $operationalHour->update($data);
        return $operationalHour;
    }

    public function delete($id)
    {
        $operationalHour = OperationalHour::findOrFail($id);
        return $operationalHour->delete();
    }

    public function findById($id)
    {
        return OperationalHour::findOrFail($id);
    }
}