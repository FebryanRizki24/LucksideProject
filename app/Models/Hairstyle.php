<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hairstyle extends Model
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

    protected $table = 'hairstyles';

    protected $fillable = [
        'name',
        'photo',
        'deskripsi'
    ];

    public function faceShapes()
    {
        return $this->belongsToMany(FaceShape::class, 'hairstyle_face_shape');
    }
}
