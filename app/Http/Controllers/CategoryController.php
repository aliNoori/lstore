<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * نمایش لیست دسته‌بندی‌ها
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function list(): AnonymousResourceCollection|JsonResponse
    {
        try {
            // دریافت تمامی دسته‌بندی‌ها
            $categories = Category::all();
            return CategoryResource::collection($categories);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت دسته‌بندی‌ها',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ایجاد دسته‌بندی جدید
     *
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function create(CategoryRequest $request): JsonResponse
    {
        try {
            // دریافت داده‌های معتبر
            $validatedData = $request->validated();

            // ایجاد دسته‌بندی
            $category = Category::create($validatedData);

            // اضافه کردن تصویر به دسته‌بندی
            $category->addImage($request, $category);

            return response()->json([
                'success' => true,
                'category' => new CategoryResource($category)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ایجاد دسته‌بندی',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * نمایش جزئیات دسته‌بندی
     *
     * @param int $id شناسه دسته‌بندی
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'دسته‌بندی مورد نظر یافت نشد'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'category' => new CategoryResource($category)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت اطلاعات دسته‌بندی',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * به‌روزرسانی دسته‌بندی
     *
     * @param CategoryRequest $request
     * @param int $id شناسه دسته‌بندی
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'دسته‌بندی مورد نظر یافت نشد'
                ], 404);
            }

            // به‌روزرسانی داده‌ها
            $validatedData = $request->validated();
            $category->update($validatedData);

            // به‌روزرسانی تصویر در صورت وجود
            $category->updatedImageIfExist($request, $category);

            // بارگذاری دوباره مدل
            $category->refresh();

            return response()->json([
                'success' => true,
                'category' => new CategoryResource($category)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در به‌روزرسانی دسته‌بندی',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف دسته‌بندی
     *
     * @param CategoryRequest $request
     * @param int $id شناسه دسته‌بندی
     * @return JsonResponse
     */
    public function delete(CategoryRequest $request, int $id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'دسته‌بندی مورد نظر یافت نشد'
                ], 404);
            }

            // حذف دسته‌بندی
            $category->delete();

            // حذف تصویر در صورت وجود
            $category->deletedImageIfExist($request, $category);

            return response()->json([
                'success' => true,
                'message' => 'دسته‌بندی با موفقیت حذف شد'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف دسته‌بندی',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
