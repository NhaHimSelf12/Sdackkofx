@extends('layouts.app')

@section('title', 'Website Strategies')
@section('subtitle', 'Manage strategies displayed on the landing page')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4">
        <h2 class="text-[14px] font-semibold text-[var(--text)] m-0">Website Strategies</h2>
        <a href="{{ route('admin.public-strategies.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[var(--brand)] text-white text-[12px] font-semibold hover:opacity-90 transition border-none cursor-pointer no-underline m-0 shadow-lg shadow-[var(--brand)]/20">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Strategy
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-[var(--up)]/25 bg-[var(--up)]/8 px-4 py-3.5">
            <svg class="w-4 h-4 text-[var(--up)] mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <p class="text-[13px] leading-relaxed text-[var(--up)] m-0">{{ session('success') }}</p>
        </div>
    @endif

    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="overflow-x-auto thin-scroll">
            <table class="w-full text-[13px] min-w-[700px] m-0 border-collapse">
                <thead>
                    <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)] border-b border-[var(--line)] bg-[var(--base)]/40">
                        <th class="text-left font-medium px-5 py-3 border-none">Title</th>
                        <th class="text-left font-medium px-3 py-3 border-none">Description</th>
                        <th class="text-left font-medium px-3 py-3 border-none">Images</th>
                        <th class="text-right font-medium px-5 py-3 border-none">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse($strategies as $strategy)
                        <tr class="hover:bg-[var(--raised)]/50 transition-colors">
                            <td class="px-5 py-4 font-semibold text-[var(--text)] border-none">{{ $strategy->title }}</td>
                            <td class="px-3 py-4 text-[var(--muted)] border-none">{{ Str::limit($strategy->description, 50) }}</td>
                            <td class="px-3 py-4 border-none">
                                @if($strategy->images)
                                    <div class="flex items-center gap-1.5">
                                        @foreach(array_slice($strategy->images, 0, 3) as $img)
                                            <img src="{{ str_starts_with($img, 'http') ? $img : asset('storage/'.$img) }}" class="w-10 h-8 object-cover rounded shadow-sm border border-[var(--line)] bg-[var(--base)]">
                                        @endforeach
                                        @if(count($strategy->images) > 3)
                                            <span class="text-[10px] font-bold px-1.5 py-1 rounded bg-[var(--raised)] border border-[var(--line)] text-[var(--muted)]">+{{ count($strategy->images) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] text-[var(--muted)]">No images</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 border-none text-right">
                                <div class="flex items-center justify-end gap-2 m-0">
                                    <a href="{{ route('admin.public-strategies.edit', $strategy) }}" class="px-3 py-1.5 rounded-lg border border-[var(--line)] bg-[var(--surface)] text-[11px] font-semibold text-[var(--muted)] hover:text-[var(--text)] hover:border-[var(--brand)] transition cursor-pointer no-underline">Edit</a>
                                    
                                    <form action="{{ route('admin.public-strategies.destroy', $strategy) }}" method="POST" class="m-0 inline-block" onsubmit="return confirm('Are you sure you want to delete this strategy?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-[var(--line)] bg-[var(--surface)] text-[11px] font-semibold text-[var(--down)] hover:bg-[var(--down)]/10 hover:border-[var(--down)]/30 transition cursor-pointer">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-[var(--muted)] border-none">No strategies found. Add one to show on the landing page.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
