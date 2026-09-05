<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs'; // ✅ explicit

    protected $fillable = [
        'institute_id',
        'name',
        'description',
        'status'
    ];

    /**
     * Program belongs to Institute
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class, 'institute_id');
    }

    /**
     * Program has many Products
     * (Faculty / Courses handled via products)
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'program_id');
    }
}
