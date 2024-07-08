<?php

namespace App\Http\Controllers;

use App\Common\ImageWorker;
use App\Models\Category;
use Illuminate\Http\Request;



class CategoryController extends Controller
{
    protected string $upload;
    public function __construct()
    {
        $this-> upload = env('UPLOAD_DIR');
    }
    public function getall(): \Symfony\Component\HttpFoundation\Response
    {
        $items = Category::all();
        $data = json_encode($items);
        $dataSize = strlen($data);


        return response($data, 200)
            ->header('Content-Type', 'application/json')
            ->header('Content-Length', $dataSize)
            ->header('Accept-Ranges', 'bytes');

//      $response = new StreamedResponse(function () use ($data, $dataSize) {
//            $chunkSize =ceil($dataSize / 10);
//            $totalChunks = ceil($dataSize / $chunkSize);
//
//            for ($i = 0; $i < $totalChunks; $i++) {
//                $start = $i * $chunkSize;
//                $chunk = substr($data, $start, $chunkSize);
//                echo $chunk;
//                ob_flush();
//                flush();
//                usleep(80000);
//            }
//        });
//
//        $response->headers->set('Content-Type', 'application/json');
//        $response->headers->set('Content-Length', $dataSize);
//        $response->headers->set('Accept-Ranges', 'bytes');
//        return $response;

    }

    public function getList(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $perPage = intval($request->query('perPage',2));
        $search = $request->query('search');
        $page = $request->query('page',1);
        $query = Category::query();
        if($search){
            $query-> where('name', 'like', '%'.$search.'%');
        }
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        $json = json_encode($data);
        $dataSize = strlen($json);
        return response($json, 200)
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
            $fileName =ImageWorker::save($file);
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

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $item = Category::find($id);
        if($item && $request->input('name') != ''){
            if ( $request->hasFile('image') ) {
                $file = $request->file('image');
                ImageWorker::delete($id);
                $item->image = ImageWorker::save($file);
            }
            $item->name = $request->input('name');
            $item->save();
            return response()->json([
                'message' => 'Category updated successfully!',
                'category' => $item
            ], 200);
        }
        else
            return response()->json("Bad request", 400);
    }

    public function delete(int $id): \Illuminate\Http\JsonResponse
    {
        deteteImages($id);
        Category::destroy($id);
        return response()->json(null, 204);
    }
}
