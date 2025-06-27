<?php

namespace App\Http\Controllers;

use App\Helper\Response;
use App\Models\OperationalHour;
use App\Repositories\OperationalHourRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OperationalHourController extends Controller
{
    protected $operationalHourRepo;
    protected $response;

    public function __construct(OperationalHourRepository $operationalHourRepo, Response $response)
    {
        $this->operationalHourRepo = $operationalHourRepo;
        $this->response = $response;

        $this->middleware('can:operationalHour-view')->only(['index', 'getData']);
        $this->middleware('can:operationalHour-store')->only(['store']);
        $this->middleware('can:operationalHour-update')->only(['update']);
        $this->middleware('can:operationalHour-destroy')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.operationalHour');
    }

    public function store(Request $request)
    {
         try {
            $validated = $request->validate([
                'open_time' => 'required|date_format:H:i',
                'close_time' => 'required|date_format:H:i|after:open_time',
            ]);
            
            $data = $this->operationalHourRepo->create($validated);
            
            return $this->response->store($data);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (Exception $e) {
            return $this->response->storeError();
        }
    }

    /**
     * Display the specified resource.
     */
    public function getData()
    {
        if (request()->ajax()) {
            return $this->operationalHourRepo->getDatatables();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'open_time' => 'required|date_format:H:i',
                'close_time' => 'required|date_format:H:i|after:open_time',
            ]);

            $data = $this->operationalHourRepo->update($id, $validated);

            return $this->response->update($data);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (Exception $e) {
            return $this->response->updateError();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = $this->operationalHourRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            $this->operationalHourRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }
}
