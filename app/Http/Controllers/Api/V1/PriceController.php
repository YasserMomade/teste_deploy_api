<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Price;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PriceService;
use App\Http\Requests\Price as StorePrice;
use App\Traits\ApiResponse;


class PriceController extends Controller
{
    use ApiResponse;
    
    protected $priceService;

    public function __construct(PriceService $priceService)
    {
        $this->priceService = $priceService;
    }

    public function index()
    {
        try {
            $prices = $this->priceService->getAllPrice();
            return $this->success($prices);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StorePrice $request)
    {
        try {
            $price = $this->priceService->createPrice($request->validated());
            return $this->success($price);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $price = $this->priceService->getPriceById($id);
            return $this->success($price);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(StorePrice $request, string $id)
    {
        try {
            $price = $this->priceService->updatePrice($id, $request->validated());
            return $this->success($price);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->priceService->deletePrice($id);
            return $this->success(null, 'Price deleted successfully');

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
