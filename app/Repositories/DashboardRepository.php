<?php

namespace App\Repositories;

use App\Models\Barberman;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\User;
use App\Models\Hairstyle;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;

class DashboardRepository
{
    public function getMonthlyBookings()
    {
        $bookingQuery = Booking::query();

        if (Auth::user()->hasRole('barberman')) {
            $barbermanName = Auth::user()->name;

            $bookingQuery->whereHas('barberman', function ($query) use ($barbermanName) {
                $query->where('name', $barbermanName);
            });
        }

        $monthlyBookings = $bookingQuery
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Konversi angka bulan ke nama bulan
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        // Format hasil query agar semua bulan muncul dengan default 0 jika tidak ada data
        $formattedBookings = [];
        foreach ($months as $num => $name) {
            $formattedBookings[$name] = $monthlyBookings[$num] ?? 0;
        }

        return $formattedBookings;
    }

    public function getDailyQueueData()
    {
        $isBarberman = Auth::user()->hasRole('barberman');
        $authName = Auth::user()->name;

        if ($isBarberman) {
            $barbermen = Barberman::where('name', $authName)->pluck('name', 'id');
        } else {
            $barbermen = Barberman::pluck('name', 'id');
        }

        // Hapus antrean yang bukan hari ini
        \App\Models\Queue::whereDate('created_at', '<', now()->toDateString())->delete();

        // Ambil antrean hari ini
        $queueQuery = \App\Models\Queue::with(['details.booking', 'details.barberman'])
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('antrean');

        if ($isBarberman) {
            $queueQuery->whereHas('details.barberman', function ($query) use ($authName) {
                $query->where('name', $authName);
            });
        }

        $queues = $queueQuery->get();

        $queueData = $queues->groupBy(function ($queue) {
            return optional($queue->details->barberman)->id ?? 'lainnya';
        });

        $result = [];

        foreach ($barbermen as $barbermanId => $barbermanName) {
            $barbermanQueues = $queueData[$barbermanId] ?? collect();

            $result[$barbermanName] = $barbermanQueues->map(function ($queue) {
                return [
                    'id'         => $queue->id,
                    'name'       => $queue->customer_name,
                    'antrean'    => $queue->antrean,
                    'status'     => $queue->status,
                    'booking_id' => $queue->bookingDetail->booking->id ?? null,
                ];
            })->toArray();
        }

        return $result;
    }


    public function getAdminDashboardData($filter = 'all')
    {
        $bookingQuery = Booking::query();

        // Filter hanya untuk booking_status
        if ($filter === 'today') {
            $bookingQuery->whereDate('created_at', today());
        } elseif ($filter === 'week') {
            $bookingQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter === 'month') {
            $bookingQuery->whereMonth('created_at', now()->month);
        }

        return [
            'booking_konfirmasi' => (clone $bookingQuery)->where('status', 'confirmed')->count(),
            'booking_selesai'     => (clone $bookingQuery)->where('status', 'completed')->count(),
            'booking_pending'     => (clone $bookingQuery)->where('status', 'pending')->count(),
            'booking_batal'       => (clone $bookingQuery)->where('status', 'cancelled')->count(),

            // Data tanpa filter
            'total_booking'       => Booking::count(),
            'daily_queue'         => $this->getDailyQueueData(),
            'monthly_bookings'    => $this->getMonthlyBookings(),
            'active_barbermen'    => Barberman::where('status', '1')->count(),
            'total_users'         => User::role('user')->count(),
            'total_hairstyles'    => Hairstyle::count(),
        ];
    }

    public function getBarbermanDashboardData($filter = 'all', $barbermanName = null)
    {
        $bookingQuery = Booking::query();

        if ($filter === 'today') {
            $bookingQuery->whereDate('date', today());
        } elseif ($filter === 'week') {
            $bookingQuery->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter === 'month') {
            $bookingQuery->whereMonth('date', now()->month);
        }

        if ($barbermanName) {
            $bookingQuery->whereHas('barberman', function ($query) use ($barbermanName) {
                $query->where('name', $barbermanName);
            });
        }

        return [
            'booking_konfirmasi' => (clone $bookingQuery)->where('status', 'confirmed')->count(),
            'booking_selesai'     => (clone $bookingQuery)->where('status', 'completed')->count(),
            'booking_pending'     => (clone $bookingQuery)->where('status', 'pending')->count(),
            'booking_batal'       => (clone $bookingQuery)->where('status', 'cancelled')->count(),

            // Data tanpa filter
            'daily_queue'         => $this->getDailyQueueData(),
            'monthly_bookings'    => $this->getMonthlyBookings()
        ];
    }

    public function getUserDataDashboard()
    {
        return [
            'booking_aktif' => $this->getAktif(),
            'review_belum' => $this->getBookingBelumDireview()
        ];
    }

    private function getBookingBelumDireview()
    {
        $user = auth()->user();

        $bookingDetails = BookingDetail::with(['barberman', 'hairstyle', 'booking.reviews'])
            ->where('user_id', $user->id)
            ->whereHas('booking', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereHas('booking.reviews', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }, '<', 3)
            ->get();

        $result = [];

        foreach ($bookingDetails as $detail) {
            $booking = $detail->booking;

            if (!$booking) continue;

            $reviewedAspects = $booking->reviews
                ->where('user_id', $user->id)
                ->pluck('aspect')
                ->toArray();

            // Default aspect
            $allAspects = ['booking'];

            // 🔁 Tambahkan aspek berdasarkan detail, bukan booking
            if ($detail->barberman_id !== null) {
                $allAspects[] = 'barberman';
            }

            if ($detail->hairstyle_id !== null) {
                $allAspects[] = 'hairstyle';
            }

            $belumDireview = array_diff($allAspects, $reviewedAspects);

            if (!empty($belumDireview)) {
                $result[] = [
                    'id' => $booking->id,
                    'kode' => $booking->kode,
                    'aspek_belum_direview' => array_values($belumDireview)
                ];
            }
        }

        return $result;
    }

    private function getAktif()
    {
        $user = auth()->user();

        $bookingDetail = BookingDetail::with(['barberman', 'hairstyle', 'booking.payment'])
            ->where('user_id', $user->id)
            ->whereHas('booking', function ($query) {
                $query->whereDate('date', '>=', now()->toDateString())
                    ->whereNotIn('status', ['cancelled', 'completed']);
            })
            ->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->orderBy('bookings.date', 'asc')
            ->select('booking_details.*')
            ->first();

        if (!$bookingDetail || !$bookingDetail->booking) {
            return null;
        }

        $booking = $bookingDetail->booking;

        return [
            'tanggal' => \Carbon\Carbon::parse($booking->date)->translatedFormat('l, d F Y'),
            'jam' => substr($bookingDetail->time, 0, 5),
            'barberman' => $bookingDetail->barberman->name ?? '-',
            'hairstyle' => $bookingDetail->hairstyle->name ?? '-',
            'status_pembayaran' => $booking->payment->status ?? '-',
            'status_booking' => $booking->status,
            'booking_id' => $booking->id
        ];
    }

    public function updateStatus($queueId, $status)
    {
        $queue = Queue::find($queueId);

        if (!$queue) {
            return null;
        }

        $currentStatus = $queue->status;

        $allowedTransitions = [
            'booked' => ['cancelled', 'waiting', 'late'],
            'waiting' => ['in_service'],
            'in_service' => ['done'],
            'late' => ['waiting']
        ];

        if (isset($allowedTransitions[$currentStatus])) {
            if (!in_array($status, $allowedTransitions[$currentStatus])) {
                throw new \Exception("Status antrean tidak bisa diubah dari \"$currentStatus\" ke \"$status\".");
            }
        } else {
            throw new \Exception("Status \"$currentStatus\" tidak dapat diubah.");
        }

        $queue->status = $status;
        $queue->save();

        if (in_array($status, ['done', 'cancelled'])) {
            $bookingDetail = $queue->details;
            $booking = $bookingDetail?->booking;

            if ($booking) {
                $allDetails = $booking->details;

                if ($status === 'done') {
                    // Booking dianggap selesai hanya jika semua antrean detail sudah 'done'
                    $allDone = $allDetails->every(function ($detail) {
                        return optional($detail->queue)->status === 'done';
                    });

                    if ($allDone) {
                        $booking->status = 'completed';
                        $booking->save();
                    }
                }

                if ($status === 'cancelled') {
                    // Booking dianggap dibatalkan hanya jika semua antrean detail sudah 'cancelled'
                    $allCancelled = $allDetails->every(function ($detail) {
                        return optional($detail->queue)->status === 'cancelled';
                    });

                    if ($allCancelled) {
                        $booking->status = 'cancelled';
                        $booking->save();
                    }
                }
            }

            $queue->save();
        }

        if ($status === 'late') {
            $this->moveToLast($queue);
        }

        return $queue;
    }

    private function moveToLast($queue)
    {
        $lastQueue = Queue::whereIn('status', ['waiting', 'late'])
            ->orderByDesc('antrean')
            ->first();

        $newAntrean = $lastQueue ? $lastQueue->antrean + 1 : 1;

        $queue->antrean = $newAntrean;
        $queue->save();
    }
}
