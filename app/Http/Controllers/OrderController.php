<?php

namespace App\Http\Controllers;

use App\Models\{Location, Order, OrderItem, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
   
    public function index()
    {
        $orders = Order::with('user')->get();

        if ($orders) {
            foreach ($orders as $order) {

                foreach ($order->items as $order_items) {
                    $product = Product::where('id', $order_items->product_id)->pluck('name');
                    $order_items->product_name = $product['0'];
                }
            }
            return response()->json(["success" => true, "data" => $order], 200);
        } else return response()->json(["message" => "there is no orders"]);
    }

    public function show($order_id)
    {
        $order = Order::find($order_id)->first();
        return response()->json(["success" => true, "data" => $order], 200);
    }

    public function store(Request $request)
    {
        $location = Location::where('usaer_id', Auth::user()->id)->first();

        $validator = Validator::make($request->all(), [
            'order_items' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
            'product_id' => 'required',
            'date_of_delivery' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => "some errors", "error" => $validator->errors()], 422);
        }


        try {

            DB::beginTransaction();
            $order = Order::create([
                'user_id' => Auth::user()->id,
                'product_id' => $request->product_id,
                'location_id' => $location->id,
                'total_price' => $request->total_price,
                'quantity' => $request->quantity,
                'date_of_delivery' => $request->date_of_delivery,
            ]);

            foreach ($request->order_items as $order_item) {
                $item = OrderItem::create([
                    'order_id' => $order->id,
                    'price' => $order_item['price'],
                    'product_id' => $order_item['product_id'],
                    'quantity' => $order_item['quantity'],
                    'price' => $order_item['price'],

                ]);

                $product = Product::where('id', $order_item['product_id'])->first();
                $product->quantity -= $order_item['quantity'];
                $product->save();
            }

            DB::commit();

            return response()->json(["success" => true, "message" => "order is added successfully", "data" => $item], 201);
        } catch (\Exception $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["success" => false, "some error" => $th], 500);
        }
    }

    //obter todos o itens de um pedido
    public function get_order_items($order_id)
    {
        $order_items = OrderItem::where('order_id', $order_id)->get();

        if ($order_items) {
            foreach ($order_items as $order_item) {
                $product = Product::where('id', $order_item->product_id)->pluck('name');
                $order_item->product_name = $product['0'];
            }
            return response()->json(["success" => true, "data" => $order_items], 200);
        } else return response()->json(["success" => false, "message" => "Items not found"], 400);
    }


    //obter todos os pedidos de usuario
    public function get_user_orders($user_id)
    {
        $orders = Order::where('user_id', $user_id)
            ::with('items', function ($query) {
                $query->orderBy('created_at', 'desc');
            })->get();

        if($orders){

            foreach($orders as $order)
            {
                foreach($order->items as $order_items){

                }
                $product = Product::where('id', $order_items->product_id)->pluck('name');
                $order_items->product->name = $product['0'];
            }

            return response()->json(["success"=>true, "data"=>$orders],200);
        }

        return response()->json(["success"=>false, "message"=>"no orders found for this user"],400);

    }

    public function change_order_status(Request $request, $order_id)
    {
        $order = Order::find($order_id);

        if($order){
            $order->update(['status'=>$request->status]);
            return response()->json(["sucess"=>true, "message"=>"Status cheanged successfully"]);
        }else response()->json(["success"=>false, "message"=>"Order not found"]);
    }
}
