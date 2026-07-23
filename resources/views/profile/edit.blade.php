@extends('layouts.app')

@section('title', 'Profile')
@section('subtitle', 'Manage your account settings')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    @if(session('success'))
        <div class="alert alert-success" style="background: var(--green-soft); color: var(--green); padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="position: relative; display: inline-block;">
                @if($user->avatar)
                    <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--blue);">
                @else
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--blue-soft); color: var(--blue); display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: bold; border: 2px solid var(--blue);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <label for="avatar" style="position: absolute; bottom: 0; right: 0; background: var(--blue); color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid var(--surface); box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </label>
                <input type="file" id="avatar" name="avatar" style="display: none;" accept="image/*">
            </div>
            @error('avatar')<div style="color: var(--red); font-size: 12px; margin-top: 5px;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; font-size: 13px; color: var(--text-2); margin-bottom: 8px; font-weight: 600;">Display Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="input" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); color: var(--text); font-size: 15px;" required>
            @error('name')<div style="color: var(--red); font-size: 12px; margin-top: 5px;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; font-size: 13px; color: var(--text-2); margin-bottom: 8px; font-weight: 600;">Email Address</label>
            <input type="email" value="{{ $user->email }}" class="input" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); color: var(--text-3); font-size: 15px;" disabled>
            <div style="font-size: 11px; color: var(--text-3); margin-top: 6px;">Email address cannot be changed.</div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer;">Save Changes</button>
    </form>
</div>

<script>
    document.getElementById('avatar').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            let reader = new FileReader();
            reader.onload = function(event) {
                let img = e.target.previousElementSibling.previousElementSibling;
                if(img.tagName === 'IMG') {
                    img.src = event.target.result;
                } else {
                    let newImg = document.createElement('img');
                    newImg.src = event.target.result;
                    newImg.style = "width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--blue);";
                    img.parentNode.replaceChild(newImg, img);
                }
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection
