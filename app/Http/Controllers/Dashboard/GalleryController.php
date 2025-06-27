<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboard\GalleryRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class GalleryController extends Controller
{
    protected $galleryRepo;
    protected $response;

    public function __construct(GalleryRepository $galleryRepo, Response $response)
    {
        $this->galleryRepo = $galleryRepo;
        $this->response = $response;

        $this->middleware('can:gallery-view')->only(['index', 'getData']);
        $this->middleware('can:gallery-store')->only(['store']);
        $this->middleware('can:gallery-update')->only(['update']);
        $this->middleware('can:gallery-destroy')->only(['destroy']);
    }

    public function index()
    {
        return view('dashboard.gallery');
    }

    public function getData()
    {
        if (request()->ajax()) {
            return $this->galleryRepo->getDatatables();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpg,png,jpeg|max:2048',
                'is_visible' => 'nullable|boolean'
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('gallerys', 'public');
            }

            $data = $this->galleryRepo->create($validated);

            return $this->response->store($data);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (Exception $e) {
            return $this->response->storeError();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $gallery = $this->galleryRepo->findById($id);
            if (!$gallery) {
                return $this->response->notFound();
            }

            $validated =  $request->validate([
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                'is_visible' => 'nullable|boolean'
            ]);

            if ($request->hasFile('image')) {
                if ($gallery->image) {
                    Storage::disk('public')->delete($gallery->image);
                }
                $validated['image'] = $request->file('image')->store('gallerys', 'public');
            }

            $data = $this->galleryRepo->update($id, $validated);

            return $this->response->update($data);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (Exception $e) {
            return $this->response->updateError();
        }
    }

    public function destroy($id)
    {
        try {
            $data = $this->galleryRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            if ($data->image) {
                Storage::disk('public')->delete($data->image);
            }

            $this->galleryRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }

    public function toggleVisible(Request $request, $id)
    {
        try {
            $data = $this->galleryRepo->update($id, [
                'is_visible' => $request->is_visible
            ]);
            return $this->response->update($data);
        } catch (Exception $e) {
            return $this->response->updateError();
        }
    }
}
