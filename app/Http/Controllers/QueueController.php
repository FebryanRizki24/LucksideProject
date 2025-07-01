<?php

namespace App\Http\Controllers;

use App\Models\Barberman;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    public function index()
    {
        return view('queue');
    }

    public function processDailyQueue()
    {
        $today = Carbon::now()->format('Y-m-d');

        DB::beginTransaction();

        try {
            // Hapus antrean yang bukan hari ini
            $queuesToDelete = Queue::whereHas('details.booking', function ($query) use ($today) {
                $query->whereDate('date', '!=', $today);
            })->get();

            foreach ($queuesToDelete as $queue) {
                $queue->delete();
            }

            // Ambil semua booking terkonfirmasi hari ini, dengan semua details-nya
            $bookings = Booking::with('details')
                ->where('status', 'confirmed')
                ->whereDate('date', $today)
                ->get();

            $counter = 1; // untuk nomor antrean

            foreach ($bookings as $booking) {
                foreach ($booking->details as $detail) {
                    Queue::updateOrCreate(
                        [
                            'booking_detail_id' => $detail->id,
                        ],
                        [
                            // 'booking_id' => $booking->id,
                            'booking_detail_id' => $detail->id,
                            'barberman_id' => $detail->barberman_id,
                            'customer_name' => $detail->customer_name,
                            'antrean' => $counter++,
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Antrean berhasil diproses'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses antrean: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkMissingQueue()
    {
        $today = Carbon::now()->format('Y-m-d');

        // Semua detail dari booking yang confirmed dan tanggal hari ini
        $detailIdsToday = BookingDetail::whereHas('booking', function ($query) use ($today) {
            $query->where('status', 'confirmed')
                ->whereDate('date', $today);
        })->pluck('id')->toArray();

        // Semua antrean hari ini berdasarkan detail_id (pastikan kamu punya kolom detail_id di queue)
        $queuesDetailIdsToday = Queue::pluck('booking_detail_id')->toArray();

        // Detail yang belum ada antreannya
        $missingDetailIds = array_diff($detailIdsToday, $queuesDetailIdsToday);

        return response()->json([
            'has_missing' => !empty($missingDetailIds)
        ]);
    }

    public function getLiveQueue()
    {
        $barbermen = Barberman::with(['queues' => function ($query) {
            $query->select('queues.id', 'queues.booking_detail_id', 'queues.customer_name', 'queues.status', 'queues.antrean')
                ->orderBy('queues.antrean');
        }])->get(['id', 'name']);

        $result = $barbermen->map(function ($barberman) {
            $queues = $barberman->queues;

            $current = $queues->firstWhere('status', 'in_service');
            $upcoming = $queues->where('status', 'wait')->sortBy('antrean')->values();
            $done = $queues->where('status', 'done')->values();

            return [
                'barberman_name' => $barberman->name,
                'antrean_saat_ini' => $current ? [
                    'customer_name' => $current->customer_name,
                    'status' => $current->status,
                    'antrean' => $current->antrean,
                ] : null,
                'antrean_akan_datang' => $upcoming->map(function ($q) {
                    return [
                        'customer_name' => $q->customer_name,
                        'status' => $q->status,
                        'antrean' => $q->antrean,
                    ];
                }),
                'antrean_selesai' => $done->map(function ($q) {
                    return [
                        'customer_name' => $q->customer_name,
                        'status' => $q->status,
                        'antrean' => $q->antrean,
                    ];
                }),
                'jumlah_antrean' => $queues->count(),
            ];
        });

        return response()->json($result);
    }
}
