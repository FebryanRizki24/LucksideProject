<?php

namespace App\Repositories;

use App\Events\BookingCreated;
use App\Mail\PaymentStatusMail;
use App\Models\BarbermanSchedule;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\HairstyleFaceShape;
use App\Models\Payment;
use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingRepository
{
    public function getAvailableSlots($barberman_id, $date)
    {
        $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

        Log::info("Checking available slots for Barberman ID: $barberman_id on Date: $date");

        $schedules = BarbermanSchedule::where('barberman_id', $barberman_id)->get();

        if ($schedules->isEmpty()) {
            Log::error("No schedule found for Barberman ID: $barberman_id");
            return ['error' => 'Schedule not found'];
        }

        $allSlots = [];
        foreach ($schedules as $schedule) {
            $slots = $this->generateTimeSlots($schedule->start_time, $schedule->end_time);
            $allSlots = array_merge($allSlots, $slots);
        }

        Log::info("All generated slots: ", $allSlots);

        $bookedSlots = BookingDetail::where('barberman_id', $barberman_id)
            ->whereHas('booking', function ($query) use ($date) {
                $query->whereDate('date', $date)
                    ->whereIn('status', ['pending', 'confirmed']);
            })
            ->pluck('time')
            ->toArray();

        Log::info("Booked slots: ", $bookedSlots);

        $blockedSlots = $this->getBlockedSlots($bookedSlots, $allSlots);

        Log::info("Blocked slots after applying logic: ", $blockedSlots);

        $availableSlots = array_diff($allSlots, $blockedSlots);

        Log::info("Final available slots: ", array_values($availableSlots));

        return array_values($availableSlots);
    }

    private function generateTimeSlots($start, $end, $interval = 30)
    {
        $slots = [];
        $current = strtotime($start);
        $end = strtotime($end);

        while ($current < $end) {
            $slots[] = date('H:i:s', $current);
            $current = strtotime("+$interval minutes", $current);
        }

        return $slots;
    }

    private function getBlockedSlots($bookedTimes, $availableSlots, $interval = 30)
    {
        $blockedSlots = [];

        foreach ($bookedTimes as $time) {
            $roundedSlot = $this->roundToNearestSlot($time, $interval);

            // Cari slot yang belum diblokir
            while (in_array($roundedSlot, $blockedSlots) || !in_array($roundedSlot, $availableSlots)) {
                $roundedSlot = date('H:i:s', strtotime("+$interval minutes", strtotime($roundedSlot)));
            }

            $blockedSlots[] = $roundedSlot;
        }

        return array_values(array_unique($blockedSlots));
    }

    private function roundToNearestSlot($time, $interval)
    {
        $minutes = date('i', strtotime($time));
        $hour = date('H', strtotime($time));

        $lower = floor($minutes / $interval) * $interval;
        $upper = ceil($minutes / $interval) * $interval;

        if ($upper == 60) {
            $upper = 0;
            $hour = date('H', strtotime('+1 hour', strtotime($time)));
        }

        $distanceToLower = abs($minutes - $lower);
        $distanceToUpper = abs($minutes - $upper);

        $nearestMinutes = ($distanceToLower <= $distanceToUpper) ? $lower : $upper;

        return sprintf('%02d:%02d:00', $hour, $nearestMinutes);
    }

    public function create(array $data)
    {
        $user = Auth::user();

        $service = Service::first();
        $servicePrice = $service->price;
        $feePercentage = 0.007;
        $biayaAdmin = round($servicePrice * $feePercentage);
        $totalBayar = $servicePrice + $biayaAdmin;

        $date = Carbon::createFromFormat('d-m-Y', $data['date'])->format('Y-m-d');

        $kodeBooking = Booking::generateKodeBookingOnline($date);

        $booking = Booking::create([
            'kode' => $kodeBooking,
            'date' => $date,
            'status' => 'pending',
        ]);

        BookingDetail::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'service_id' => $service->id,
            'barberman_id' => $data['barberman_id'],
            'hairstyle_id' => $data['hairstyle_id'],
            'time' => $data['time'],
            'price' => $servicePrice,
            'deskripsi' => $data['deskripsi']
        ]);

        $snapToken = $this->generateSnapToken($booking, $service, $biayaAdmin);

        $booking->update(['snap_token' => $snapToken]);

        Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'amount' => $totalBayar,
            'status' => 'pending',
        ]);

        event(new BookingCreated($booking));

        return $booking;
    }

    public function generateSnapToken(Booking $booking, $service, $biayaAdmin)
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $totalBayar = $service->price + $biayaAdmin;

        $user = $booking->details->first()->user;

        $snapToken = \Midtrans\Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $booking->id,
                'gross_amount' => $totalBayar,
            ],
            'item_details' => [
                [
                    'id' => $service->id,
                    'price' => $service->price,
                    'quantity' => 1,
                    'name' => $service->name
                ],
                [
                    'id' => 'admin-fee',
                    'price' => $biayaAdmin,
                    'quantity' => 1,
                    'name' => 'Biaya Admin QRIS 0.7%'
                ]
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'enabled_payments' => ["other_qris"],
        ]);

        return $snapToken;
    }

    public function handleCallback(Request $request)
    {
        Log::info('Midtrans Callback Received:', $request->all());

        $serverKey = config('midtrans.serverKey');
        $expectedSignature = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Cek keaslian signature
        if ($expectedSignature !== $request->signature_key) {
            Log::warning('Invalid Signature Key on Midtrans Callback', [
                'order_id' => $request->order_id,
                'expected_signature' => $expectedSignature,
                'received_signature' => $request->signature_key
            ]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // Ambil data booking
        $booking = Booking::find($request->order_id);
        if (!$booking) {
            Log::error('Booking not found', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // Ambil data pembayaran
        $payment = Payment::where('booking_id', $booking->id)->first();
        if (!$payment) {
            Log::error('Payment record not found', ['booking_id' => $booking->id]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $status = $request->transaction_status;

        switch ($status) {
            case 'capture':
            case 'settlement':
                $booking->update(['status' => 'confirmed']);
                $payment->update([
                    'status' => 'paid',
                    'payment_type' => $request->payment_type,
                    'acquirer' => $request->acquirer ?? null,
                    'gross_amount' => $request->gross_amount,
                    'transaction_time' => $request->transaction_time,
                ]);

                $this->sendPaymentEmail($booking, 'paid');
                break;

            case 'pending':
                $payment->update(['status' => 'pending']);
                $this->sendPaymentEmail($booking, 'pending');
                break;

            case 'cancel':
            case 'deny':
            case 'expire':
                $booking->update(['status' => 'cancelled']);
                $payment->update(['status' => 'failed']);
                $this->sendPaymentEmail($booking, 'failed');
                break;

            default:
                Log::warning('Unhandled transaction status', ['status' => $status]);
                return response()->json(['message' => 'Unhandled transaction status'], 400);
        }

        Log::info('Callback processed successfully', ['booking_id' => $booking->id]);
        return response()->json(['message' => 'Callback handled successfully']);
    }

    private function sendPaymentEmail($booking, $status)
    {
        try {
            $bookingDetail = $booking->details()->first();
            $user = $bookingDetail ? $bookingDetail->user : null;
            if ($user && $user->email) {
                Mail::to($user->email)->send(new PaymentStatusMail($booking, $status));
                Log::info("Payment status email ($status) sent to: " . $user->email);
            } else {
                Log::warning('User email not found, cannot send email', ['booking_id' => $booking->id]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send payment status email: " . $e->getMessage(), [
                'booking_id' => $booking->id,
                'status' => $status,
            ]);
        }
    }

    public function getByFaceShape($faceShapeId)
    {
        $hairstyles = HairstyleFaceShape::with('hairstyle')
            ->where('face_shape_id', $faceShapeId)
            ->get()
            ->pluck('hairstyle')
            ->filter();

        return response()->json($hairstyles);
    }
}
