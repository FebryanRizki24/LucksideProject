<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
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

    protected $table = 'galleries';

    protected $fillable = [
        'image',
        'is_visible'
    ];
}
