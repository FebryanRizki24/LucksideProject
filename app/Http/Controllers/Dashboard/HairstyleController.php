<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\Response;
use App\Http\Controllers\Controller;
use App\Models\FaceShape;
use App\Repositories\Dashboard\HairstyleRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class HairstyleController extends Controller
{
    protected $hairstyleRepo;
    protected $response;

    public function __construct(HairstyleRepository $hairstyleRepo, Response $response)
    {
        $this->hairstyleRepo = $hairstyleRepo;
        $this->response = $response;

        $this->middleware('can:hairstyle-view')->only(['index', 'getData']);
        $this->middleware('can:hairstyle-store')->only(['store']);
        $this->middleware('can:hairstyle-update')->only(['update']);
        $this->middleware('can:hairstyle-destroy')->only(['destroy']);
    }

    public function index()
    {
        $faceShapes = FaceShape::get();
        return view('dashboard.hairstyle', compact('faceShapes'));
    }

    public function getData()
    {
        if (request()->ajax()) {
            return $this->hairstyleRepo->getDatatables();
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());

            $validated = $request->validate([
                'name' => 'required|string|unique:hairstyles,name|max:255',
                'category' => 'required|array',
                'category.*' => 'exists:face_shapes,id',
                'deskripsi' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
            ]);

            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('hairstyles', 'public');
            }

            // dd($validated);

            $data = $this->hairstyleRepo->create($validated);

            if ($data) {
                $this->hairstyleRepo->syncFaceShapes($data->id, $validated['category']);
            } else {
                throw new Exception("Gagal menyimpan hairstyle");
            }

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
            $hairstyle = $this->hairstyleRepo->findById($id);
            if (!$hairstyle) {
                return $this->response->notFound();
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:hairstyles,name,' . $id,
                'category' => 'required|array',
                'category.*' => 'exists:face_shapes,id',
                'deskripsi' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            ]);

            if ($request->hasFile('photo')) {
                if ($hairstyle->photo) {
                    Storage::disk('public')->delete($hairstyle->photo);
                }
                $validated['photo'] = $request->file('photo')->store('hairstyles', 'public');
            }

            $data = $this->hairstyleRepo->update($id, $validated);

            // Hapus kategori lama & tambahkan yang baru
            $this->hairstyleRepo->syncFaceShapes($id, $validated['category']);

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
            $data = $this->hairstyleRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            $this->hairstyleRepo->deleteFaceShapes($id);

            if ($data->photo) {
                Storage::disk('public')->delete($data->photo);
            }

            $this->hairstyleRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }

    public function reviews($id)
    {
        try {
            $data = $this->hairstyleRepo->getReviews($id);

            return $this->response->show($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Barberman dengan ID ' . $id . ' tidak ditemukan.',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan pada database.',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan tidak terduga.',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
