<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Category;

class CategoryController extends Controller
{
    use ApiResponse;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
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
            $category = $this->categoryService->createCategory($request->all());
            return $this->success($category);

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
            $category = $this->categoryService->updateCategory($id, $request->all());
            return $this->success($category);

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