<x-mail::message>
# Welcome to Dynamic QR SaaS, {{ $user->name }}!

We're excited to have you on board. You can now create dynamic QR codes, customize your public profiles, and start tracking analytics instantly.

<x-mail::button :url="$dashboardUrl">
Go to Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>