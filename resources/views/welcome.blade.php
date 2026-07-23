<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sdach KOFX · Market intelligence for disciplined traders</title>
<meta name="description" content="Live market terminal, verified signals, FVG and trendline analysis, financial news and risk management.">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/public.css') }}">
<link rel="stylesheet" href="{{ asset('css/feed.css') }}">
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
{{-- New: redesigned home styling. Must load LAST so it overrides older rules. --}}
<link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ time() }}">
<script src="{{ asset('js/theme.js') }}"></script>
</head>
<body class="public-body">

<header class="public-nav">
	<a class="public-brand" href="{{ route('home') }}"><span class="brand-mark">Sdach</span><strong>KOFX</strong></a>
	<nav id="publicNavMenu">
		<a href="#markets">Markets</a>
		<a href="#features">Platform</a>
		<a href="#strategies">Strategy</a>
		<a href="#news">News</a>
		<a href="{{ route('public.learn') }}">Learn</a>
	</nav>
	<div class="nav-actions">
		<button class="theme-toggle" type="button" aria-label="Toggle theme"><span class="theme-icon"></span></button>
		@auth
            <a href="{{ route('dashboard') }}" class="public-profile-link" style="display:flex; align-items:center; gap:8px; text-decoration:none; color:var(--text); font-weight:600; padding:4px 12px 4px 4px; background:var(--raised); border-radius:999px; border:1px solid var(--border); transition:border-color 0.2s;">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="Avatar" style="width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid var(--border);">
                @else
                    <div style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg, var(--blue), #7c5cff); color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; text-transform:uppercase;">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <span style="font-size:14px; margin-right:4px;">{{ explode(' ', auth()->user()->name)[0] }}</span>
            </a>
		@else
			<a class="nav-login" href="{{ route('login') }}">Log in</a>
			<a class="btn btn-primary" href="{{ route('register') }}">Start free</a>
		@endauth
        <button class="mobile-menu-btn" id="publicMenuToggle" aria-label="Toggle navigation">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
	</div>
</header>

{{-- Scrolling market ticker --}}
<div class="market-ticker" aria-hidden="true">
	<div class="ticker-track">
		@for($i=0;$i<6;$i++)
			<div class="ticker-set">
				@foreach($markets as $market)
					<span class="tick"><b>{{ $market->symbol }}</b><span>{{ number_format($market->price,$market->precision()) }}</span><em class="{{ $market->change_pct>=0?'up':'down' }}">{{ $market->change_pct>=0?'▲':'▼' }} {{ number_format(abs($market->change_pct),2) }}%</em></span>
				@endforeach
			</div>
		@endfor
	</div>
</div>

<main>

	{{-- ============================ HERO ============================ --}}
	<section class="hero">
		{{-- CSS Animated Background Chart --}}
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

		<div class="hero-glow g1"></div>
		<div class="hero-glow g2"></div>
		<div class="hero-copy reveal">
			<span class="eyebrow"><i></i> Market intelligence, not noise</span>
			<h1>Read the market.<br><span>Plan the trade.</span></h1>
			<p>One professional workspace for live candles, FVG zones, automatic trendlines, verified entry signals, news sentiment and disciplined risk.</p>
			<div class="hero-actions">
				@auth
					<a class="btn btn-primary btn-large" href="{{ route('terminal.show', $markets->first()) }}">Launch terminal</a>
				@else
					<a class="btn btn-primary btn-large" href="{{ route('register') }}">Create trader account</a>
					<a class="btn btn-large" href="{{ route('login') }}">Log in</a>
				@endauth
			</div>
            
            <div class="social-proof-widget">
                <div class="avatars">
                    @foreach($recentUsers as $u)
                        @if($u->avatar)
                            <img src="{{ str_starts_with($u->avatar, 'http') ? $u->avatar : asset('storage/'.$u->avatar) }}" alt="{{ $u->name }}">
                        @else
                            <div class="avatar-initial">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                        @endif
                    @endforeach
                </div>
                <div class="social-text">
                    <strong>{{ number_format($totalUsers) }}+ traders</strong>
                    <span>joined the {{ config('app.name', 'Sdach KOFX') }} community</span>
                </div>
                <div class="live-badge">
                    <span class="dot"></span> Live
                </div>
            </div>

			<div class="hero-proof">
				<div><strong>{{ $markets->count() }}+</strong><span>Markets</span></div>
				<div><strong>{{ $signalCount }}</strong><span>Active setups</span></div>
				<div><strong>4</strong><span>Strategy models</span></div>
			</div>
		</div>

		<div class="hero-terminal reveal">
            @php $heroMarket = \App\Models\Market::where('symbol','XAUUSD')->first() ?? $markets->first(); @endphp
			<div class="mock-head">
				<div><b>{{ $heroMarket?->symbol ?? 'XAUUSD' }}</b><span>{{ $heroMarket?->name ?? 'Gold / US Dollar' }}</span></div>
				<strong id="nfxHeroPrice">{{ number_format($heroMarket?->price ?? 0, $heroMarket?->precision() ?? 2) }}</strong>
				<span class="feed-chip feed-delayed">MARKET FEED</span>
			</div>
			<div class="mock-chart">
				<svg viewBox="0 0 700 340" preserveAspectRatio="none">
					<g class="mock-grid"><path d="M0 55H700M0 120H700M0 185H700M0 250H700M100 0V340M230 0V340M360 0V340M490 0V340M620 0V340"/></g>
					<path class="mock-line-green" d="M20 275L160 245L305 220L450 180L680 135"/>
					<path class="mock-line-red" d="M20 82L200 92L380 74L680 60"/>
					<g id="heroCandles"></g>
				</svg>
				<div class="mock-tag buy-tag">BUY · SMC</div>
				<div class="mock-tag fvg-tag">BULLISH FVG</div>
			</div>
			<div class="mock-foot">
				<span>M1</span><span>M5</span><b>M15</b><span>H1</span><span>H4</span>
				<em id="nfxCountdown">Next candle 00:42</em>
			</div>
		</div>
	</section>

	{{-- ============================ MARKETS ============================ --}}
	<section class="market-strip" id="markets">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow">Market board</span>
				<h2>Focus on what is moving</h2>
			</div>
			<p>Prices display their source status so delayed or demo data is never presented as live.</p>
		</div>
		<div class="public-market-grid">
			@foreach($markets as $market)
				<article class="public-market reveal">
					<div><strong>{{ $market->symbol }}</strong><span>{{ $market->name }}</span></div>
					<b>{{ number_format($market->price,$market->precision()) }}</b>
					<span class="{{ $market->change_pct>=0?'up':'down' }}">{{ $market->change_pct>=0?'▲':'▼' }} {{ number_format(abs($market->change_pct),2) }}%</span>
					<small class="feed-{{ $market->data_status }}">{{ strtoupper($market->data_status.' · '.$market->data_source) }}</small>
				</article>
			@endforeach
		</div>
	</section>

	{{-- ============================ FEATURES ============================ --}}
	<section class="features" id="features">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow">Complete workflow</span>
				<h2>From context to execution</h2>
			</div>
		</div>
		<div class="feature-grid">
			<article class="feature-card reveal">
				<span>01</span>
				<h3>Streaming terminal</h3>
				<p>M1 to D1 candles, candle countdown, OHLC crosshair, volume and automatic refresh without reloading.</p>
			</article>
			<article class="feature-card reveal">
				<span>02</span>
				<h3>FVG &amp; structure</h3>
				<p>Bullish/bearish fair value gaps, swing trendlines and volume profile calculated from the active timeframe.</p>
			</article>
			<article class="feature-card reveal">
				<span>03</span>
				<h3>Verified signals</h3>
				<p>SMC, ICT, MSNR and technical confluence entries with SL, TP, confidence, source and expiry.</p>
			</article>
			<article class="feature-card reveal">
				<span>04</span>
				<h3>Risk &amp; journal</h3>
				<p>Position sizing, planned risk, trade review, win rate, net P/L and R-multiple tracking for every user.</p>
			</article>
		</div>
	</section>

	{{-- ============================ STRATEGIES ============================ --}}
	<section class="strategies-section" id="strategies">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow">Trading Strategies</span>
				<h2>Proven Market Models</h2>
			</div>
			<p>We analyze the market using the most advanced trading concepts to provide high-probability entries.</p>
		</div>
		<div class="strategy-gallery reveal">
			@forelse($publicStrategies as $strategy)
				<a href="{{ route('public.strategy.show', $strategy) }}" class="strategy-card">
					@if($strategy->images && count($strategy->images) > 0)
						<div class="strategy-media">
							@foreach($strategy->images as $img)
								<img src="{{ asset('storage/'.$img) }}" alt="{{ $strategy->title }}" loading="lazy">
							@endforeach
						</div>
					@else
						<div class="strategy-placeholder">No image</div>
					@endif
					<div class="strategy-info">
						<h3>{{ $strategy->title }} <span class="arrow">→</span></h3>
						<p>{{ Str::limit($strategy->description, 100) }}</p>
					</div>
				</a>
			@empty
				<p class="strategy-empty">No strategies published yet.</p>
			@endforelse
		</div>
	</section>

	{{-- ============================ NEWS ============================ --}}
	<section class="news-section" id="news">
		<div class="section-head reveal">
			<div>
				<span class="eyebrow">Market intelligence</span>
				<h2>News with context</h2>
			</div>
			@auth<a href="{{ route('news.index') }}">View all news →</a>@endauth
		</div>
		<div class="public-news-grid">
			@foreach($news as $item)
				<article class="public-news reveal">
					<div>
						<span class="badge {{ $item->sentiment==='bullish'?'badge-buy':($item->sentiment==='bearish'?'badge-sell':'badge-neutral') }}">{{ strtoupper($item->sentiment) }}</span>
						@if($item->impact==='high')<span class="badge badge-orange">HIGH IMPACT</span>@endif
					</div>
					<h3>{{ $item->title }}</h3>
					<p>{{ $item->summary }}</p>
					<footer>
						<span>{{ $item->source }}</span>
						<time>{{ optional($item->published_at)?->diffForHumans() }}</time>
					</footer>
				</article>
			@endforeach
		</div>
	</section>

	{{-- ============================ CTA ============================ --}}
	<section class="public-cta reveal">
		<div>
			<span class="eyebrow">Build a repeatable process</span>
			<h2>Your terminal, signals and journal — together.</h2>
		</div>
		@auth
			<a class="btn btn-primary btn-large" href="{{ route('dashboard') }}">Open dashboard</a>
		@else
			<a class="btn btn-primary btn-large" href="{{ route('register') }}">Start now</a>
		@endauth
	</section>

</main>

<footer class="public-footer">
	<a class="public-brand"><span class="brand-mark">Nha</span><strong>FX</strong></a>
	<p>Market analysis for education — not financial advice.</p>
	<span>© {{ date('Y') }} Nha FX</span>
</footer>

<script src="{{ asset('js/public.js') }}" defer></script>
{{-- Animations: scroll reveal, candles, countdown, live price tick, count-up stats --}}
<script>
(function(){
	document.documentElement.classList.add('nfx-js');
	var rm=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Staggered scroll reveal */
	var items=[].slice.call(document.querySelectorAll('.reveal'));
	if('IntersectionObserver' in window&&!rm){
		var io=new IntersectionObserver(function(entries){
			var d=0;
			entries.forEach(function(e){
				if(!e.isIntersecting)return;
				var el=e.target;io.unobserve(el);
				el.style.animationDelay=d+'ms';d+=90;
				el.classList.add('in');
			});
		},{threshold:.12,rootMargin:'0px 0px -40px 0px'});
		items.forEach(function(el){io.observe(el)});
	}else{
		items.forEach(function(el){el.classList.add('in')});
	}

	/* Hero candles (skips if public.js already drew them) */
	var g=document.getElementById('heroCandles');
	if(g&&!g.children.length){
		var ns='http://www.w3.org/2000/svg',x=28,mid=200,seed=7;
		var rnd=function(){seed=(seed*16807)%2147483647;return seed/2147483647};
		for(var i=0;i<24;i++){
			var drift=-i*3.2,o=mid+drift+(rnd()-.5)*26,c=o+(rnd()-.42)*34;
			var hi=Math.min(o,c)-(4+rnd()*14),lo=Math.max(o,c)+(4+rnd()*14),up=c<=o;
			var w=document.createElementNS(ns,'line');
			w.setAttribute('x1',x+6);w.setAttribute('x2',x+6);
			w.setAttribute('y1',hi);w.setAttribute('y2',lo);
			w.setAttribute('class',up?'w-up':'w-down');
			w.style.animationDelay=(i*70)+'ms';
			g.appendChild(w);
			var r=document.createElementNS(ns,'rect');
			r.setAttribute('x',x);r.setAttribute('width',12);r.setAttribute('rx',2);
			r.setAttribute('y',Math.min(o,c));
			r.setAttribute('height',Math.max(4,Math.abs(c-o)));
			r.setAttribute('class',up?'c-up':'c-down');
			r.style.animationDelay=(i*70)+'ms';
			g.appendChild(r);
			x+=27;
		}
	}

	/* Candle countdown */
	var cd=document.getElementById('nfxCountdown');
	if(cd){
		var s=42;
		setInterval(function(){
			s=s>0?s-1:59;
			cd.textContent='Next candle 00:'+(s<10?'0':'')+s;
		},1000);
	}

	/* Live price tick flash */
	var pr=document.getElementById('nfxHeroPrice');
	if(pr&&!rm){
		var base=parseFloat(pr.textContent.replace(/,/g,''));
		if(base>0){
			setInterval(function(){
				var delta=(Math.random()-.48)*(base*.0003);
				base+=delta;
				pr.textContent=base.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
				pr.classList.remove('tick-up','tick-down');
				void pr.offsetWidth;
				pr.classList.add(delta>=0?'tick-up':'tick-down');
			},1800);
		}
	}

	/* Count-up hero stats */
	if(!rm){
		[].forEach.call(document.querySelectorAll('.hero-proof strong'),function(el){
			var m=el.textContent.trim().match(/^([\d,]+)(.*)$/);
			if(!m)return;
			var target=parseInt(m[1].replace(/,/g,''),10),suffix=m[2]||'';
			if(!target)return;
			var t0=null;
			var step=function(t){
				if(!t0)t0=t;
				var p=Math.min(1,(t-t0)/1200);
				p=1-Math.pow(1-p,3);
				el.textContent=Math.round(target*p).toLocaleString('en-US')+suffix;
				if(p<1)requestAnimationFrame(step);
			};
			requestAnimationFrame(step);
		});
	}
})();
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('publicMenuToggle');
    const nav = document.getElementById('publicNavMenu');
    if (btn && nav) {
        btn.addEventListener('click', () => {
            nav.classList.toggle('open');
            btn.classList.toggle('open');
        });
    }
});
</script>
</body>
</html>
