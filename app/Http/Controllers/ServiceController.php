<?php

namespace App\Http\Controllers;

use App\Helper\Response;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    protected $serviceRepo;
    protected $response;

    public function __construct(ServiceRepository $serviceRepo, Response $response)
    {
        $this->serviceRepo = $serviceRepo;
        $this->response = $response;

        $this->middleware('can:service-view')->only(['index', 'getData']);
        $this->middleware('can:service-store')->only(['store']);
        $this->middleware('can:service-update')->only(['update']);
        $this->middleware('can:service-destroy')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.service');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         try {
            $validated = $request->validate([
                'name' => 'required|string',
                'price' => 'required|integer',
            ]);
            
            $data = $this->serviceRepo->create($validated);
            
            return $this->response->store($data);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (Exception
        
        $e) {
            return $this->response->storeError();
        }
    }

    /**
     * Display the specified resource.
     */
    public function getData()
    {
        if (request()->ajax()) {
            return $this->serviceRepo->getDatatables();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'price' => 'required|integer',
            ]);

            $data = $this->serviceRepo->update($id, $validated);

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
            $data = $this->serviceRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            $this->serviceRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }
}
