<x-mail::message>
<strong style="font-size: 20px;">{{ $greetings }}</strong>

{{ $header }}

<strong style="font-size: 30px; color: #0077CE;">{{ $code }}</strong>

This code is valid for the next 24 hours.
@if($action == "sign-up")
    <br><br>
@endif
{{ $message }}

Thank you,<br>
The Wayyti Team
</x-mail::message>
