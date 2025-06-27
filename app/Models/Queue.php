<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory, HasUuids;

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

    protected $table = 'queues';

    protected $fillable = [
        'booking_detail_id',
        'customer_name',
        'status',
        'antrean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function details()
    {
        return $this->belongsTo(BookingDetail::class, 'booking_detail_id');
    }
}
