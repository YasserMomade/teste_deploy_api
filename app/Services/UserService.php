<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->with('counter.country')
            ->when(
                isset($filters['search']),
                fn($q) => $q->where('name', 'like', "%{$filters['search']}%")
                            ->orWhere('lastname', 'like', "%{$filters['search']}%")
                            ->orWhere('email', 'like', "%{$filters['search']}%")
                            ->orWhere('user_code', 'like', "%{$filters['search']}%")
            )
            ->when(
                isset($filters['role']),
                fn($q) => $q->where('role', $filters['role'])
            )
            ->when(
                isset($filters['counter_id']),
                fn($q) => $q->where('counter_id', $filters['counter_id'])
            )
            ->orderBy('name')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): User
    {
        $role = RoleEnum::from($data['role']);

        $user = User::create([
            'user_code'  => $this->generateUserCode($role),
            'role'       => $role->value,
            'name'       => $data['name'],
            'lastname'   => $data['lastname'],
            'phone'      => $data['phone'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'counter_id' => $data['counter_id'] ?? null,
        ]);

        return $user->load('counter.country');
    }

    public function update(User $user, array $data): User
    {
        $updateData = [];

        foreach (['name', 'lastname', 'phone', 'email', 'role', 'counter_id'] as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Regenerate user_code if role changed
        if (isset($data['role']) && $data['role'] !== $user->role->value) {
            $updateData['user_code'] = $this->generateUserCode(RoleEnum::from($data['role']));
        }

        $user->update($updateData);

        return $user->fresh(['counter.country']);
    }

    public function delete(User $user, User $authUser): void
    {
        if ($user->id === $authUser->id) {
            throw new \DomainException('You cannot delete your own account.');
        }

        // Revoke all tokens before deleting
        $user->tokens()->delete();
        $user->delete(); // Hard delete — sem softDeletes
    }

    public function resetPassword(User $user, string $newPassword): void
    {
        $user->update(['password' => Hash::make($newPassword)]);

        // Force re-login after password reset
        $user->tokens()->delete();
    }

    private function generateUserCode(RoleEnum $role): string
    {
        $prefix = match($role) {
            RoleEnum::Admin    => 'ADM',
            RoleEnum::Manager  => 'MGR',
        };

        do {
            $code = $prefix . '-' . strtoupper(Str::random(6));
        } while (User::where('user_code', $code)->exists()); // withTrashed() REMOVIDO
        
        return $code;
    }
}