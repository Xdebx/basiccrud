<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cart 
{
    use HasFactory;

    // public $primaryKey = 'item_id';
    // public $table = 'item';
    // public $timestamps = false;

    // protected $fillable = ['description','sell_price','cost_price','img_path'
    // ];

    public function __construct($oldCart) {
        if($oldCart) {
            $this->items = $oldCart->items;
            $this->totalQty = $oldCart->totalQty;
            $this->totalPrice = $oldCart->totalPrice;
        }
   public function add($item, $id){
        //dd($this->items);
        $storedItem = ['qty'=>0, 'price'=>$item->sell_price, 'item'=> $item];
        if ($this->items){
            if (array_key_exists($id, $this->items)){
                $storedItem = $this->items[$id];
            }
        }
       //$storedItem['qty'] += $item->qty;
       $storedItem['qty']++;
        $storedItem['price'] = $item->sell_price * $storedItem['qty'];
        $this->items[$id] = $storedItem;
        $this->totalQty++;
        $this->totalPrice += $item->sell_price;
    }

       public function reduceByOne($id){
        $this->items[$id]['qty']--;
        $this->items[$id]['price']-= $this->items[$id]['item']['sell_price'];
        $this->totalQty --;
        $this->totalPrice -= $this->items[$id]['item']['sell_price'];
        if ($this->items[$id]['qty'] <= 0) {
            unset($this->items[$id]);
        }
        public function removeItem($id){
        $this->totalQty -= $this->items[$id]['qty'];
        $this->totalPrice -= $this->items[$id]['price'];
        unset($this->items[$id]);
    }

}

