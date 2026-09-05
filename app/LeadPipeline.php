<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadPipeline extends Model
{
    protected $table = 'pipelines';

    protected $fillable = [

 	'name','is_default','rotten_days','created_at','updated_at','organization_id'

    ];

   public function stages()
{
    return $this->hasMany(LeadPipelineStage::class, 'lead_pipeline_id', 'id')
                ->orderBy('sort_order', 'ASC');
}

}
