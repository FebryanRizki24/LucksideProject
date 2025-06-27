<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BookingCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    protected $booking;
    protected $detail;
    protected $barberman;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load('details.user', 'details.barberman');
        $this->detail = $this->booking->details->first();

        // Ambil admin pertama yang ditemukan (kalau hanya satu admin)
        $admin = User::role('admin')->first();

        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'booking_id' => $booking->id,
                'title' => 'Booking Baru',
                'message' => "Booking baru oleh {$this->detail->user->name} pada {$this->booking->date}",
            ]);
        }

        // Notifikasi ke user
        Notification::create([
            'user_id' => $this->detail->user_id,
            'booking_id' => $booking->id,
            'title' => 'Booking Berhasil',
            'message' => "Booking Anda pada {$this->booking->date} telah berhasil dibuat",
        ]);

        // Cek apakah nama barberman yang dipesan sesuai dengan nama barberman yang ada dalam sistem
        $this->barberman = User::role('barberman')->where('name', $this->detail->barberman->name)->first();

        if ($this->barberman) {
            Log::info('BookingCreated: Barberman ditemukan', [
                'barberman_id' => $this->barberman->id,
                'barberman_name' => $this->barberman->name,
            ]);
        } else {
            Log::warning('BookingCreated: Barberman TIDAK ditemukan', [
                'nama_dicari' => $this->detail->barberman->name,
            ]);
        }

        if ($this->barberman) {
            Notification::create([
                'user_id' => $this->barberman->id,
                'booking_id' => $booking->id,
                'title' => 'Booking Baru untuk Anda',
                'message' => "Anda mendapatkan booking baru oleh {$this->detail->user->name} pada {$this->booking->date}",
            ]);
        }
    }

    public function broadcastOn()
    {
        $channels = [
            new PrivateChannel('admin-channel'),
            new PrivateChannel('user.' . $this->detail->user_id),
        ];

        if ($this->barberman) {
            $channels[] = new PrivateChannel('user.' . $this->barberman->id);
        }

        return $channels;
    }

    public function broadcastAs()
    {
        return 'booking.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->booking->id,
            'date' => $this->booking->date,
            'user' => [
                'id' => $this->detail->user->id,
                'name' => $this->detail->user->name,
            ],
            'messages' => [
                'admin' => "Booking baru oleh {$this->detail->user->name} pada {$this->booking->date}",
                'user' => "Booking Anda pada {$this->booking->date} telah berhasil dibuat",
                'barberman' => "Anda mendapatkan booking baru oleh {$this->detail->user->name} pada {$this->booking->date}"
            ],
        ];
    }
}
