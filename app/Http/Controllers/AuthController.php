<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => "Aconteceu erros", "error" => $validator->errors()], 422);
        }

        

        if (!$token = auth()->attempt( $validator->validated())) {
            return response()->json(["message" => "unauthorized"], 422);
        }



        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json(["message" => "User successfully registed", "User" => $user], 201);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(["message" => "User successfully signed out "]);
    }

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    protected function createNewToken($token)
    {
        return response()->json([
            "access_token" => $token,
            "token_type" => "bearer",
            "expires_in" => auth()->factory()->getTTL() * 120,
            "User" => auth()->user()
        ]);
    }
}
