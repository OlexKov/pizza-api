<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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
}
