<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $status)
    {
        $this->booking = $booking;
        $this->status = $status;

        if ($status === 'paid') {
            $startTime = Carbon::parse($booking->date . ' ' . $booking->time);
            $endTime = $startTime->copy()->addMinutes(30);

            $startGCal = $startTime->format('Ymd\THis');
            $endGCal = $endTime->format('Ymd\THis');

            $title = urlencode('Booking Barbershop - Luckside Barbershop');
            $details = urlencode("Booking dengan {$booking->barberman->name} untuk gaya rambut {$booking->hairstyle->name}");
            $location = urlencode('Luckside Barbershop - Sedayulawas');

            $this->gcalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text=$title&dates=$startGCal/$endGCal&details=$details&location=$location";
        } else {
            $this->gcalUrl = null;
        }
    }

    public function build()
    {
        return $this->subject('Status Pembayaran Booking Anda')
            ->view('vendor.notifications.payment_email')
            ->with([
                'customer_name' => $this->booking->customer_name,
                'booking_date' => $this->booking->date,
                'booking_time' => $this->booking->time,
                'status' => $this->status,
                'barberman_name' => $this->booking->barberman->name,
                'hairstyle' => optional($this->booking->hairstyle)->name ?? 'Tidak dipilih',
                'barbershop_name' => config('app.name'),
                'gcalUrl' => $this->gcalUrl,
            ]);
    }
}
