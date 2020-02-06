@component('mail::message')

# Exception throw!

**url:** {!! $data['url'] !!}

**message:** {!! $data['message'] !!}  

**status:** {{ $data['status'] }}

**statusMessage:** {{ $data['statusMessage'] }}

**random_string:** {{ $data['random_string'] }}

**log_file:** `{{ $data['log_file'] }}`

**server_address:** `{{ $data['server_address'] }}`

@component('mail::button', ['url' => ''])
Button example
@endcomponent

@component('mail::panel', ['url' => ''])
Panel example
@endcomponent

@endcomponent