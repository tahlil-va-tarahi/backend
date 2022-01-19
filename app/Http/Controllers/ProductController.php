<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $category
     * @return JsonResponse
     */
    public function index(string $category = ''): JsonResponse
    {
        $products = Product::latest('id')->paginate(12);

        if ($category) {
            $products = Product::where('category_id', $category)->paginate(12);
        }

        return response()->json([
            'products' => ProductResource::collection($products)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:16|min:3',
            'description' => 'max:512',
            'image' => 'required|image|mimes:png,jpg,jpeg,gif',
            'price' => 'required|regex:/^\d{1,13}(\.\d{1,3})?$/',
            'category_id' => ['required', Rule::in([1, 2, 3])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->getMessageBag()], 400);
        }

        $imageName = $request->file('image')->getClientOriginalName();
        $filename = pathinfo($imageName, PATHINFO_FILENAME);
        $extension = pathinfo($imageName, PATHINFO_EXTENSION);

        $inputs = $request->all();

        $publicPath = public_path($pp = "products/" . rand() . rand() . $filename . '.' . $extension);
        $storagePath = public_path($sp = "products/" . rand() . rand() . $filename . '.' . $extension);

        if (!file_exists(public_path("products/"))) {
            mkdir(public_path("products/"), 0755, true);
        }
//        if (!file_exists(storage_path("products/"))) {
//            mkdir(storage_path("products/"), 0755, true);
//        }

        $image = Image::make($request->file('image'));

        $width = $image->width();
        $height = $image->height();

        $image->save($storagePath);
        $image->resize($width / 4, $height / 4)->save($publicPath);

        $inputs['source_url'] = $sp;
        $inputs['thumbnail_url'] = $pp;

        $product = Product::create($inputs);

        return response()->json([
            'product' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'product' => new ProductResource($product)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'max:16|min:3',
            'description' => 'max:512',
            'price' => 'regex:/^\d{1,13}(\.\d{1,3})?$/',
            'category_id' => [Rule::in([1, 2, 3])],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }

        $product->update($request->all());

        return response()->json([
            'message' => 'اطلاعات با موفقیا بروزرسانی شد.',
            'product' => $product
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $isDeletePublicPic = File::delete(public_path($product->thumbnail_url));
        $isDeleteStoragePic = File::delete(public_path($product->source_url));

        $product->users()->detach();

        if ($isDeletePublicPic && $isDeleteStoragePic) {
            $product->delete();
            return response()->json([
                'message' => 'محصول با موفقیت حذف شد.'
            ]);
        }
        return response()->json([
            'error' => 'عملیات خطا با خطا مواجه شد.'
        ], 500);
    }

    public function downloadProduct(Product $product)
    {
        $user = auth()->user();
        if (!$user->products()->get()->contains($product)) {
            return response()->json(['error' => 'اجازه دسترسی وجود ندارد.'], 403);
        }

        return response()->download(storage_path($product->source_url), $product->title);
    }
}
