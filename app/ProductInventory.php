<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class ProductInventory extends Model
{
    protected $fillable = ['in_stock','allocated','product_id','warehouse_id','warehouse_location_id'];
}
