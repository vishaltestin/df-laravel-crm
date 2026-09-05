<?php
namespace App;
 
use Illuminate\Database\Eloquent\Model;

class LeadPipelineStage extends Model
{
    protected $table = 'pipeline_stages';

    protected $fillable = [
       
 	'code','name','probability','sort_order','lead_pipeline_id','is_final','created_at','updated_at'
    ];
}
