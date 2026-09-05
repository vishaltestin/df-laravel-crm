<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebForm extends Model
{
 protected $fillable = [
    'form_id',
    'title',
    'description',
    'submit_button_label',
    'submit_success_action',
    'submit_success_content',
    'create_lead',
    'background_color',
    'form_background_color',
    'form_title_color',
    'form_submit_button_color',
    'attribute_label_color',
    'organization_id',
    'lead_source_id',
    'lead_campaign_id',
    'pipeline_id',
    'type_id',
    'attr_programme_check',
    'view_count',
    'submit_count',
    'inbound_call',
    'outbound_call',
    'campaign_call',
    'alternate_mobile',
    'institute_applying_for',
    'faculty',
    'extra_fields_json','application_form_id',
	'created_by'
];


    public function attributes()
    {
        return $this->hasMany(WebFormAttribute::class);
    }
}
