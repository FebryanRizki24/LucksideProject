<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Repositories\DashboardRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardRepo;

    public function __construct(DashboardRepository $dashboardRepo)
    {
        $this->dashboardRepo = $dashboardRepo;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');

        if ($user->hasRole('admin')) {
            $data = $this->dashboardRepo->getAdminDashboardData($filter);
        } elseif ($user->hasRole('user')) {
            $data = $this->dashboardRepo->getUserDataDashboard();
        } else {
            $data = $this->dashboardRepo->getBarbermanDashboardData($filter, $user->name);
        }

        return view('dashboard.index', compact('data', 'filter'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|uuid',
            'status' => 'required|in:waiting,in_service,done,late,cancelled',
        ]);

        $queue = $this->dashboardRepo->updateStatus($request->id, $request->status);

        if (!$queue) {
            return response()->json(['message' => 'Antrean tidak ditemukan!'], 404);
        }

        return response()->json(['message' => 'Status updated successfully', 'data' => $queue]);
    }
}
