<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    protected $fillable = [
        'sku','name','description','quantity','price','organization_id','program_id'
    ];

    public function inventory()
    {
        return $this->hasOne(ProductInventory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags','product_id','tag_id');
    }

    public function activities()
    {
        return $this->hasMany(ProductActivity::class);
    }
	public function program()
	{
		return $this->belongsTo(Program::class, 'program_id');
	}
 public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
	public function products()
{
    return $this->hasMany(Product::class, 'program_id');
}

 
}
