<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Menonaktifkan auto increment
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe primary key adalah string (UUID)
     *
     * @var string
     */

    protected $table = 'bookings';

    protected $fillable = [
        'kode',
        'date',
        'status',
        'snap_token',
    ];

    public static function generateKodeBookingOnline($tanggal)
    {
        $tanggalFormatted = Carbon::parse($tanggal)->format('Ymd');

        $lastBooking = self::whereDate('date', $tanggal)
            ->where('kode', 'like', 'BK-%')
            ->orderBy('kode', 'desc')
            ->first();

        if ($lastBooking && preg_match('/(\d+)$/', $lastBooking->kode, $match)) {
            $nextNumber = intval($match[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'BK-' . $tanggalFormatted . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public static function generateKodeBookingOffline($tanggal)
    {
        $tanggalFormatted = Carbon::parse($tanggal)->format('Ymd');

        $lastBooking = self::whereDate('date', $tanggal)
            ->where('kode', 'like', 'BKO-%')
            ->orderBy('kode', 'desc')
            ->first();

        if ($lastBooking && preg_match('/(\d+)$/', $lastBooking->kode, $match)) {
            $nextNumber = intval($match[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'BKO-' . $tanggalFormatted . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barberman()
    {
        return $this->belongsTo(Barberman::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id');
    }

    public function hairstyle()
    {
        return $this->belongsTo(Hairstyle::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'booking_id');
    }

    public function details()
    {
        return $this->hasMany(BookingDetail::class, 'booking_id');
    }
}
