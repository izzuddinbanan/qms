@component('mail::message')

<img src="{{ asset('assets/images/bb-logo.png') }}" width="60px" style="border-radius:5px;">

Hi {{ $user->name }}

We are unpleasant to inform you that your account might be inactive or suspended due to certain frauds or reports from other user.
Please contact or email us for more information.

Thanks,<br>
Baby Block Team
@endcomponent
