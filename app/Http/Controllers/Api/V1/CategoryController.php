<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\PriceService;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    use ApiResponse;

    protected $categoryService;

    public function __construct(CategoryService $categoryService, PriceService $priceService)
    {
        $this->categoryService = $categoryService;
        $this->priceService = $priceService;
    }

    public function index()
    {
        try {
            $categories = $this->categoryService->getAllCategory();
            return $this->success($categories);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $category = $this->categoryService->createCategory([
                    "category" => $request->category
                ]);

                $price = $this->priceService->createPrice([
                    "amount" => $request->amount,
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

    public function update(Request $request, string $id)
    {
        try {
            $result = DB::transaction(function () use ($request, $id) {
                $category = $this->categoryService->updateCategory($id, [
                    "category" => $request->category
                ]);

                $price_id = $this->priceService->getPriceByCategoryId($category->id);

                $price = $this->priceService->updatePrice($price_id, [
                    "amount" => $request->amount,
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