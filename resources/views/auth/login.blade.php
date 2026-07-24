<!DOCTYPE html><html lang="km"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login · Nha FX</title><link rel="stylesheet" href="{{ asset('css/app.css') }}"><link rel="stylesheet" href="{{ asset('css/v2.css') }}"><link rel="stylesheet" href="{{ asset('css/theme.css') }}"><script src="{{ asset('js/theme.js') }}"></script>
<style>
.hero-bg-anim{position:fixed;top:0;bottom:0;left:0;right:0;z-index:0;opacity:0.4;pointer-events:none;}
@media (max-width: 768px) { .hero-bg-anim { display: none; } }
.hero-bg-anim svg{width:100%;height:100%;}
.auth-theme-toggle { position: relative; z-index: 10; }
</style>
</head>
<body class="auth-body">
<div class="hero-bg-anim">
	<svg viewBox="0 0 1000 400" preserveAspectRatio="xMidYMid slice">
		<defs>
			<linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
				<stop offset="0%" stop-color="rgba(63, 206, 143, 0.25)" />
				<stop offset="100%" stop-color="rgba(63, 206, 143, 0)" />
			</linearGradient>
			<filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
				<feGaussianBlur stdDeviation="8" result="blur" />
				<feMerge>
					<feMergeNode in="blur" />
					<feMergeNode in="SourceGraphic" />
				</feMerge>
			</filter>
		</defs>
		<g class="bg-bars">
			@for($i=0; $i<60; $i++)
				@php 
					$h = rand(40, 250);
					$y = 400 - $h;
					$dur = rand(4, 10);
				@endphp
				<rect x="{{ $i*18 }}" y="{{ $y }}" width="2" height="{{ $h }}" fill="rgba(63, 206, 143, 0.15)">
					<animate attributeName="height" values="{{ $h }};{{ $h + rand(-40, 80) }};{{ $h }}" dur="{{ $dur }}s" repeatCount="indefinite" />
					<animate attributeName="y" values="{{ $y }};{{ $y - rand(-40, 80) }};{{ $y }}" dur="{{ $dur }}s" repeatCount="indefinite" />
				</rect>
			@endfor
		</g>
		<path class="bg-line" d="M0,350 Q150,280 300,320 T600,200 T1000,100" fill="none" stroke="#3fce8f" stroke-width="3" filter="url(#glow)">
			<animate attributeName="d" values="M0,350 Q150,280 300,320 T600,200 T1000,100; M0,320 Q150,300 300,280 T600,250 T1000,80; M0,350 Q150,280 300,320 T600,200 T1000,100" dur="8s" repeatCount="indefinite" />
		</path>
		<path class="bg-area" d="M0,350 Q150,280 300,320 T600,200 T1000,100 L1000,400 L0,400 Z" fill="url(#chartGrad)">
			<animate attributeName="d" values="M0,350 Q150,280 300,320 T600,200 T1000,100 L1000,400 L0,400 Z; M0,320 Q150,300 300,280 T600,250 T1000,80 L1000,400 L0,400 Z; M0,350 Q150,280 300,320 T600,200 T1000,100 L1000,400 L0,400 Z" dur="8s" repeatCount="indefinite" />
		</path>
		<!-- Arrows -->
		<g transform="translate(800, 80)" filter="url(#glow)">
			<path d="M0,0 L30,-30 L60,0 L40,0 L40,40 L20,40 L20,0 Z" fill="#3fce8f" transform="rotate(30)">
				<animateTransform attributeName="transform" type="translate" values="0,15; 0,-15; 0,15" dur="4s" repeatCount="indefinite" additive="sum"/>
			</path>
		</g>
		<g transform="translate(300, 260)" filter="url(#glow)">
			<path d="M0,0 L20,-20 L40,0 L25,0 L25,25 L15,25 L15,0 Z" fill="#3fce8f" opacity="0.6" transform="rotate(10)">
				<animateTransform attributeName="transform" type="translate" values="0,10; 0,-10; 0,10" dur="5s" repeatCount="indefinite" additive="sum"/>
			</path>
		</g>
	</svg>
</div>
<div class="auth-shell" style="position:relative; z-index:10;">
<div class="auth-brand"><span class="brand-mark">Nha</span><div><strong>Nha FX</strong><div class="muted">Professional Market Intelligence</div></div></div>
<div class="card auth-card"><h1>Welcome back</h1><p class="muted">Access your dashboard, signals and trading journal.</p>
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('login.submit') }}" class="form-stack">@csrf
<label>Email<input class="input" type="email" name="email" value="{{ old('email') }}" required autofocus></label>
<label>Password<input class="input" type="password" name="password" required></label>
<label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>
<button class="btn btn-primary" type="submit">Sign in</button></form>
<div style="margin: 16px 0; text-align: center; color: var(--text-3); font-size: 13px; position: relative;">
    <span style="background: var(--card); padding: 0 10px; position: relative; z-index: 1;">or</span>
    <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: var(--border); z-index: 0;"></div>
</div>
<a href="{{ route('auth.google') }}" class="btn" style="width:100%; display:flex; justify-content:center; gap:8px; align-items:center;">
    <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C12.955 4 4 12.955 4 24s8.955 20 20 20s20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/><path fill="#FF3D00" d="m6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C16.318 4 9.656 8.337 6.306 14.691z"/><path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.222 0-9.654-3.343-11.13-8l-6.56 5.045C9.646 39.52 16.317 44 24 44z"/><path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/></svg>
    Continue with Google
</a>
<p class="auth-switch">New to Nha FX? <a href="{{ route('register') }}">Create an account</a></p></div></div></body></html>
