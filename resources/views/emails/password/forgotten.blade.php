@component('mail::message')

<img src="{{ asset('assets/images/bb-logo.png') }}" width="60px" style="border-radius:5px;">

Hi {{ $user->name }}

You have submitted a password reset request. If it was you, then confirm the password reset by click below button.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

If it wasn't you please ignore this email and make sure you can still login to your account.

Thanks,<br>
Baby Block Team
@endcomponent
