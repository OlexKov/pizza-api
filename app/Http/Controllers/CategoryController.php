<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CategoryController extends Controller
{
    protected string $upload;

    public function __construct()
    {
        $this-> upload = env('UPLOAD_DIR');
    }
    public function getall(): \Illuminate\Http\JsonResponse
    {
        $items = Category::all();
        return response()->json($items) ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!file_exists(public_path($this->upload))) {
            mkdir(public_path($this->upload), 0777);
        }
        if ($request->hasFile('image') && $request->input('name') != '') {
            $file = $request->file('image');
            $fileName = uniqid() . '.webp';
            $manager = new ImageManager(new Driver());
            $sizes = [50,150,300,600,1200];
            foreach ($sizes as $size) {
                $imageSave = $manager->read($file);
                $imageSave->scale(width: $size);
                $path = public_path($this->upload.$size."_".$fileName);
                $imageSave->toWebp()->save($path);
            }
            $item = Category::create(['name' => $request->input('name') , 'image' => $fileName]);
            return response()->json($item, 201);
         }
        else
            return response()->json("Bad request", 400);
    }

    public function getById(int $categoryId): \Illuminate\Http\JsonResponse
    {
        $item = Category::find($categoryId);
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
