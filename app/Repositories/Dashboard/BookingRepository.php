<?php

namespace App\Repositories\Dashboard;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Queue;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BookingRepository
{
    public function getDatatables(Request $request)
    {
        $authName = Auth::user()->name;

        $query = Booking::query()
            ->leftjoin('payments', 'bookings.id', '=', 'payments.booking_id')
            ->with(['payment', 'reviews', 'details.hairstyle', 'details.barberman', 'details.service'])
            ->select('bookings.*', 'payments.status as payment_status');

        // Jika role user, hanya ambil detail yang sesuai user login
        if (Auth::user()->hasRole('user')) {
            $query->whereHas('details', function ($q) {
                $q->where('user_id', Auth::id());
            });
        } elseif (Auth::user()->hasRole('barberman')) {
            $query->whereHas('details', function ($q) use ($authName) {
                $q->where('name', $authName);
            });
        }

        // Filter status online / offline berdasarkan booking_details.user_id
        if ($request->status == 1) {
            $query->whereHas('details', function ($q) {
                $q->whereNotNull('user_id');
            });
        } elseif ($request->status == 2) {
            $query->whereDoesntHave('details', function ($q) {
                $q->whereNotNull('user_id');
            });
        }

        // Filter berdasarkan waktu
        if ($request->filled('filter_time')) {
            $today = now()->toDateString();
            if ($request->filter_time == 'today') {
                $query->whereDate('bookings.date', $today);
            } elseif ($request->filter_time == 'this_week') {
                $query->whereBetween('bookings.date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->filter_time == 'this_month') {
                $query->whereMonth('bookings.date', now()->month)
                    ->whereYear('bookings.date', now()->year);
            }
        }

        // Filter berdasarkan barberman dari booking_details
        if ($request->filled('barberman')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('barberman_id', $request->barberman);
            });
        }

        // Filter status booking
        if ($request->filled('booking_status')) {
            $query->where('bookings.status', $request->booking_status);
        }

        // Filter status pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payments.status', $request->payment_status);
        }

        return DataTables::of($query)
            ->editColumn('kode_booking', fn($row) => $row->kode)
            ->editColumn('date', fn($row) => $row->date)
            ->addColumn('time', fn($row) => optional($row->details->first())->time ?? '-')
            ->addColumn('customer_name', fn($row) => optional($row->details->first())->customer_name ?? '-')
            ->addColumn(
                'total_bayar',
                fn($row) =>
                $row->payment ? 'Rp ' . number_format($row->payment->gross_amount, 0, ',', '.') : 'Rp 0'
            )
            ->addColumn('status_bayar', function ($row) {
                $status = $row->payment_status ?? '-';
                $color = $status === 'paid' ? 'bg-green-500' : ($status === 'pending' ? 'bg-yellow-500' : 'bg-red-500');
                return '<span class="px-2 py-1 text-white rounded ' . $color . '">' . $status . '</span>';
            })
            ->addColumn('status_booking', function ($row) {
                $status = $row->status;
                $color = match ($status) {
                    'confirmed' => 'bg-green-500',
                    'completed' => 'bg-blue-500',
                    'pending'   => 'bg-yellow-500',
                    'cancelled' => 'bg-red-500',
                    default     => 'bg-gray-500',
                };
                return '<span class="px-2 py-1 text-white rounded ' . $color . '">' . $status . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn-detail px-2 py-1 bg-blue-500 text-white rounded" data-id="' . $row->id . '"> Detail </button>';
            })
            ->rawColumns(['action', 'status_booking', 'status_bayar'])
            ->make(true);
    }

    public function findById($id)
    {
        return Booking::with(['payment', 'reviews', 'details.barberman', 'details.hairstyle'])->findOrFail($id);
    }

    public function create(array $dataList)
    {
        if (empty($dataList)) {
            return [];
        }

        $kode = Booking::generateKodeBookingOffline($dataList[0]['date']);

        // Buat satu booking utama
        $booking = Booking::create([
            'kode' => $kode,
            'date' => $dataList[0]['date'],
            'status' => 'confirmed',
        ]);

        $totalHarga = 0;

        // Simpan semua detail layanan
        foreach ($dataList as $data) {
            $booking->details()->create([
                'customer_name' => $data['customer_name'],
                'service_id' => $data['service_id'],
                'time' => $data['time'],
                'price' => $data['harga'],
                'barberman_id' => $data['barberman_id'],
                'hairstyle_id' => $data['hairstyle_id'],
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            $totalHarga += $data['harga'];
        }

        // Simpan pembayaran total
        Payment::create([
            'booking_id' => $booking->id,
            'payment_type' => 'cash',
            'gross_amount' => $totalHarga,
            'status' => 'paid',
            'transaction_time' => now(),
        ]);

        return [$booking];
    }

    // protected function generateQueueNumber($date, $barbermanId)
    // {
    //     $lastQueue = \App\Models\Queue::whereDate('created_at', $date)
    //         ->whereHas('booking', function ($query) use ($barbermanId) {
    //             $query->where('barberman_id', $barbermanId);
    //         })
    //         ->orderByDesc('antrean')
    //         ->first();

    //     return $lastQueue ? $lastQueue->antrean + 1 : 1;
    // }

    public function update($bookingId, array $bookingData, array $detailsData)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->update($bookingData);

        $updatedDetails = [];

        foreach ($detailsData as $detail) {
            $detailModel = BookingDetail::findOrFail($detail['id']);
            $detailModel->update([
                'time' => $detail['time'],
                'hairstyle_id' => $detail['hairstyle_id'],
                'deskripsi' => $detail['deskripsi'] ?? null,
            ]);

            $updatedDetails[] = $detailModel;
        }

        return [
            'booking' => $booking,
            'details' => $updatedDetails,
        ];
    }


    public function updateStatus($id, array $data)
    {
        $booking = Booking::findOrFail($id);
        $currentStatus = $booking->status;
        $newStatus = $data['status'];

        if ($currentStatus === 'pending') {
            if ($newStatus !== 'cancelled') {
                throw new \Exception('Booking dengan status "pending" hanya bisa dibatalkan.');
            }
        } elseif ($currentStatus === 'confirmed') {
            if (!in_array($newStatus, ['completed', 'cancelled'])) {
                throw new \Exception('Status hanya bisa diubah menjadi "completed" atau "cancelled".');
            }

            if ($newStatus === 'completed') {
                $bookingTime = Carbon::parse($booking->date . ' ' . $booking->details->time);
                if (now()->lt($bookingTime)) {
                    throw new \Exception('Booking belum bisa diselesaikan sebelum waktu booking.');
                }
            }
        } else {
            throw new \Exception('Status booking tidak dapat diubah.');
        }

        $booking->update(['status' => $newStatus]);

        return $booking;
    }

    public function delete($id)
    {
        $booking = Booking::findOrFail($id);
        return $booking->delete();
    }

    public function show($id)
    {
        $booking = $this->findById($id);

        return $booking;
    }
}
