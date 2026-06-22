@extends('layouts.client')

@section('content')

<h2 class="text-2xl font-semibold mb-6">
    Election Results
</h2>

@foreach($election->categories as $category)

<div class="card mb-6">
    <h3 class="font-semibold mb-4">{{ $category->name }}</h3>

    @foreach($category->candidates as $candidate)

    <div class="mb-3">
        <div class="flex justify-between text-sm">
            <span>{{ $candidate->name }}</span>
            <span>{{ $results[$candidate->id] ?? 0 }} votes</span>
        </div>

        <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded">
            <div class="bg-[#0B1F3A] h-2 rounded"
                 style="width: {{ percentage logic here }}%">
            </div>
        </div>
    </div>

    @endforeach
</div>

@endforeach

@endsection