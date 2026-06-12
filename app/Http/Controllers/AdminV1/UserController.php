<?php

namespace App\Http\Controllers\AdminV1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->getFiltered($request->all());
        return response()->json($users);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = $this->userRepository->create($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Tạo khách hàng thành công'
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(int $id)
    {
        $user = $this->userRepository->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user->load('membershipLevel')
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'status' => 'sometimes|required|in:active,inactive,banned',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $this->userRepository->update($id, $validated);
        $user = $this->userRepository->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Cập nhật khách hàng thành công'
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(int $id)
    {
        $this->userRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Xóa khách hàng thành công'
        ]);
    }

    /**
     * Get user orders
     */
    public function orders(int $id)
    {
        $orders = $this->userRepository->getUserOrders($id);

        return response()->json($orders);
    }

    /**
     * Get user addresses
     */
    public function addresses(int $id)
    {
        $addresses = $this->userRepository->getUserAddresses($id);

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }
}
