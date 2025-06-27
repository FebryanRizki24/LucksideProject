<?php

namespace App\Http\Controllers;

use App\Models\FaceShape;
use App\Models\Hairstyle;
use App\Models\Rekomendasi;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekomendasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faceShapes = FaceShape::get();

        return view('recommendation', ['faceShapes' => $faceShapes]);
    }

    public function detail($shape)
    {
        $faceShapes = FaceShape::all();

        $faceShape = FaceShape::where('name', $shape)->first();

        if (!$faceShape) {
            return redirect()->back()->with('error', 'Bentuk wajah tidak ditemukan.');
        }

        $hairstyles = Hairstyle::whereHas('faceShapes', function ($query) use ($faceShape) {
            $query->where('face_shape_id', $faceShape->id);
        })->get();

        // Ambil average rating dari review untuk aspek hairstyle
        $ratings = DB::table('reviews as r')
            ->join('bookings as b', 'r.booking_id', '=', 'b.id')
            ->where('r.aspect', 'hairstyle')
            ->select('r.target_id as hairstyle_id', DB::raw('AVG(r.rating) as avg_rating'))
            ->groupBy('r.target_id')
            ->pluck('avg_rating', 'hairstyle_id'); // [hairstyle_id => avg_rating]

        // Tambahkan rating ke setiap object hairstyle
        foreach ($hairstyles as $hairstyle) {
            $hairstyle->average_rating = $ratings[$hairstyle->id] ?? 0;
        }

        return view('detailrecommendation', compact('shape', 'faceShapes', 'hairstyles'));
    }

    public function show(Hairstyle $hairstyle)
    {
        $images = Review::with(['images', 'booking.details.hairstyle', 'user'])
            ->where('aspect', 'hairstyle')
            ->whereHas('images')
            ->whereHas('booking.details', function ($q) use ($hairstyle) {
                $q->where('hairstyle_id', $hairstyle->id);
            })
            ->get();

        return view('show-hairstyle', compact('hairstyle', 'images'));
    }
}
