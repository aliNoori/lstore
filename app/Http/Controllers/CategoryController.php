<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        //
        $categories=Category::all();
        return CategoryResource::collection($categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return CategoryResource
     */
    public function create(CategoryRequest $request)
    {
        //
        //
        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $category=Category::create($validatedData);

        $category->addimage($request,$category);

        return new CategoryResource($category);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return CategoryResource
     */
    public function show($id)
    {
        //
        $category=Category::find($id);

        //$this->authorize('view', $category);
        return new CategoryResource($category);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, $id)
    {
        //
        //
        $category=Category::find($id);

        // این کد متد 'update' را در UserProduct فراخوانی می‌کند
        //$this->authorize('update', $category);

        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $category->update($validatedData);

        $category->updatedImageIfExist($request,$category);

        // دوباره بارگیری کردن مدل از دیتابیس برای به‌روزرسانی اطلاعات
        $category->refresh();


        return response()->json([
            'category'=>new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(CategoryRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        //
        //
        //
        $category=Category::find($id);
        //$this->authorize('delete', $category);
        $category->delete();
        $category->deletedImageIfExist($request,$category);
        return response()->json([
            'message'=>$category->name.'deleted',
        ]);
    }
}
