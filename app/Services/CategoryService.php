<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function getAllCategory()
    {
        return Category::all();
    }

    public function getCategoryById(string $id)
    {
        return Category::findOrFail($id);
    }

    public function updateCategory(string $id, array $data)
    {
        $category = Category::findOrFail($id);
        $category->update($data);

        return $category;
    }

    public function deleteCategory(string $id)
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }
}