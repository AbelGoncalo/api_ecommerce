<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    //marca
    public function index()
    {
        $brands = Brand::get();
        return response()->json(["date" => $brands], 200);
    }

    public function show($brand_id)
    {
        $brand = Brand::findOrFail($brand_id);

        if ($brand) {
            return response()->json(["data" => $brand], 200);
        } else return response()->json(["message" => "Brand not found"]);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:brands,name'
            ]);

            if ($validator->fails()) {
                return response()->json(["message" => "Aconteceu erros", "error" => $validator->errors()], 422);
            }

            $brand = Brand::create([
                'name' => $request->name
            ]);

            return response()->json(["success" => true, "message" => "Brand added", "data" => $brand], 201);
        } catch (\Exception $th) {

            return response()->json(["success" => false, "message" => "Erro ao realizar a opercao", "erro" => $th->getMessage()], 500);
        }
    }

    public function updateBrand(Request $request, $brand_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:brands,name,' . $brand_id
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => "ocrreu um erro", "erro" => $validator->errors()]);
        }

        try {

            $brand = Brand::findOrFail($brand_id)->update([
                'name' => $request->name
            ]);

            if ($brand) {
                return response()->json(["success" => true, "message" => "Brand update successfully"], 200);
            }

            return response()->json(["success" => false, "message" => "Dont  update successfully"], 200);
        } catch (\Exception $th) {

            return response()->json(["success" => false, "message" => "Fail to update", "erro" => $th->getMessage()], 500);
        }
    }

    public function deleteBrand($brand_id)
    {
        try {
            $brand = Brand::findOrFail($brand_id);
          
            if($brand)
            {
                $brand->delete();
                return response()->json(["success"=>true, "message"=>"brand deleted successfully"], 200);
            }

            return response()->json(["success"=>false, "message"=>"brand not found  "], 400);


        } catch (\Exception $th) {
            return response()->json(["success"=>false, "message"=>"Error", "error"=>$th->getMessage()],500);
        }
    }
}
