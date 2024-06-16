<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected string $upload;
    protected string $url;
    public function __construct()
    {
        $protocol = isset($_SERVER['HTTPS']) &&
        $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $base_url = $protocol . $_SERVER['HTTP_HOST'] . '/';
        $this-> upload = env('UPLOAD_DIR');
        $this-> url = $base_url . $this->upload;

    }
    public function getall(): \Illuminate\Http\JsonResponse
    {
        $items = Category::all();
        foreach ($items as $item) {
            $item->image =$this->url . $item->image;
        }
        return response()->json($items) ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!file_exists(public_path($this->upload))) {
            mkdir(public_path($this->upload), 0777);
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $fileName = uniqid() . '.' .$ext;
            $destinationPath =public_path( $this->upload);
            $file->move($destinationPath,$fileName);
            $item = Category::create(['name' => $request->input('name') , 'image' => $fileName]);
            $item->image =$this->url . $item->image;
            return response()->json($item, 201);
         }
        else
            return response()->json("Image file not found", 404);
    }

    public function getById(int $categoryId): \Illuminate\Http\JsonResponse
    {
        $item = Category::find($categoryId);
        $item->image =$this->url . $item->image;
        return response()->json($item);
    }

    public function update(Request $request, Category $category)
    {
        //
    }

    public function delete(int $categoryId): \Illuminate\Http\JsonResponse
    {
        Category::destroy($categoryId);
        return response()->json(null, 204);
    }
}
