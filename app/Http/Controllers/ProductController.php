<?php

namespace App\Http\Controllers;

use App\Common\ImageWorker;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $input_data = $request->all();
        $validator = Validator::make(
            $input_data, [
            'name' => 'required|max:128',
            'description' => 'required|max:3000',
            'price' => 'required|numeric|gte:0',
            'quantity' => 'required|numeric|gte:0',
            'category_id' => 'required|numeric|gte:0',
            'images' => 'required',
            'images.*' => 'required|file|mimes:jpg,jpeg,png,bmp,webp|max:20000'
        ]
        );

        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'category_id' => $request->input('category_id'),
        ]);

        foreach ($request->file('images') as $key=> $value) {
            ProductImage::create([
                'product_id' => $product->id,
                'priority' => $key,
                'name' => ImageWorker::save($value)
            ]);
        }
        return response()->json($product, 201);
    }

}



