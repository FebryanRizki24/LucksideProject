<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingDetail extends Model
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

    protected $table = 'booking_details';


    protected $fillable = [
        'booking_id',
        'customer_name',
        'user_id',
        'barberman_id',
        'hairstyle_id',
        'service_id',
        'time',
        'price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function barberman()
    {
        return $this->belongsTo(Barberman::class);
    }

    public function hairstyle()
    {
        return $this->belongsTo(Hairstyle::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function queue()
    {
        return $this->hasOne(Queue::class, 'booking_detail_id');
    }
}
