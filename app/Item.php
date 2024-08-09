<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $fillable = ['name','code','cost_prices','sell_price','unit_id','category_id','quantity','description'];
}
