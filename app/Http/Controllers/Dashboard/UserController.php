<?php 

namespace App\Http\Controllers\Dashboard;

use App\Helper\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboard\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $userRepo;
    protected $response;

    public function __construct(UserRepository $userRepo, Response $response)
    {
        $this->userRepo = $userRepo;
        $this->response = $response;

        $this->middleware('can:user-view')->only(['index', 'getData']);
        $this->middleware('can:user-store')->only(['store']);
        $this->middleware('can:user-update')->only(['update']);
        $this->middleware('can:user-destroy')->only(['destroy']);
    }

    public function index()
    {
        $roles = $this->userRepo->getRole();
        return view('dashboard.user', compact('roles'));
    }

    public function getData()
    {
        if (request()->ajax()) {
            return $this->userRepo->getDatatables();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|numeric|digits_between:11,13|unique:users,phone',
                'role' => 'required|exists:roles,id'
            ]);
            
            $data = $this->userRepo->create($validated);
            
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
                'name' => 'required|string|max:255|unique:users,name,' . $id,
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|numeric|digits_between:11,13|unique:users,phone,' . $id,
                'role' => 'required|exists:roles,id'
            ]);

            $data = $this->userRepo->update($id, $validated);

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
            $data = $this->userRepo->findById($id);

            if (!$data) {
                return $this->response->notFound();
            }

            $this->userRepo->delete($id);
            return $this->response->destroy($data);
        } catch (Exception $e) {
            return $this->response->destroyError();
        }
    }
}
