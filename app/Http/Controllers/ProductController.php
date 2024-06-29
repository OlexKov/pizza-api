<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected string $upload;
    public function __construct()
    {
        $this-> upload = env('UPLOAD_DIR');
    }
public function getall()
{
    $items = Product::with(['category','product_images'])->get();
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
        if ($request->hasFile('image') && $request->input('name') != '') {
            $file = $request->file('image');
            $fileName = $this->saveImages($file);
            $item = Category::create(['name' => $request->input('name') , 'image' => $fileName]);
            return response()->json($item, 201);
        }
        else
            return response()->json("Bad request", 400);
    }


}
