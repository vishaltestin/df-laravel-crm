<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class LandingPageSection extends Model
{
    protected $table = 'landing_page_sections';

    protected $fillable = ['page_id','section_key','content_json','sort_order'];
}
