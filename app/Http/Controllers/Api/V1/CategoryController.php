<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\PriceService;
use App\Http\Requests\Category\StoreCategory;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    protected $categoryService;

    public function __construct(CategoryService $categoryService, PriceService $priceService)
    {
        $this->categoryService = $categoryService;
        $this->priceService = $priceService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $categories = $this->categoryService->getAllCategory($request);
            return $this->success($categories);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreCategory $request)
    {
        $data = $request->validated();

        try {
            $result = DB::transaction(function () use ($data) {
                $category = $this->categoryService->createCategory([
                    "category" => $data['category']
                ]);

                $price = $this->priceService->createPrice([
                    "amount" => $data['amount'],
                    "min_weight" => 0,
                    "max_weight" => 0,
                    "category_id" => $category->id
                ]);

                return ['category' => $category, 'price' => $price];
            });

            return $this->success($result);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            return $this->success($category);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(StoreCategory $request, string $id)
    {
        $data = $request->validated();
        try {
            $result = DB::transaction(function () use ($data, $id) {
                $category = $this->categoryService->updateCategory($id, [
                    "category" => $data['category']
                ]);

                $price_id = $this->priceService->getPriceByCategoryId($category->id);

                $price = $this->priceService->updatePrice($price_id, [
                    "amount" => $data['amount'],
                    "min_weight" => 0,
                    "max_weight" => 0,
                ]);

                return ['category' => $category, 'price' => $price];
            });

            return $this->success($result);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->categoryService->deleteCategory($id);
            return $this->success("Category deleted successfully");

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}