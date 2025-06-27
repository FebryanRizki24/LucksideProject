<?php

namespace App\Repositories\Dashboard;

use App\Models\Booking;
use App\Models\Review;

class ReviewRepository
{
    public function create($data)
    {
        if (!isset($data['booking_id'])) {
            throw new \Exception('Booking ID harus disertakan!');
        }

        $booking = Booking::with('details')
            ->where('id', $data['booking_id'])
            ->whereHas('details', function ($query) use ($data) {
                $query->where('user_id', $data['user_id']);
            })
            ->where('status', 'completed')
            ->first();

        if (!$booking) {
            throw new \Exception('Booking tidak ditemukan atau belum selesai!');
        }

        $targetId = $this->getTargetId($data['aspect'], $booking);

        $review = Review::firstOrNew([
            'user_id' => $data['user_id'],
            'booking_id' => $data['booking_id'],
            'aspect' => $data['aspect'],
        ]);

        $review->target_id = $targetId;
        $review->rating = $data['rating'];
        $review->comment = $data['review'];
        $review->save();

        if (!empty($data['images']) && is_array($data['images'])) {
            $review->images()->delete();

            foreach ($data['images'] as $image) {
                $path = $image->store('reviews', 'public');
                $review->images()->create(['image' => $path]);
            }
        }

        return $review;
    }

    private function getTargetId($aspect, $booking)
    {
        switch ($aspect) {
            case 'barberman':
                return $booking->details->first()->barberman_id ?? null;
            case 'hairstyle':
                return $booking->details->first()->hairstyle_id ?? null;
            case 'booking':
                return $booking->id;
            default:
                throw new \Exception('Aspect tidak valid!');
        }
    }
}
