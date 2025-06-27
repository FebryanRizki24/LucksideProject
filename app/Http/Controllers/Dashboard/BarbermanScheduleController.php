<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboard\BarbermanScheduleRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BarbermanScheduleController extends Controller
{
    protected $scheduleRepo;
    protected $response;

    public function __construct(BarbermanScheduleRepository $scheduleRepo, Response $response)
    {
        $this->scheduleRepo = $scheduleRepo;
        $this->response = $response;

        $this->middleware('can:barbermanSchedule-view')->only(['index', 'getData']);
        $this->middleware('can:barbermanSchedule-store')->only(['store']);
        $this->middleware('can:barbermanSchedule-update')->only(['update']);
        $this->middleware('can:barbermanSchedule-destroy')->only(['destroy']);
    }

    public function index()
    {
        $barbermans = $this->scheduleRepo->getBarberman();
        return view('dashboard.barberman.schedule', compact('barbermans'));
    }

    public function getData()
    {
        if (request()->ajax()) {
            return $this->scheduleRepo->getDatatables();
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $validated = $request->validate([
                'barberman_id' => 'required|exists:barbermans,id',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time'
            ]);

            $conflict = $this->scheduleRepo->checkScheduleConflict(
                $validated['barberman_id'], 
                $validated['start_time'], 
                $validated['end_time'], 
                $request->id ?? null
            );

            if ($conflict) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal bertabrakan dengan jadwal yang sudah ada.'
                ], 422);
            }

            $data = $this->scheduleRepo->create($validated);
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
            $validated = $request->validate([
                'barberman_id' => 'required|exists:barbermans,id',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time'
            ]);

            $conflict = $this->scheduleRepo->checkScheduleConflict(
                $validated['barberman_id'], 
                $validated['start_time'], 
                $validated['end_time'], 
                $request->id ?? null
            );

            if ($conflict) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal bertabrakan dengan jadwal yang sudah ada.'
                ], 422);
            }

            $data = $this->scheduleRepo->update($id, $validated);
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
            $data = $this->scheduleRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            $this->scheduleRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }
}
