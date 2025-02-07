<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\History;
use App\Models\Like;
use App\Models\Product;
use App\Models\Review;
use App\Models\View;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        // گرفتن تمام محصولات
        $products = Product::all();
        return ProductResource::collection($products);
    }

    /**
     * Create a new product.
     *
     * @param ProductRequest $request
     * @return ProductResource
     */
    public function create(ProductRequest $request): ProductResource
    {
        $validatedData = $request->validated();
        $product = Product::create($validatedData);
        $product->addImage($request, $product);

        return new ProductResource($product);
    }

    /**
     * Display a single product by ID.
     *
     * @param int $id
     * @return JsonResponse|ProductResource
     */
    public function show(int $id): JsonResponse|ProductResource
    {
        try {
            $product = Product::findOrFail($id);
            return new ProductResource($product);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    /**
     * Update the specified product.
     *
     * @param ProductRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $oldPrice = $product->price;
            $validatedData = $request->validated();
            $product->update($validatedData);

            if ($oldPrice != $product->price) {
                History::create([
                    'product_id' => $product->id,
                    'price_history' => $oldPrice,
                ]);
            }

            $product->updatedImageIfExist($request, $product);
            $product->refresh();

            return response()->json(['product' => new ProductResource($product)]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('delete', $product);

            $product->deletedImageIfExist($product);
            $product->delete();


            return response()->json(['message' => "{$product->name} deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    /**
     * Increment product view count.
     *
     * @param Request $request
     * @param int $id
     * @return ProductResource
     */
    public function view(Request $request, int $id): ProductResource
    {
        $product = Product::findOrFail($id);
        $user = $product->getUser($request);

        View::create(['product_id' => $product->id, 'user_id' => $user->id]);

        $product->refresh();

        return new ProductResource($product);
    }

    /**
     * Add like to product.
     *
     * @param Request $request
     * @param int $id
     * @return ProductResource
     */
    public function like(Request $request, int $id): ProductResource
    {
        $product = Product::findOrFail($id);
        $user = $product->getUser($request);

        // Avoid duplicate likes
        $existingLike = Like::where('product_id', $product->id)->where('user_id', $user->id)->first();
        if (!$existingLike) {
            Like::create(['product_id' => $product->id, 'user_id' => $user->id]);
        }

        $product->refresh();

        return new ProductResource($product);
    }

    /**
     * Remove like from product.
     *
     * @param Request $request
     * @param int $id
     * @return ProductResource
     */
    public function disLike(Request $request, int $id): ProductResource
    {
        $product = Product::findOrFail($id);
        $user = $product->getUser($request);

        $like = Like::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete();
        }

        $product->refresh();

        return new ProductResource($product);
    }

    /**
     * Add review to product.
     *
     * @param Request $request
     * @param int $id
     * @return ProductResource
     */
    public function review(Request $request, int $id): ProductResource
    {
        $product = Product::findOrFail($id);
        $user = $product->getUser($request);

        Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        $product->refresh();

        return new ProductResource($product);
    }

    /**
     * Get product price history.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function histories(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $histories = History::where('product_id', $product->id)->get();

        return response()->json(['histories' => $histories]);
    }
}
