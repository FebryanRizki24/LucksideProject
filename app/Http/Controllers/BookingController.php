<?php

namespace App\Http\Controllers;

use App\Models\Barberman;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\FaceShape;
use App\Models\Hairstyle;
use App\Models\HairstyleFaceShape;
use App\Models\Holiday;
use App\Models\OperationalHour;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    protected $bookingRepo;

    public function __construct(BookingRepository $bookingRepo)
    {
        $this->bookingRepo = $bookingRepo;
    }

    public function index()
    {
        $barbermans = Barberman::where('status', true)->get();
        $faceshapes = FaceShape::all();
        $hairstyles = Hairstyle::all();
        return view('booking', compact('barbermans', 'hairstyles', 'faceshapes'));
    }

    public function getSchedule(Request $request)
    {
        $barberman_id = $request->barberman_id;
        $date = $request->date;

        $result = $this->bookingRepo->getAvailableSlots($barberman_id, $date);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }

        return response()->json([
            'available_slots' => $result,
        ]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'time' => 'required',
            'barberman_id' => 'required',
            'hairstyle_id' => 'required',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $isHoliday = Holiday::where('date', $request->date)->exists();

        if ($isHoliday) {
            return response()->json([
                'message' => 'Tanggal yang Anda pilih merupakan hari libur. Silakan pilih tanggal lain.'
            ], 422);
        }

        $existingBooking = BookingDetail::where('user_id', $userId)
            ->whereHas('booking', function ($query) {
                $query->where('status', 'pending')
                    ->where('date', '>=', now()->format('Y-m-d'));
            })->exists();

        if ($existingBooking) {
            return response()->json(['message' => 'Anda masih memiliki booking yang belum selesai!'], 422);
        }

        $booking = $this->bookingRepo->create($request->all());

        return response()->json([
            'message' => 'Booking sukses!',
            'snap_token' => $booking->snap_token
        ]);
    }

    public function callback(Request $request)
    {
        try {
            Log::info('Midtrans Callback Request:', $request->all());

            $this->bookingRepo->handleCallback($request);

            Log::info('Midtrans Callback processed successfully.');

            return response()->json(['message' => 'Callback handled'], 200);
        } catch (\Throwable $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json(['error' => 'Callback failed'], 500);
        }
    }


    public function getByFaceShape(Request $request)
    {
        try {
            $request->validate([
                'face_shape_id' => 'required|exists:face_shapes,id',
            ]);

            // Ambil face shape lalu ambil hairstyle-nya lewat relasi many-to-many
            $faceShape = FaceShape::with('hairstyles')->findOrFail($request->face_shape_id);

            return response()->json([
                'hairstyles' => $faceShape->hairstyles
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan server'], 500);
        }
    }

    // public function getSnapToken($id)
    // {
    //     try {
    //         $booking = Booking::with('user')->findOrFail($id);

    //         // Panggil repository untuk dapatkan token
    //         $snapToken = $this->bookingRepo->generateSnapToken($booking);

    //         return response()->json([
    //             'snap_token' => $snapToken
    //         ]);
    //     } catch (\Exception $e) {
    //         \Log::error('Gagal ambil Snap Token: ' . $e->getMessage());
    //         return response()->json(['message' => 'Gagal mengambil token pembayaran.'], 500);
    //     }
    // }

    public function getSnapToken($id)
    {
        $booking = Booking::findOrFail($id);

        if (!$booking->snap_token) {
            $snapToken = $this->generateSnapToken($booking);
            $booking->update(['snap_token' => $snapToken]);
        }

        return response()->json(['snap_token' => $booking->snap_token]);
    }

    public function checkDate(Request $request)
    {
        $date = Carbon::parse($request->date)->format('Y-m-d');

        $isHoliday = Holiday::where('date', $date)->exists();

        if ($isHoliday) {
            return response()->json(['message' => 'Tanggal yang Anda pilih merupakan hari libur.']);
        }

        return response()->json(['message' => null]);
    }
}
