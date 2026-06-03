<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly UserService $userService) {}

    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->list(
            $request->only(['search', 'role', 'counter_id', 'per_page'])
        );

        return $this->success(
            UserResource::collection($users)
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return $this->created(new UserResource($user), 'User created successfully.');
    }

    public function show(User $user): JsonResponse
    {
        $user->load('counter.country');
        return $this->success(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->userService->update($user, $request->validated());

        return $this->success(new UserResource($updated), 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        try {
            $this->userService->delete($user, $request->user());
            return $this->success(message: 'User deleted successfully.');
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function resetPassword(ResetPasswordRequest $request, User $user): JsonResponse
    {
        $this->userService->resetPassword($user, $request->validated('password'));

        return $this->success(message: 'Password reset successfully. User must login again.');
    }
}
