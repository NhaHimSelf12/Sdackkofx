@extends('layouts.app')

@section('title', 'Website Strategies')
@section('subtitle', 'Manage strategies displayed on the landing page')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px; color: var(--text);">Website Strategies</h2>
        <a href="{{ route('admin.public-strategies.create') }}" class="btn btn-primary">Add Strategy</a>
    </div>

    @if(session('success'))
        <div style="background: var(--green-soft); color: var(--green); padding: 10px; border-radius: 8px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <table class="table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border); text-align: left;">
                <th style="padding: 12px; color: var(--text-3);">Title</th>
                <th style="padding: 12px; color: var(--text-3);">Description</th>
                <th style="padding: 12px; color: var(--text-3);">Images</th>
                <th style="padding: 12px; color: var(--text-3); text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($strategies as $strategy)
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 12px; color: var(--text);"><strong>{{ $strategy->title }}</strong></td>
                    <td style="padding: 12px; color: var(--text-2);">{{ Str::limit($strategy->description, 50) }}</td>
                    <td style="padding: 12px; color: var(--text-2);">
                        @if($strategy->images)
                            <div style="display: flex; gap: 5px;">
                                @foreach(array_slice($strategy->images, 0, 3) as $img)
                                    <img src="{{ asset('storage/'.$img) }}" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px;">
                                @endforeach
                                @if(count($strategy->images) > 3)
                                    <span style="font-size: 11px; background: var(--hover); padding: 5px; border-radius: 4px;">+{{ count($strategy->images) - 3 }}</span>
                                @endif
                            </div>
                        @else
                            No images
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        <a href="{{ route('admin.public-strategies.edit', $strategy) }}" class="btn" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                        <form action="{{ route('admin.public-strategies.destroy', $strategy) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn" style="padding: 6px 12px; font-size: 12px; color: var(--red);">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding: 20px; text-align: center; color: var(--text-3);">No strategies found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
