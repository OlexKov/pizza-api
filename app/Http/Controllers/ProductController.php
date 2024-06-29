<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use function MongoDB\BSON\toJSON;


include(app_path().'/Common/CommonFunctions.php');

class ProductController extends Controller
{
    protected string $upload;

    public function __construct()
    {
        $this->upload = env('UPLOAD_DIR');
    }

    public function getall()
    {
        $items = Product::with(['category', 'product_images'])->get();
        $data = json_encode($items);
        $dataSize = strlen($data);
        return response($data, 200)
            ->header('Content-Type', 'application/json')
            ->header('Content-Length', $dataSize)
            ->header('Accept-Ranges', 'bytes');
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!file_exists(public_path($this->upload))) {
            mkdir(public_path($this->upload), 0777);
        }
//        $input_data = $request->all();
//        $validator = Validator::make(
//            $input_data, [
//            'name' => 'required|max:128',
//            'description' => 'required|max:3000',
//            'price' => 'required|numeric|gte:0',
//            'quantity' => 'required|numeric|gte:0',
//            'category_id' => 'required|numeric|gte:0',
//            'images.*' => 'required|mimes:jpg,jpeg,png,bmp,webp|max:20000'
//        ], [
//                'images.*.required' => 'Please upload an image',
//                'images.*.mimes' => 'Only jpeg,png,webp and bmp images are allowed',
//                'images.*.max' => 'Sorry! Maximum allowed size for an image is 20MB',
//                'name.*.required' => 'Name is required',
//                'name.*.max' => 'Name must have les 128 symbols',
//                'description.*.required' => 'Description is required',
//                'description.*.max' => 'Description must have les 3000 symbols',
//                'price.*.required' => 'Price is required',
//                'price.*.gte' => 'Price must be greater or equal 0',
//                'price.*.numeric' => 'Price must have numeric type',
//                'quantity.*.required' => 'Quantity is required',
//                'quantity.*.gte' => 'Quantity must be greater or equal 0',
//                'quantity.*.numeric' => 'Quantity must have numeric type',
//                'category_id.*.required' => 'category_id is required',
//                'category_id.*.gte' => 'category_id must be greater or equal 0',
//                'category_id.*.numeric' => 'category_id must have numeric type',
//            ]
//        );
//
//        if ($validator->fails()) {
//            return response()->json(array(
//                'success' => false,
//                'errors' => $validator->getMessageBag()->toArray()
//            ), 400);
//        }
        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'category_id' => $request->input('category_id'),
        ]);
        $images = $request->file('images');

        for ($i=0;$i<count($images);$i++){
            ProductImage::create([
                'product_id' => $product->id,
                'priority' => $i,
                'image' => saveImage($images[$i])
            ]);
        }
//        foreach ($images as  $value) {
//            ProductImage::create([
//                'product_id' => $product->id,
//                'priority' => 1,
//                'image' => saveImage($value)
//            ]);
//        }
        return response()->json($product, 201);
    }

}



