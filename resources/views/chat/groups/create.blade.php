@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/chat/groups/create.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection">
        <div class="chat-create-form-box">
            <h2 class="text-2xl font-bold mb-4">Create New {{ ucfirst($type) }}</h2>

            <form action="{{ route('chat.groups.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="form-group mb-3">
                    <label for="title" class="form-label font-semibold">Name</label>
                    <input type="text" name="title" id="title" class="form-control w-full p-2 rounded border" required placeholder="Enter {{ $type }} name">
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold block">Privacy</label>
                    <label>
                        <input type="radio" name="is_private" value="1" checked> Private
                    </label>
                    <label class="ml-4">
                        <input type="radio" name="is_private" value="0"> Public
                    </label>
                </div>

                <div class="form-group mb-4">
                    <label for="link" class="form-label font-semibold">Custom Link (Optional)</label>
                    <input type="text" name="link" id="link" class="form-control w-full p-2 rounded border" placeholder="example: my-{{ $type }}-group">
                    <small class="text-gray-500">Link will be used like <code>/chat/{{ $type }}/your-link</code></small>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Continue
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
