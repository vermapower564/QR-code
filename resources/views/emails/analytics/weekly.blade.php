<x-mail::message>
# Your Weekly Profile Analytics ??

Hi {{ $user->name }},

Here is the performance summary for your dynamic QR profiles over the last 7 days.

<x-mail::panel>
**Total QR Scans:** {{ $stats['scans'] }}  
**Unique Visitors:** {{ $stats['visitors'] }}  
**Total Link Clicks:** {{ $stats['clicks'] }}
</x-mail::panel>

Top Performing Profile: **{{ $stats['top_profile_name'] }}**

<x-mail::button :url="$analyticsUrl">
View Detailed Report
</x-mail::button>

Keep growing!<br>
{{ config('app.name') }} Analytics Team
</x-mail::message>