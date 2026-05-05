<?php

namespace App\Services;

use App\Models\Observation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ObservationService
{

    public function listByOrder(int $orderId): Collection
    {
        return Observation::with('creator:id,name,user_code')
            ->where('order_id', $orderId)->orderByDesc('created_at')->get();
    }

    public function create(int $orderId, array $data, User $user): Observation
    {
        $observation = Observation::create([
            'description' => $data['description'],
            'level' => $data['level'],
            'order_id' => $orderId,
            'created_by' => $user->id,
        ]);

        return $observation->load('creator:id,name,user_code');
    }

    public function update(Observation $observation, array $data): Observation
    {
        $observation->update($data);

        return $observation->fresh(['creator:id,name,user_code']);
    }

    public function delete(Observation $observation, User $user): void
    {
        if (! $observation->isOwnedBy($user)) {
            throw new \DomainException('You can onlu delete your own observations.');
        }

        $observation->delete();
    }
}
