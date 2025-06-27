<?php

namespace App\Repositories\Dashboard;

use App\Models\Barberman;
use App\Models\BarbermanSchedule;
use Yajra\DataTables\Facades\DataTables;

class BarbermanScheduleRepository
{
    public function getDatatables()
    {
        $query = BarbermanSchedule::query()->join('barbermans', 'barberman_schedules.barberman_id', '=', 'barbermans.id')
        ->select([
            'barberman_schedules.*',
            'barbermans.name as barberman_name'
        ]);;

        return DataTables::of($query)
            ->addColumn('barberman_name', function ($schedule) {
                return $schedule->barberman->name ?? '-';
            })
            ->make(true);
    }

    public function getBarberman()
    {
        return Barberman::select('id', 'name')->where('status', 1)->get();
    }

    public function create($data)
    {
        return BarbermanSchedule::create($data);
    }

    public function update($id, $data)
    {
        $schedule = BarbermanSchedule::findOrFail($id);
        $schedule->update($data);
        return $schedule;
    }

    public function delete($id)
    {
        $schedule = BarbermanSchedule::findOrFail($id);
        return $schedule->delete();
    }

    public function findById($id)
    {
        return BarbermanSchedule::findOrFail($id);
    }

    public function checkScheduleConflict($barbermanId, $startTime, $endTime, $excludeId = null)
    {
        return BarbermanSchedule::where('barberman_id', $barbermanId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime]);
                })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->exists();
    }
}
