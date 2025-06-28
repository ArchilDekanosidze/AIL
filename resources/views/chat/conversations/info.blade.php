@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/chat/groups/info.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection">
        <h2 class="mb-4">اطلاعات کانال</h2>

        <a href="{{ route('chat.messages.index', $conversation->id) }}" class="btn btn-outline-secondary mb-3">
            بازگشت به کانال
        </a>
        <a href="{{ route('chat.groups.search-users-after-creation-form', $conversation->id) }}" class="btn btn-primary">
            افزودن عضو جدید
        </a>


        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if(in_array($role, ['admin', 'super_admin']))
            <form method="POST" action="{{ route('chat.groups.updateInfo', $conversation->id) }}" id="channel-info-form">
                @csrf

                {{-- LINK --}}
                <div class="form-group mb-3">
                    <label class="form-label">لینک کانال</label>
                    <div id="link-view">
                        <p class="form-control-plaintext d-inline">
                            <a href="{{ url('/chat/' . $conversation->slug) }}" target="_blank">
                                {{ url('/chat/' . $conversation->slug) }}
                            </a>
                        </p>
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-toggle-btn" data-target="link">ویرایش لینک</button>
                    </div>
                    <div id="link-edit" style="display: none;">
                        <input type="text" name="link" class="form-control mt-2" value="{{ old('link', $conversation->slug) }}">
                    </div>
                </div>

                {{-- BIO --}}
                <div class="form-group mb-3">
                    <label class="form-label">بیوگرافی کانال</label>
                    <div id="bio-view">
                        <p class="form-control-plaintext d-inline">
                            {{ $conversation->bio ?? 'ثبت نشده' }}
                        </p>
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-toggle-btn" data-target="bio">ویرایش بیو</button>
                    </div>
                    <div id="bio-edit" style="display: none;">
                        <textarea name="bio" rows="4" class="form-control mt-2">{{ old('bio', $conversation->bio) }}</textarea>
                    </div>
                </div>

                {{-- PRIVACY --}}
                <div class="form-group mb-4">
                    <label class="form-label">وضعیت حریم خصوصی</label>
                    <div id="privacy-view">
                        <p class="form-control-plaintext d-inline">
                            {{ $conversation->is_private ? 'خصوصی' : 'عمومی' }}
                        </p>
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-toggle-btn" data-target="privacy">ویرایش</button>
                    </div>
                    <div id="privacy-edit" style="display: none;">
                        <select name="is_private" class="form-control mt-2">
                            <option value="1" {{ $conversation->is_private ? 'selected' : '' }}>خصوصی</option>
                            <option value="0" {{ !$conversation->is_private ? 'selected' : '' }}>عمومی</option>
                        </select>
                    </div>
                </div>

                {{-- Save Button (initially hidden) --}}
                <button type="submit" class="btn btn-primary" id="save-btn" style="display: none;">ذخیره تغییرات</button>
            </form>
        @else
            {{-- REGULAR USER VIEW --}}
            <div class="mb-3">
                <strong>لینک کانال:</strong>
                @if($conversation->slug)
                    <p class="form-control-plaintext">
                        <a href="{{ url('/chat/' . $conversation->slug) }}" target="_blank">
                            {{ url('/chat/' . $conversation->slug) }}
                        </a>
                    </p>
                @else
                    <p class="form-control-plaintext text-muted">ثبت نشده</p>
                @endif
            </div>

            <div class="mb-3">
                <strong>بیوگرافی:</strong>
                <p class="form-control-plaintext">{{ $conversation->bio ?? 'ثبت نشده' }}</p>
            </div>

            <div class="mb-3">
                <strong>حریم خصوصی:</strong>
                <p class="form-control-plaintext">{{ $conversation->is_private ? 'خصوصی' : 'عمومی' }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-toggle-btn');
        const saveBtn = document.getElementById('save-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const target = this.dataset.target;

                // Toggle visibility
                document.getElementById(`${target}-view`).style.display = 'none';
                document.getElementById(`${target}-edit`).style.display = 'block';

                // Show save button
                saveBtn.style.display = 'inline-block';
            });
        });
    });
</script>
@endsection
