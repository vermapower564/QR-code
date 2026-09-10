@extends('layouts.public')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl">
        <div>
            @if($profile->logo)
                <img class="mx-auto h-20 w-auto rounded-full object-cover" src="{{ Storage::url($profile->logo) }}" alt="{{ $profile->name }}">
            @elseif($profile->profile_image)
                <img class="mx-auto h-20 w-auto rounded-full object-cover" src="{{ Storage::url($profile->profile_image) }}" alt="{{ $profile->name }}">
            @else
                <div class="mx-auto h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-xl">
                    {{ substr($profile->name, 0, 2) }}
                </div>
            @endif
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ $profile->name }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                This profile is password protected
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('profile.unlock', $profile->slug) }}" method="POST">
            @csrf
            
            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ $errors->first() }}
                            </h3>
                        </div>
                    </div>
                </div>
            @endif

            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Enter Profile Password">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    Unlock Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
