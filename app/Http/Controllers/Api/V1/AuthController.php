<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use ApiResponse;

    /* Cria conta inicial do admmin(So riada uma vez)*/
    public function setupAdmin(Request $request): JsonResponse
    {
        if (User::exists()) {
            return $this->error('Setup already completed. This endpoint is no longer available.', 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $admin = User::create([
            'user_code' => $this->generateUserCode(RoleEnum::Admin),
            'role' => RoleEnum::Admin->value,
            'name' => $validated['name'],
            'lastname' => $validated['lastname'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'counter_id' => null,
        ]);

        $token = $admin->createToken('api-token', ['*'], now()->addHours(8))->plainTextToken;

        return $this->created([
            'user' => new UserResource($admin),
            'token' => $token,
            'expires_at' => now()->addHours(8)->toDateTimeString(),
        ], 'Admin account created successfully.');
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || !Hash::check($validated['password'], $user->password)) {
            return $this->error('Invalid credentials.', 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api-token', ['*'], now()->addHours(8))->plainTextToken;

        return $this->success([
            'user' => new UserResource($user->load('counter.country')),
            'token' => $token,
            'expires_at' => now()->addHours(8)->toDateTimeString(),
        ], 'Login successful.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: 'Logged out successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()->load('counter.country'))
        );
    }

    public function updateMe(Request $request): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        $rules = [
            'phone' => ['sometimes', 'string', 'max:50'],
            'password' => ['sometimes', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];

        if ($isAdmin) {
            $rules['name'] = ['sometimes', 'string', 'max:255'];
            $rules['lastname'] = ['sometimes', 'string', 'max:255'];
            $rules['email'] = ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }

        $validated = $request->validate($rules);

        if (isset($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($validated);
        $user->refresh()->load('counter.country');

        return $this->success(
            new UserResource($user),
            'Perfil actualizado com sucesso.'
        );
    }

    private function generateUserCode(RoleEnum $role): string
    {
        $prefix = match ($role) {
            RoleEnum::Admin => 'ADM',
            RoleEnum::Manager => 'MGR',
        };

        do {
            $code = $prefix . '-' . strtoupper(Str::random(6));
        } while (User::where('user_code', $code)->exists());

        return $code;
    }
}
