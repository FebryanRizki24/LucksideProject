<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HairstyleFaceShape extends Model
{
    use HasFactory;

    protected $table = 'hairstyle_face_shape';

    public $timestamps = false;

    protected $fillable = ['hairstyle_id', 'face_shape_id'];

    public function hairstyle()
    {
        return $this->belongsTo(Hairstyle::class);
    }
}