<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Cart;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function __construct()
    // {
    //     if(Auth::check())
    //     {
    //         return redirect('item');
    //        return redirect()->route('user.signin');
    //     }
    // }

    public function index()
    {
       if (Auth::check())
       {
        $items = Item::all();
        return view('shop.index', compact('items'));
           
        }
        return redirect()->route('user.signin');
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
    }
    
    public function getAddToCart(Request $request , $id){
        $item = Item::find($id);
        $oldCart = Session::has('cart') ? $request->session()->get('cart'): null;

        $cart = new Cart($oldCart);
        $cart->add($item, $item->item_id);
        //$request->session()->put('cart', $cart);
        Session::put('cart', $cart);
        $request->session()->save();
        dd(Session::all());
    }

    public function getCart() {
        if (!Session::has('cart')) {
            return view('shop.shopping-cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        return view('shop.shopping-cart', ['items' => $cart->items, 'totalPrice' => $cart->totalPrice]);
    }

    public function getRemoveItem($id){
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);
        if (count($cart->items) > 0) {
            Session::put('cart',$cart);
        }else{
            Session::forget('cart');
        }
         return redirect()->route('item.shoppingCart');
    }

     public function postCheckout(Request $request){
        if (!Session::has('cart')) {
            return redirect()->route('item.shoppingCart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        // dd($cart);
        try {
            DB::beginTransaction();
            $order = new Order();
            $customer =  Customer::where('user_id',Auth::id())->first();
            // dd($cart->items);
            // $customer->orders()->save($order);
            $order->customer_id = $customer->customer_id;
            $order->date_placed = now();
            $order->date_shipped =Carbon::now()->addDays(5);
            $order->shipvia = $request->shipper_id;
            $order->shipping = $request->shipping;
            $order->status = 'Processing';
            $order->save();
            // dd($cart->items);
            foreach($cart->items as $items){
                $id = $items['item']['item_id'];
                // dd($id);
                DB::table('orderline')->insert(
                    ['item_id' => $id, 
                     'orderinfo_id' => $order->orderinfo_id,
                     'quantity' => $items['qty']
                    ]
                    );
                // $order->items()->attach($id,['quantity'=>$items['qty']]);
                $stock = Stock::find($id);
                $stock->quantity = $stock->quantity - $items['qty'];
                $stock->save();
            }
            // dd($order);
        }
        catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            // dd($order);
            return redirect()->route('item.shoppingCart')->with('error', $e->getMessage());
        }
        DB::commit();
        Session::forget('cart');
        return redirect()->route('item.index')->with('success','Successfully Purchased Your items!!!');
    }

}
