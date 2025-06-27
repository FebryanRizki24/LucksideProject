<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Dashboard\BarbermanRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BarbermanController extends Controller
{
    protected $barbermanRepo;
    protected $response;

    public function __construct(BarbermanRepository $barbermanRepo,  Response $response)
    {
        $this->barbermanRepo = $barbermanRepo;
        $this->response = $response;

        $this->middleware('can:barberman-view')->only(['getData', 'index']);
        $this->middleware('can:barberman-store')->only(['store']);
        $this->middleware('can:barberman-update')->only(['update']);
        $this->middleware('can:barberman-destroy')->only(['destroy']);
    }

    public function index()
    {
        return view('dashboard.barberman.data');
    }

    public function getData()
    {
        if (request()->ajax()) {
            return $this->barbermanRepo->getDatatables();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:barbermans,name|max:255',
                'phone' => 'required|numeric|digits_between:11,13|unique:barbermans,phone',
                'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
            ]);

            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('barbermans', 'public');
            }

            $data = $this->barbermanRepo->create($validated);

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
            $barberman = $this->barbermanRepo->findById($id);
            if (!$barberman) {
                return $this->response->notFound();
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:barbermans,name,' . $id,
                'phone' => 'required|numeric|digits_between:11,13|unique:barbermans,phone,' . $id,
                'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                'status' => 'required|boolean'
            ]);

            if ($request->hasFile('photo')) {
                if ($barberman->photo) {
                    Storage::disk('public')->delete($barberman->photo);
                }
                $validated['photo'] = $request->file('photo')->store('barbermans', 'public');
            }

            $data = $this->barbermanRepo->update($id, $validated);

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
            $data = $this->barbermanRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            if ($data->photo) {
                Storage::disk('public')->delete($data->photo);
            }

            $user = User::where('name', $data->name)->first();
            if ($user) {
                $user->delete();
            }

            $this->barbermanRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }

    public function reviews($id)
    {
        try {
            $data = $this->barbermanRepo->getReviews($id);

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
