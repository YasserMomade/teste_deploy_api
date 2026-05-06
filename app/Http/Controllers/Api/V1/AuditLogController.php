<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Services\AuditLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{

    use ApiResponse;

    public function __construct(private readonly AuditLogService $auditLogService) {}


    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'   => ['sometimes', 'integer'],
            'action'    => ['sometimes', 'string', 'in:created,updated,deleted'],
            'entity'    => ['sometimes', 'string'],
            'entity_id' => ['sometimes', 'integer'],
            'date_from' => ['sometimes', 'date'],
            'date_to'   => ['sometimes', 'date', 'after_or_equal:date_from'],
            'per_page'  => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $logs = $this->auditLogService->list(
            $request->only([
                'user_id',
                'action',
                'entity',
                'entity_id',
                'date_from',
                'date_to',
                'per_page',
            ])
        );

        return $this->success(
            AuditLogResource::collection($logs)->response()->getData(true)
        );
    }

    public function show(int $id): JsonResponse
    {
        $log = $this->auditLogService->find($id);

        return $this->success(new AuditLogResource($log));
    }
}
