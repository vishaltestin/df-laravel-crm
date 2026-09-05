 
    <meta charset="utf-8">
    <title>{{ $form->title ?? 'Application Form' }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
@include($formView, [
    'leadKey'  => $leadKey,
    'form'     => $form,
    'response' => $response
])

    {{-- COMMON CSS --}}
 