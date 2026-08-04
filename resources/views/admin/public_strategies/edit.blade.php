@extends('layouts.app')

@section('title', 'Edit Strategy')
@section('subtitle', 'Update strategy details')

@section('content')
<div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] max-w-3xl overflow-hidden">
    <div class="px-6 py-5 border-b border-[var(--line)] bg-[var(--base)]/40">
        <h2 class="text-[14px] font-semibold text-[var(--text)] m-0">Strategy details</h2>
    </div>

    <form action="{{ route('admin.public-strategies.update', $publicStrategy) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
        @csrf @method('PUT')
        
        <div class="flex flex-col gap-2">
            <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Title</label>
            <input type="text" name="title" value="{{ $publicStrategy->title }}" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors">
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Description</label>
            <textarea name="description" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors min-h-[120px] resize-y">{{ $publicStrategy->description }}</textarea>
        </div>

        @if($publicStrategy->images)
            <div class="flex flex-col gap-3">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Current Images</label>
                <div class="flex flex-wrap gap-4">
                    @foreach($publicStrategy->images as $index => $img)
                        <div class="relative w-32 group">
                            <div class="w-full h-24 rounded-lg overflow-hidden border border-[var(--line)] bg-[var(--base)]">
                                <img src="{{ str_starts_with($img, 'http') ? $img : asset('storage/'.$img) }}" class="w-full h-full object-cover">
                            </div>
                            <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                <input type="checkbox" name="remove_images[]" value="{{ $img }}" class="w-3.5 h-3.5 rounded border-[var(--line)] text-[var(--down)] focus:ring-[var(--down)] bg-[var(--base)]">
                                <span class="text-[11px] font-medium text-[var(--down)]">Remove</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex flex-col gap-2">
            <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Add More Images</label>
            <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-[12px] text-[var(--muted)] file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-[12px] file:font-semibold file:bg-[var(--raised)] file:text-[var(--text)] hover:file:bg-[var(--line)] file:transition-colors file:cursor-pointer cursor-pointer border border-[var(--line)] rounded-lg bg-[var(--base)]">
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-[var(--line)]">
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-[var(--brand)] text-white text-[13px] font-semibold hover:opacity-90 transition border-none cursor-pointer shadow-lg shadow-[var(--brand)]/20">Update Strategy</button>
            <a href="{{ route('admin.public-strategies.index') }}" class="px-5 py-2.5 rounded-lg border border-[var(--line)] bg-[var(--surface)] text-[13px] font-semibold text-[var(--text)] hover:bg-[var(--raised)] transition no-underline">Cancel</a>
        </div>
    </form>
</div>
@endsection
