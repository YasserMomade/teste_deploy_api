<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StatusService;
use App\Traits\ApiResponse;
use App\Http\Requests\Status\StoreStatus;



class StatusController extends Controller
{
    use ApiResponse;
    
    protected $statusService;

    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function index()
    {
        try {
            $statuss = $this->statusService->getAllstatus();
            return $this->success($status);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(Storestatus $request)
    {
        try {
            $status = $this->statusService->createstatus($request->validated());
            return $this->success($status);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $status = $this->statusService->getstatusById($id);
            return $this->success($status);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(Storestatus $request, string $id)
    {
        try {
            $status = $this->statusService->updatestatus($id, $request->validated());
            return $this->success($status);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->statusService->deletestatus($id);
            return $this->success(null, 'status deleted successfully');

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
