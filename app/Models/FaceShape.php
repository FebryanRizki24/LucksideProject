<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceShape extends Model
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

    protected $table = 'face_shapes';

    protected $fillable = ['name', 'deskripsi'];

    public function hairstyles()
    {
        return $this->belongsToMany(Hairstyle::class, 'hairstyle_face_shape');
    }
}
