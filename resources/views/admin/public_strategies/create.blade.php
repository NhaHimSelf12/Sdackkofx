@extends('layouts.app')

@section('title', 'Add Strategy')
@section('subtitle', 'Add a new strategy for the public landing page')

@section('content')
<div class="card" style="max-width: 700px;">
    <form action="{{ route('admin.public-strategies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--text-2); font-weight: 600;">Title</label>
            <input type="text" name="title" class="input" style="width: 100%;" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--text-2); font-weight: 600;">Description</label>
            <textarea name="description" class="input" style="width: 100%; min-height: 100px;"></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--text-2); font-weight: 600;">Images (Multiple)</label>
            <input type="file" name="images[]" class="input" style="width: 100%;" multiple accept="image/*">
            <small style="color: var(--text-3); display: block; margin-top: 5px;">You can select multiple images to upload.</small>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Save Strategy</button>
            <a href="{{ route('admin.public-strategies.index') }}" class="btn">Cancel</a>
        </div>
    </form>
</div>
@endsection
