<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $publicStrategy->title }} · FX Command</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
    <style>
        .strategy-detail-page { max-width: 1000px; margin: 0 auto; padding: 120px 20px 60px; min-height: 100vh; }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--blue); text-decoration: none; font-weight: 600; margin-bottom: 40px; transition: color 0.2s; }
        .back-link:hover { color: var(--text); }
        .strategy-header { margin-bottom: 40px; }
        .strategy-header h1 { font-size: 48px; font-weight: 800; color: var(--text); margin: 0 0 16px; line-height: 1.1; }
        .strategy-header p { font-size: 18px; color: var(--text-2); line-height: 1.6; max-width: 800px; margin: 0; }
        .strategy-images-grid { display: grid; gap: 40px; }
        .strategy-image-container { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 8px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .strategy-image-container img { width: 100%; height: auto; border-radius: 12px; display: block; cursor: zoom-in; transition: transform 0.3s ease; }
        .strategy-image-container img:hover { transform: scale(1.02); }
        
        /* Modal for full screen image */
        .img-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9); backdrop-filter: blur(10px); }
        .img-modal-content { margin: auto; display: block; max-width: 90%; max-height: 90vh; margin-top: 5vh; border-radius: 8px; box-shadow: 0 0 40px rgba(0,0,0,0.5); }
        .img-modal-close { position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .img-modal-close:hover { color: #bbb; }
        
        @media (max-width: 768px) {
            .strategy-header h1 { font-size: 36px; }
        }
    </style>
</head>
<body class="public-body">
    <header class="public-nav">
        <a class="public-brand" href="{{ route('home') }}">
            <span class="brand-mark">FX</span><strong>Command</strong>
        </a>
        <div class="nav-actions" style="margin-left: auto;">
            <button class="theme-toggle" type="button" aria-label="Toggle theme"><span class="theme-icon"></span></button>
            @auth
                <a class="btn" href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a class="nav-login" href="{{ route('login') }}">Log in</a>
                <a class="btn btn-primary" href="{{ route('register') }}">Start free</a>
            @endauth
        </div>
    </header>

    <main class="strategy-detail-page reveal">
        <a href="{{ route('home') }}#strategies" class="back-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Strategies
        </a>

        <div class="strategy-header">
            <h1>{{ $publicStrategy->title }}</h1>
            <p>{{ $publicStrategy->description }}</p>
        </div>

        <div class="strategy-images-grid">
            @if($publicStrategy->images && count($publicStrategy->images) > 0)
                @foreach($publicStrategy->images as $img)
                    <div class="strategy-image-container reveal">
                        <img src="{{ asset('storage/'.$img) }}" alt="{{ $publicStrategy->title }} Image {{ $loop->iteration }}" onclick="openModal(this.src)">
                    </div>
                @endforeach
            @else
                <div style="padding: 60px; text-align: center; background: var(--surface); border: 1px dashed var(--border); border-radius: 16px; color: var(--text-3);">
                    No images uploaded for this strategy yet.
                </div>
            @endif
        </div>
    </main>

    <!-- The Modal -->
    <div id="imageModal" class="img-modal" onclick="closeModal()">
        <span class="img-modal-close" onclick="closeModal()">&times;</span>
        <img class="img-modal-content" id="modalImg">
    </div>

    <footer class="public-footer">
        <a class="public-brand"><span class="brand-mark">FX</span><strong>Command</strong></a>
        <p>Market analysis for education—not financial advice.</p>
        <span>© {{ date('Y') }} FX Command</span>
    </footer>

    <script src="{{ asset('js/public.js') }}" defer></script>
    <script>
        function openModal(src) {
            document.getElementById('imageModal').style.display = "block";
            document.getElementById('modalImg').src = src;
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            document.getElementById('imageModal').style.display = "none";
            document.body.style.overflow = 'auto';
        }
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") closeModal();
        });
    </script>
</body>
</html>
