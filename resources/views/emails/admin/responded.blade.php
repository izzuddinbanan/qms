@component('mail::message')

<img src="{{ asset('assets/images/bb-logo.png') }}" width="60px" style="border-radius:5px;">

{!! $content !!}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
