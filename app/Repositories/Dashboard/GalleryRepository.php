<?php

namespace App\Repositories\Dashboard;

use App\Models\Gallery;
use Yajra\DataTables\Facades\DataTables;

class GalleryRepository
{
    public function getDatatables()
    {
        $query = Gallery::query();

        return DataTables::of($query)
            ->addColumn('image', function ($row) {
                return $row->image ? asset('storage/' . $row->image) : null;
            })
            ->addColumn('is_visible_value', function ($row) {
                return $row->is_visible;
            })
            ->addColumn('is_visible', function ($row) {
                $checked = $row->is_visible ? 'checked' : '';

                return '
                <input type="checkbox" data-id="' . $row->id . '" 
                       class="toggle-visible w-5 h-5 text-green-500 rounded focus:ring-0 border-gray-300" 
                       ' . $checked . '>';
            })
            ->rawColumns(['image', 'is_visible'])
            ->make(true);
    }

    public function create($data)
    {
        return Gallery::create($data);
    }

    public function update($id, $data)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->update($data);
        return $gallery;
    }

    public function delete($id)
    {
        $gallery = Gallery::findOrFail($id);
        return $gallery->delete();
    }

    public function findById($id)
    {
        return Gallery::findOrFail($id);
    }
}
