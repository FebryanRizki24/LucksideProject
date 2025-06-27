<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;

class NotificationRepository
{
    public function getAllByUser($userId)
    {
        $notifs = Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return $notifs->map(function ($notif) {
            $booking = Booking::find($notif->booking_id);
            $payment = $booking ? Payment::where('booking_id', $booking->id)->first() : null;

            return [
                'id' => $notif->id,
                'title' => $notif->title ?? 'Booking baru diterima',
                'barbershop_name' => 'Luckside Barbershop', // hardcode atau ambil dari config
                'booking_detail' => $booking
                    ? \Carbon\Carbon::parse($booking->date)->translatedFormat('l, d F Y') . ' - ' . substr($booking->time, 0, 5) . ' (' . $booking->status . ')'
                    : '-',
                'payment_status' => $payment->status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar',
                'snap_token' => $booking->snap_token ?? null,
                'payment_method' => $payment->payment_type ?? null,
                'payment_date' => $payment && $payment->transaction_time
                    ? \Carbon\Carbon::parse($payment->transaction_time)->format('d M Y H:i')
                    : null,
                'notes' => 'Harap datang 10 menit lebih awal.',
                'booking_id' => $notif->booking_id ?? null,
                'is_read' => $notif->is_read,
                'message' => $notif->message,
            ];
        });
    }

    public function markAsRead($id, $userId)
    {
        return Notification::where('id', $id)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    public function delete($id, $userId)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            \Log::info('Notifikasi tidak ditemukan: ' . $id);
            return false; // Kembalikan false jika notifikasi tidak ditemukan
        }

        $deleted = $notification->delete();

        if ($deleted) {
            \Log::info('Notifikasi berhasil dihapus: ' . $id);
        } else {
            \Log::info('Gagal menghapus notifikasi: ' . $id);
        }

        return $deleted;
    }
}
