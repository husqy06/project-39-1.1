<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Dish;
use App\Order;
use App\Dish_Order;
use App\DishOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $user = Auth::user();
        if($user->id === 1){
            $orders = Order::all();
            // $i = 0;
            $data = $orders;
        }else {

            $orders=Order::whereHas('dishes',function($q ) use ($user) {
                $q->where('user_id', $user['id']);
            })->orderBy('created_at', 'desc')->get();
            $data = $orders;
        }
        return view('admin.orders.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $dishes = Dish::where('user_id', $user->id)->get();
        return view('admin.orders.create', compact('dishes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $data = $request->all();
        $new_order = new Order();
        $new_order->fill($data);
        $new_order->save();
        $new_order->dishes()->attach($data['dish']);

        return redirect()->route('admin.orders.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $order = Order::find($id);
        $data = [$order];
        $quanto=DishOrder::where('order_id',  $order['id'])->get();
    

        $carrello=Dish::whereHas('orders',function($q ) use ($order) {
            
            $q->where('order_id', $order['id']);
        })->get();
       
        return view('admin.orders.show', compact('carrello','quanto', 'order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   

        $orders = Order::find($id);
        $user = Auth::user();
        $dishes = Dish::where('user_id', $user->id)->get();
        return view('admin.orders.edit', compact('orders', 'dishes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->all();
        $order->update($data);
        if(array_key_exists('dish', $data)){
            $order->dishes()->sync($data['dish']);
        }else{
            $order->dishes()->sync([]);
        }
        return redirect()->route('admin.orders.index');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->dishes()->detach($order->id);
        $order = Order::findOrFail($order->id);
        $order->delete();
        return redirect()->route('admin.orders.index');

    }
}
