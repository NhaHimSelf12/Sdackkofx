@extends('layouts.app')

@section('title', 'Edit Strategy')
@section('subtitle', 'Update strategy details')

@section('content')
<div class="card" style="max-width: 700px;">
    <form action="{{ route('admin.public-strategies.update', $publicStrategy) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--text-2); font-weight: 600;">Title</label>
            <input type="text" name="title" value="{{ $publicStrategy->title }}" class="input" style="width: 100%;" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--text-2); font-weight: 600;">Description</label>
            <textarea name="description" class="input" style="width: 100%; min-height: 100px;">{{ $publicStrategy->description }}</textarea>
        </div>

        @if($publicStrategy->images)
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: var(--text-2); font-weight: 600;">Current Images</label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    @foreach($publicStrategy->images as $index => $img)
                        <div style="position: relative; width: 120px; height: 100px;">
                            <img src="{{ asset('storage/'.$img) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                            <label style="display: block; margin-top: 5px; font-size: 12px; color: var(--red);">
                                <input type="checkbox" name="remove_images[]" value="{{ $img }}"> Remove
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--text-2); font-weight: 600;">Add More Images</label>
            <input type="file" name="images[]" class="input" style="width: 100%;" multiple accept="image/*">
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Update Strategy</button>
            <a href="{{ route('admin.public-strategies.index') }}" class="btn">Cancel</a>
        </div>
    </form>
</div>
@endsection
