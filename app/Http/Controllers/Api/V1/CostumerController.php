<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CostumerService;
use App\Traits\ApiResponse;
use App\Http\Requests\Costumer\StoreCostumer;
use Illuminate\Http\JsonResponse;


class CostumerController extends Controller
{
    use ApiResponse; 

    protected $costumerService;

    public function __construct(CostumerService $costumerService)
    {
        $this->costumerService = $costumerService;
    }

    public function index() : JsonResponse
    {
        try {
            $costumers = $this->costumerService->getAllCostumers();
            return $this->success($costumers);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreCostumer $request): JsonResponse
    {
        try {
            
            $costumer = $this->costumerService->createCostumer($request->validated());

            //descricao
            //quantidade

            return $this->created($costumer);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $costumer = $this->costumerService->getCostumerById($id);
            if (!$costumer) {
                return $this->notFound();
            }
            return $this->success($costumer);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(StoreCostumer $request, int $id): JsonResponse
    {
        try {
            $costumer = $this->costumerService->getCostumerById($id);
            if (!$costumer) {
                return $this->notFound();
            }
            $updatedcostumer = $this->costumerService->updateCostumer($costumer, $request->validated());
            return $this->success($updatedcostumer);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->costumerService->deleteCostumer($id);
            return $this->success(null, 'costumer deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
