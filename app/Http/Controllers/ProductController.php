<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\History;
use App\Models\Like;
use App\Models\Product;
use App\Models\Review;
use App\Models\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        //
        $product=Product::all();
        // بازگشت مجموعه‌ای از منابع
        return ProductResource::collection($product);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return ProductResource
     */
    public function create(ProductRequest $request)
    {
        //
        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        // این کد متد 'create' را در UserProduct فراخوانی می‌کند
        //$this->authorize('create', $product);


        $product=Product::create($validatedData);
        $product->addImage($request,$product);

        return new ProductResource($product);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return ProductResource
     */
    public function show($id)
    {
        //
        //
        $product=Product::find($id);

        //$this->authorize('view', $product);
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        //
        $product=Product::find($id);

        // این کد متد 'update' را در UserProduct فراخوانی می‌کند
        //$this->authorize('update', $product);

// قیمت فعلی محصول را ذخیره می‌کنیم
        $oldPrice = $product->price;

        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $product->update($validatedData);

// اگر قیمت تغییر کرده باشد
        if ($oldPrice != $product->price) {
            // ایجاد یک رکورد جدید در تاریخچه
            $history = History::create([
                'product_id' => $product->id,
                'price_history' => $oldPrice, // قیمت قبلی را به عنوان تاریخچه ذخیره می‌کنیم
            ]);
        }


        $product->updatedImageIfExist($request,$product);

        // دوباره بارگیری کردن مدل از دیتابیس برای به‌روزرسانی اطلاعات
        $product->refresh();


        return response()->json([
            'product'=>new ProductResource($product),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(ProductRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        //
        //
        $product=Product::find($id);
        $this->authorize('delete', $product);
        $product->delete();
        $product->deletedImageIfExist($request,$product);
        return response()->json([
            'message'=>$product->name.'deleted',
        ]);
    }


    public function view(Request $request,$id): ProductResource
    {
        //
        $product=Product::find($id);
        $user=$product->getUser($request);

        $view=View::create(['product_id'=>$product->id,
            'user_id'=>$user->id]);

        $product->refresh();

        return new ProductResource($product);
    }
    public function like(Request $request,$id): ProductResource
    {
        //
        //
        $product=Product::find($id);
        $user=$product->getUser($request);

        $like=Like::create(['product_id'=>$product->id,
            'user_id'=>$user->id]);

        $product->refresh();

        return new ProductResource($product);
    }
    public function disLike(Request $request,$id): ProductResource
    {
        //
        //
        $product=Product::find($id);

        $user=$product->getUser($request);

        $like = Like::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();


        $dislike=$like->delete();

        $product->refresh();

        return new ProductResource($product);
    }
    public function review(Request $request,$id): ProductResource
    {
        //
        $product=Product::find($id);

        $user=$product->getUser($request);

        $review=Review::create([

            'product_id'=>$product->id,
            'user_id'=>$user->id,
            'rating'=>$request->rating,
            'review'=>$request->review,

        ]);

        $product->refresh();

        return new ProductResource($product);

    }
    public function histories($id): \Illuminate\Http\JsonResponse
    {
        //
        //
        $product=Product::find($id);

        $histories=History::where('product_id',$product->id)->get();

        return response()->json([

            'histories'=>$histories

        ]);
    }
}
