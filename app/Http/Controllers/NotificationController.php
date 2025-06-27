<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\NotificationRepository;

class NotificationController extends Controller
{
    protected $notificationRepo;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->middleware('auth');
        $this->notificationRepo = $notificationRepo;
    }

    public function index()
    {
        $notifications = $this->notificationRepo->getAllByUser(auth()->id());
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $this->notificationRepo->markAsRead($id, auth()->id());
        return response()->json(['message' => 'Marked as read']);
    }

    public function destroy($id)
    {
        $this->notificationRepo->delete($id, auth()->id());
        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
