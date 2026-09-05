<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';   // your table name

    protected $fillable = [
        'name',
         'id'
    ];

    // If table has no timestamps (created_at / updated_at)
    public $timestamps = false;

    // Relationship: Country → States
    public function states()
    {
        return $this->hasMany(State::class, 'country_id', 'id');
    }
}
