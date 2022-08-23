@component('mail::message')

<img src="{{ asset('assets/images/bb-logo.png') }}" width="60px" style="border-radius:5px;">

Hi {{ $user->name }}

Thank you for creating a Baby Block account. To continue, please confirm your email address by clicking the button below.

@component('mail::button', ['url' => route('email-verification.check', $user->verification_token) . '?email=' . urlencode($user->email) ])
Confirm email address
@endcomponent

Thanks,<br>
Baby Block Team
@endcomponent
