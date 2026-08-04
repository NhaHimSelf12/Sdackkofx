@extends('layouts.app')

@section('title', 'Add Strategy')
@section('subtitle', 'Add a new strategy for the public landing page')

@section('content')
<div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] max-w-3xl overflow-hidden">
    <div class="px-6 py-5 border-b border-[var(--line)] bg-[var(--base)]/40">
        <h2 class="text-[14px] font-semibold text-[var(--text)] m-0">Strategy details</h2>
    </div>

    <form action="{{ route('admin.public-strategies.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
        @csrf
        
        <div class="flex flex-col gap-2">
            <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Title</label>
            <input type="text" name="title" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors">
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Description</label>
            <textarea name="description" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors min-h-[120px] resize-y"></textarea>
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Images (Multiple)</label>
            <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-[12px] text-[var(--muted)] file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-[12px] file:font-semibold file:bg-[var(--raised)] file:text-[var(--text)] hover:file:bg-[var(--line)] file:transition-colors file:cursor-pointer cursor-pointer border border-[var(--line)] rounded-lg bg-[var(--base)]">
            <span class="text-[11px] text-[var(--muted)] mt-1">You can select multiple images to upload.</span>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-[var(--line)]">
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-[var(--brand)] text-white text-[13px] font-semibold hover:opacity-90 transition border-none cursor-pointer shadow-lg shadow-[var(--brand)]/20">Save Strategy</button>
            <a href="{{ route('admin.public-strategies.index') }}" class="px-5 py-2.5 rounded-lg border border-[var(--line)] bg-[var(--surface)] text-[13px] font-semibold text-[var(--text)] hover:bg-[var(--raised)] transition no-underline">Cancel</a>
        </div>
    </form>
</div>
@endsection
