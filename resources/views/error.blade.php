@extends('layouts.app')

@section('content')

<div class="min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-2xl p-8 text-center max-w-md">

        <p class="text-gray-600 mb-6">
            {{ $error_message }}
        </p>
    </div>

</div>

@endsection