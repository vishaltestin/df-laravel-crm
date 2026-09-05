@if(!empty($content['form_id']))

<iframe 
    src="{{ url('/webform/' . $content['form_id']) }}" 
    style="width:100%;height:700px;border:0;overflow:hidden;">
</iframe>

@endif
