<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BarbermanSchedule extends Model
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

    protected $table = 'barberman_schedules';

    protected $fillable = [
        'barberman_id',
        'start_time',
        'end_time'
    ];

    public function barberman()
    {
        return $this->belongsTo(Barberman::class);
    }
}