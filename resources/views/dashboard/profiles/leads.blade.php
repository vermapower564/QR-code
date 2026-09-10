@extends('layouts.dashboard')

@section('title', 'Leads - ' . $profile->name)

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Leads & Contacts</h1>
        <p class="text-slate-500 mt-1">View leads captured from your profile contact form.</p>
    </div>
    <a href="{{ route('dashboard.profiles.edit', $profile->id) }}" class="px-4 py-2 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm font-semibold text-sm">
        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Profile
    </a>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    @if($leads->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Message</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($leads as $lead)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $lead->name }}</td>
                        <td class="px-6 py-4"><a href="mailto:{{ $lead->email }}" class="text-sky-600 hover:underline">{{ $lead->email }}</a></td>
                        <td class="px-6 py-4">{{ $lead->phone ?? '-' }}</td>
                        <td class="px-6 py-4 truncate max-w-[200px]" title="{{ $lead->message }}">{{ $lead->message ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs whitespace-nowrap">{{ $lead->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $leads->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                <i class="fa-solid fa-inbox text-2xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">No Leads Yet</h3>
            <p class="text-slate-500 max-w-sm mx-auto text-sm">
                @if(!$profile->enable_lead_capture)
                    You haven't enabled lead capture. Go back to edit your profile and check "Enable Lead Capture" to start collecting contacts.
                @else
                    You have enabled lead capture, but no visitors have submitted their information yet. Share your profile link to get started!
                @endif
            </p>
            <div class="mt-6">
                <a href="{{ route('profile.show', $profile->slug) }}" target="_blank" class="px-4 py-2 bg-indigo-50 text-indigo-600 font-bold rounded-lg hover:bg-indigo-100 transition text-sm">
                    View Public Profile
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
