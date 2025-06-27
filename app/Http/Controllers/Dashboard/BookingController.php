<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\Response;
use App\Http\Controllers\Controller;
use App\Models\Barberman;
use App\Models\Hairstyle;
use App\Models\Service;
use App\Repositories\Dashboard\BookingRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    protected $bookingRepo;
    protected $response;

    public function __construct(BookingRepository $bookingRepo, Response $response)
    {
        $this->bookingRepo = $bookingRepo;
        $this->response = $response;

        $this->middleware('can:booking-view')->only(['index', 'getData', 'show']);
        $this->middleware('can:booking-store')->only(['store']);
        $this->middleware('can:booking-update')->only(['update']);
        $this->middleware('can:booking-destroy')->only(['destroy']);
    }

    public function index()
    {
        $hairstyles = Hairstyle::all();
        $barbermans = Barberman::all();
        $services = Service::all();
        return view('dashboard.booking', compact('hairstyles', 'barbermans', 'services'));
    }

    public function getData(Request $request)
    {
        if (request()->ajax()) {
            return $this->bookingRepo->getDatatables($request);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'bookings' => 'required|array|min:1',
                'bookings.*.customer_name' => 'required|string|max:255',
                'bookings.*.date' => 'required|date_format:Y-m-d',
                'bookings.*.time' => 'required|date_format:H:i',
                'bookings.*.hairstyle_id' => 'required|exists:hairstyles,id',
                'bookings.*.barberman_id' => 'required|exists:barbermans,id',
                'bookings.*.service_id' => 'required|exists:services,id',
                'bookings.*.deskripsi' => 'nullable|string',
            ]);

            $bookings = $this->bookingRepo->create($request->bookings);

            return response()->json([
                'message' => 'Semua booking berhasil disimpan!',
                'data' => $bookings
            ]);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (Exception $e) {
            Log::error('Gagal simpan booking: ' . $e->getMessage());
            dd($e->getMessage());
            return $this->response->storeError();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi data
            $validated = $request->validate([
                'booking.date' => 'required|date_format:Y-m-d',
                'details' => 'required|array|min:1',
                'details.*.id' => 'required|exists:booking_details,id',
                'details.*.time' => 'required|date_format:H:i',
                'details.*.hairstyle_id' => 'required|exists:hairstyles,id',
                'details.*.deskripsi' => 'nullable|string',
            ]);

            // Update melalui repository
            $result = $this->bookingRepo->update($id, $validated['booking'], $validated['details']);

            return response()->json([
                'message' => 'Booking dan detail berhasil diupdate!',
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (Exception $e) {
            Log::error('Gagal update booking & details: ' . $e->getMessage(), [
                'booking_id' => $id,
                'request' => $request->all()
            ]);
            return $this->response->updateError();
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'status' => 'required|in:completed,cancelled',
            ]);

            $data = $this->bookingRepo->updateStatus($id, $validated);

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
            $data = $this->bookingRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            $this->bookingRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }

    public function show($id)
    {
        try {
            $data = $this->bookingRepo->show($id);

            return $this->response->show($data);
        } catch (Exception $e) {
            return $this->response->empty();
        }
    }
}
