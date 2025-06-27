<?php

namespace App\Repositories\Dashboard;

use App\Models\Hairstyle;
use App\Models\HairstyleFaceShape;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class HairstyleRepository
{
    public function getDatatables()
    {
        $query = Hairstyle::with('faceShapes')->select('hairstyles.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('category', function ($row) {
                return $row->faceShapes->pluck('name')->implode(', ');
            })
            ->addColumn('photo', function ($row) {
                return $row->photo ? asset('storage/' . $row->photo) : null;
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn-review px-2 py-1 bg-green-500 text-white rounded" data-id="' . $row->id . '"> Review </button>';
            })
            ->rawColumns(['photo', 'action'])
            ->make(true);
    }

    public function create($data)
    {
        // $data['password'] = Hash::make('password');
        return Hairstyle::create($data);
    }

    public function update($id, $data)
    {
        $hairstyle = Hairstyle::findOrFail($id);
        $hairstyle->update($data);
        return $hairstyle;
    }

    public function delete($id)
    {
        $hairstyle = Hairstyle::findOrFail($id);
        return $hairstyle->delete();
    }

    public function findById($id)
    {
        return Hairstyle::findOrFail($id);
    }

    public function syncFaceShapes($hairstyleId, array $faceShapeIds)
    {
        HairstyleFaceShape::where('hairstyle_id', $hairstyleId)->delete();

        $data = array_map(fn($id) => [
            'hairstyle_id' => $hairstyleId,
            'face_shape_id' => $id
        ], array_unique($faceShapeIds));

        HairstyleFaceShape::insert($data);
    }


    public function deleteFaceShapes($hairstyleId)
    {
        HairstyleFaceShape::where('hairstyle_id', $hairstyleId)->delete();
    }

    public function getReviews($id)
    {
        $reviews = Review::where('aspect', 'hairstyle')
        ->where('target_id', $id)
        ->with('user:id,name', 'images')
        ->get()
        ->map(function ($review) {
            return [
                'rating' => $review->rating,
                'review' => $review->comment,
                'user_name' => $review->user->name,
                'photos' => $review->images->map(function ($img) {
                    return asset('storage/' . $img->image);
                }),
            ];
        });

        return $reviews;
    }
}
