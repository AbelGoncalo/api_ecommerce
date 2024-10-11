<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::get();

        if ($products) {
            return response()->json(["success" => true, "data" => $products], 200);
        }

        return response()->json(["success" => true, "message" => "np products"]);
    }

    public function show($product_id)
    {
        $product = Product::find($product_id);

        if ($product) {
            return response()->json(["success" => true, "data" => $product], 200);
        } else return response()->json(["product was not found"]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
            'descount' => 'required|numeric',
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => "some errors", "error" => $validator->errors()], 422);
        }

        $filename = null;
        if ($request->hasFile('image')) {
            $path = 'assets/uploads/products';
            if (File::exists($path)) {
                File::delete();
            }

            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;

            try {
                $file->move('assets/uploads/products' . $filename);
            } catch (FileException $th) {
                dd($th);
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'amount' => $request->amount,
            'descount' => $request->descount,
            'image' => $filename
        ]);

        return response()->json(["success" => true, "message" => "product added successfully", "data" => $product], 201);
    }

    public function update(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
            'descount' => 'required|numeric',
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => "some errors", "error" => $validator->errors()], 422);
        }

        $filename = null;
        if ($request->hasFile('image')) {
            $path = 'assets/uploads/products';
            if (File::exists($path)) {
                File::delete();
            }

            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;

            try {
                $file->move('assets/uploads/products' . $filename);
            } catch (FileException $th) {
                dd($th);
            }
        }

        $product = Product::find($product_id)->update($request->all());

        return response()->json(["success" => true, "message" => "product update ", "data" => $product], 200);
    }

    public function destroy($product_id)
    {
        $product = Product::find($product_id);

        if ($product) {
            $product->delete();
            return response()->json(["success" => true, "message" => "product deleted "], 200);
        } else
            return response()->json(["success" => false, "message" => "product not found"], 400);
    }
}
