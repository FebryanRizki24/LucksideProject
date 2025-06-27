<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    use HasFactory, HasUuids;

    /**
     * Menonaktifkan auto increment
     *
     * @var bool
     */
    public $incrementing = false;

    protected $table = 'review_images';

    protected $fillable = ['review_id', 'image'];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
