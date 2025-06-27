<?php 

namespace App\Repositories\Dashboard;

use App\Models\Barberman;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class BarbermanRepository
{
    public function getDatatables()
    {
        $query = Barberman::query();

        return DataTables::of($query)
            ->addColumn('photo', function ($row) {
                return $row->photo ? asset('storage/' . $row->photo) : null;
            })
            ->addColumn('action', fn($row) => '<button class="btn-review px-2 py-1 bg-green-500 text-white rounded" data-id="' . $row->id . '"> Review </button>')
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create($data)
    {
        $barberman = Barberman::create($data);

        $nameParts = explode(' ', $barberman->name);
        $firstName = strtolower($nameParts[0]);

        $user = User::create([
            'name' => $barberman->name,
            'email' => $firstName . 'barberman@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        $user->syncRoles(['barberman']);

        return $barberman;
    }

    public function update($id, $data)
    {
        $barberman = Barberman::findOrFail($id);
        $barberman->update($data);
        return $barberman;
    }

    public function delete($id)
    {
        $barberman = Barberman::findOrFail($id);
        return $barberman->delete();
    }

    public function findById($id)
    {
        return Barberman::findOrFail($id);
    }

    public function getReviews($id)
    {
        $reviews = Review::where('aspect', 'barberman')
        ->where('target_id', $id)
        ->with('user:id,name')
        ->get()
        ->map(function ($review) {
            return [
                'rating' => $review->rating,
                'review' => $review->comment,
                'user_name' => $review->user->name
            ];
        });

        return $reviews;
    }
}
