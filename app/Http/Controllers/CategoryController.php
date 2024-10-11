<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //

    public function index()
    {
        $categories = Category::get();

        return response()->json(["success" => true, "data" => $categories], 200);
    }

    public function show($categor_id)
    {
        $category = Category::findOrFail($categor_id)->first();

        if ($category) {
            return response()->json(["success" => true, "data" => $category], 200);
        }

        return response()->json(["success" => true, "message" => "category not found!"], 400);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'image' => 'required|string'
        ]);

       
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => "ocorreu um erro", "erro" => $validator->errors()]);
        }

        try {
           
            $category = new Category();

            $path = 'assets/uploads/category'.$category->image;

            if(File::exists($path)){
                File::delete();
            }

            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;

            try {
                $file->move('assets/uploads/category'.$filename);
            } catch (\Exception $th) {
                dd($th);
            }

            $category->image= $filename;
            $category->name=$request->name;
            $category->save();
            
            return response()->json(["success"=>true, "message"=>"category added successfully", "data"=>$category], 422);




            return response()->json(["success" => true, "message" => "category added successfully", "data" => $category], 201);
        } catch (\Exception $th) {
            return response()->json(["success" => false, "message" => "Fail add category ", "erro" => $th->getMessage()], 500);
        }
    }

    public function updateCategory(Request $request, $category_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,$category_id'
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => "some errors", "erro" => $validator->errors()]);
        }
        try {
            $category = Category::findOrFail($category_id);

            if($request->hasFile('image')){
                $path ='assets/uploads/categgory'.$category->image;

                if(File::exists($path)){
                    File::delete($path);
                }

                $file = $request->file('image');

                $ext = $file->getClientOriginalExtension();
                $filename = time().'.'.$ext;

                try {
                    $file->move('assets/uploads/category'.$filename);
                } catch (\Exception $th) {
                    dd($th);
                }

                $category->image = $filename;
            }

            $category->name = $request->name;
            $category->update();

            return response()->json(["success"=>true, "message"=>"Category update successfully"],200);

        
        } catch (\Throwable $th) {
            return response()->json(["success" => true, "message" => "Error updated category", "error" => $th->getMessage()], 500);
        }
    }

    public function deleteCategory($categor_id)
    {
        $category = Category::findOrFail($categor_id);

        if ($category) {
            $category->delete();
            return response()->json(["success" => true, "message" => "Category deleted successfully"]);
        }

        return response()->json(["success" => false, "message" => "Category not found!"]);
    }
}
