<?php

namespace App\Http\Controllers;

use App\Models\Barberman;
use App\Models\Gallery;
use App\Models\OperationalHour;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        // Ambil rata-rata rating berdasarkan aspek "barberman" dari tabel reviews
        $ratings = DB::table('reviews as r')
            ->join('bookings as b', 'r.booking_id', '=', 'b.id')
            ->join('booking_details as d', 'd.booking_id', '=', 'b.id')
            ->where('r.aspect', 'barberman')
            ->select('d.barberman_id', DB::raw('AVG(r.rating) as avg_rating'))
            ->groupBy('d.barberman_id')
            ->pluck('avg_rating', 'd.barberman_id');

        // Ambil jumlah review per barberman
        $reviewCounts = DB::table('reviews as r')
            ->join('bookings as b', 'r.booking_id', '=', 'b.id')
            ->join('booking_details as d', 'd.booking_id', '=', 'b.id')
            ->where('r.aspect', 'barberman')
            ->select('d.barberman_id', DB::raw('COUNT(*) as total_reviews'))
            ->groupBy('d.barberman_id')
            ->pluck('total_reviews', 'd.barberman_id');

        // Ambil data barberman
        $barbermen = Barberman::all();

        // Tambahkan nilai rating & review count ke setiap barberman
        foreach ($barbermen as $barber) {
            $barber->average_rating = $ratings[$barber->id] ?? 0;
            $barber->reviews_count = $reviewCounts[$barber->id] ?? 0;
        }

        $gallery = Gallery::where('is_visible', true)->get();
        $operational = OperationalHour::first();
        $service = Service::first();

        return view('welcome', compact('barbermen', 'gallery', 'operational', 'service'));
    }
}
