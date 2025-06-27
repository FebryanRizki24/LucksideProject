<?php

namespace App\Repositories;

use App\Models\Service;
use Yajra\DataTables\Facades\DataTables;

class ServiceRepository
{
    public function getDatatables()
    {
        $query = Service::query();

        return DataTables::of($query)->make(true);
    }

    public function create($data)
    {
        return Service::create($data);
    }

    public function update($id, $data)
    {
        $service = Service::findOrFail($id);
        $service->update($data);
        return $service;
    }

    public function delete($id)
    {
        $service = Service::findOrFail($id);
        return $service->delete();
    }

    public function findById($id)
    {
        return Service::findOrFail($id);
    }
}
