<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboard\ReviewRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    protected $reviewRepo;
    protected $response;

    public function __construct(ReviewRepository $reviewRepo, Response $response)
    {
        $this->reviewRepo = $reviewRepo;
        $this->response = $response;
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'user_id' => 'required|string',
                'aspect' => 'required|string',
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'required|string',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('images')) {
                $validated['images'] = array_values($request->file('images'));
            } else {
                $validated['images'] = [];
            }

            // dd($validated);

            $data = $this->reviewRepo->create($validated);

            return $this->response->store($data);
        } catch (ValidationException $e) {
            return $this->response->validationError($e->validator->errors());
        } catch (Exception $e) {
            return $this->response->storeError($e->getMessage());
        }        
    }
}
