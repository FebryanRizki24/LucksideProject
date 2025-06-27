<?php

namespace App\Http\Controllers;

use App\Helper\Response;
use App\Repositories\HolidayRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HolidayController extends Controller
{
    protected $holidayRepo;
    protected $response;

    public function __construct(HolidayRepository $holidayRepo, Response $response)
    {
        $this->holidayRepo = $holidayRepo;
        $this->response = $response;

        $this->middleware('can:holiday-view')->only(['index', 'getData']);
        $this->middleware('can:holiday-store')->only(['store']);
        $this->middleware('can:holiday-update')->only(['update']);
        $this->middleware('can:holiday-destroy')->only(['destroy']);
    }

    public function index()
    {
        return view('dashboard.holiday');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date|unique:holidays,date',
                'description' => 'string',
            ]);

            $data = $this->holidayRepo->create($validated);

            return $this->response->store($data);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (
            Exception $e
        ) {
            return $this->response->storeError();
        }
    }

    /**
     * Display the specified resource.
     */
    public function getData()
    {
        if (request()->ajax()) {
            return $this->holidayRepo->getDatatables();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date|unique:holidays,date',
                'description' => 'string',
            ]);

            $data = $this->holidayRepo->update($id, $validated);

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
            $data = $this->holidayRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            $this->holidayRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }
}
