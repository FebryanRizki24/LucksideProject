<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barberman extends Model
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

    protected $table = 'barbermans';

    protected $fillable = [
        'name',
        'phone',
        'photo',
        'status'
    ];
}
