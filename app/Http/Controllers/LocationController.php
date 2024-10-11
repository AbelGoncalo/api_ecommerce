<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'street'=>'required',
            'building'=>'required',
            'area'=>'required'
        ]);

        if($validator->fails())
        {
            return response()->json(["success"=>false, "message"=>"Fail error", "error"=>$validator->errors()], 422);
        }

        $location = Location::create([
            'area'=>$request->area,
            'street'=>$request->street,
            'building'=>$request->building,
            'user_id'=>Auth::user()->id,
        ]);
        return response()->json(["success"=>true, "message"=>"Location added successfully", "data"=>$location], 201);
    }

    public function update(Request $request, $location_id)
    {
        $validator = Validator::make($request->all(),[
            'street'=>'required',
            'building'=>'required',
            'area'=>'required'
        ]);

        if($validator->fails())
        {
            return response()->json(["success"=>false, "message"=>"Fail error", "error"=>$validator->errors()], 422);
        }

        $updated = Location::findOrFail($location_id)->update([
            'area'=>$request->area,
            'street'=>$request->street,
            'building'=>$request->building, 
        ]);

        return response()->json(["success"=>true, "message"=>"Lacation update ssuccessfully", "data"=>$updated],200);
    }

    public function destroy($location_id)
    {
        $location= Location::findOrFail($location_id);

        if($location)
        {

            return response()->json(["success"=>true, "message"=>"Locatio deleted successfully"],200);
        }

        return response()->json(["success"=>true, "message"=>"Locatio not found"],400);


    }
}
