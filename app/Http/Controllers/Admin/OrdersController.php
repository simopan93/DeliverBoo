<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Order;
use App\Plate;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// if($_POST){
//     $name = $_POST['name'];
//     $lastname = $_POST['lastname'];
//     $address = $_POST['address'];
//     $city = $_POST['city'];
//     $cap = $_POST['cap'];
//     $province = $_POST['province'];
//     $email = $_POST['email'];
//     $note = $_POST['note'];
//     // $product = $_POST['product'];
//     // $quantity = $_POST['quantity'];
//     $total = $_POST['total'];
  
//     echo $name . $lastname . 'è felice';
//   }

class OrdersController extends Controller 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {
        $user = Auth::user();
        $restaurant= Restaurant::where('slug',$slug)->first();
        $orders = Order::where('restaurant_id',$restaurant->id)->with('plates')->get();
      
        
        
        return view('admin.orders.index',compact('restaurant','orders','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$idRestaurant)
    {
        $restaurant= Restaurant::where('id',$idRestaurant)->first();
        $data = $request->all();

        $newOrder = new Order();
        $newOrder->fill($data);
        $newOrder->slug = Order::generateSlug($newOrder->name);
        $newOrder->restaurant_id = $restaurant->id;
        $newOrder->save();

        return ;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
