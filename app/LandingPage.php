<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'organization_id','campaign_id','slug','title','layout_name','status','meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    public function sections()
    {
        return $this->hasMany(LandingPageSection::class, 'page_id')->orderBy('sort_order');
    }

    public function section($key)
    {
        $sec = $this->sections->firstWhere('section_key', $key);
        return $sec ? json_decode($sec->content_json, true) : null;
    }
}
