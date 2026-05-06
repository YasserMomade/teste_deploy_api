<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditLogService
{

    private static ?User $currentUser = null;
    private static ?Request $currentRequest = null;

    private const MASKED_FIELDS = ['password', 'remenber_token', 'token',];

    public static function setContext(User $user, Request $request): void 
    {
        self::$currentUser = $user;
        self::$currentRequest = $request;
    } 

    public static function getCurrentUser(): ?User 
    {
        return self::$currentUser;
    }

    public function clearContext(): void
    {
        self::$currentUser = null;
        self::$currentRequest = null;
    }

    public static function log(
        string $action,
        Model $model,
        array $oldValues = [],
        array $newValues = []
    ):void {
        try {
            AuditLog::create([
            'user_id' => self::$currentUser?->id, 
            'user_code' => self::$currentUser?->user_code, 
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => self::maskSensitiveFields($oldValues),
            'new_values' => self::maskSensitiveFields($newValues), 
            'ip_address' => self::$currentRequest?->ip(),
            'user_agent' => self::$currentRequest?->userAgent(),
            ]);
        } catch(\Exception $e) {
           \Log::error('AuditLog failed: ' .$e->getMessage(), [
            'action' => $action,
            'model' => get_class($model),
            'model_id' => $model->getKey(),
           ]);
        }
    }        

}