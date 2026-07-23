/* FX Command V3 dependency-free streaming canvas terminal */
(function(){
 const root=document.getElementById('terminal'); if(!root)return;
 const canvas=document.getElementById('terminalCanvas');
 const ctx=canvas ? canvas.getContext('2d') : null;
 const state={tf:'M1',data:null,layers:{fvg:true,volume:true,profile:true,trendlines:true,signals:true,bots:true},mouse:null};
 const $=id=>document.getElementById(id); let timer=null,countdownTimer=null;
 let fitW=0,fitH=0;
 function fit(){if(!canvas)return; const width=Math.max(1,canvas.clientWidth),height=Math.max(1,canvas.clientHeight);if(width===fitW&&height===fitH)return;fitW=width;fitH=height;const dpr=Math.min(devicePixelRatio||1,2);canvas.width=Math.round(width*dpr);canvas.height=Math.round(height*dpr);ctx.setTransform(dpr,0,0,dpr,0,0);draw();}
 function priceY(p,min,max,h,top){return top+(max-p)/(max-min)*h}
 function draw(){
  if(!canvas || !ctx)return;
  if(!state.data)return; const cs=state.data.candles||[];if(!cs.length)return;
  const w=canvas.clientWidth,h=canvas.clientHeight,pad={l:12,r:76,t:24,b:28};const volH=state.layers.volume?92:0;const chartH=h-pad.t-pad.b-volH;
  const theme=getComputedStyle(document.documentElement);const chartBg=(theme.getPropertyValue('--chart-bg')||'#1d1d1d').trim();const chartGrid=(theme.getPropertyValue('--chart-grid')||'rgba(255,255,255,.06)').trim();const chartText=(theme.getPropertyValue('--chart-text')||'rgba(255,255,255,.46)').trim();
  const min=Math.min(...cs.map(c=>c.low)),max=Math.max(...cs.map(c=>c.high)),range=(max-min)||1;const lo=min-range*.06,hi=max+range*.06;const cw=(w-pad.l-pad.r)/cs.length;
  ctx.clearRect(0,0,w,h);ctx.fillStyle=chartBg;ctx.fillRect(0,0,w,h);
  ctx.strokeStyle=chartGrid;ctx.lineWidth=1;ctx.font='11px Arial';ctx.fillStyle=chartText;
  for(let i=0;i<=6;i++){const y=pad.t+i*chartH/6;ctx.beginPath();ctx.moveTo(pad.l,y);ctx.lineTo(w-pad.r,y);ctx.stroke();const p=hi-(hi-lo)*i/6;ctx.fillText(formatPrice(p),w-pad.r+9,y+4)}
  if(state.layers.fvg){(state.data.overlays.fvg||[]).forEach(z=>{const x1=timeX(z.start_time,cs,cw,pad.l),x2=w-pad.r;const y1=priceY(z.top,lo,hi,chartH,pad.t),y2=priceY(z.bottom,lo,hi,chartH,pad.t);ctx.fillStyle=z.type==='bullish'?'rgba(114,188,143,.10)':'rgba(233,115,102,.10)';ctx.fillRect(x1,Math.min(y1,y2),Math.max(2,x2-x1),Math.abs(y2-y1));ctx.strokeStyle=z.type==='bullish'?'rgba(114,188,143,.38)':'rgba(233,115,102,.38)';ctx.strokeRect(x1,Math.min(y1,y2),Math.max(2,x2-x1),Math.abs(y2-y1))})}
  if(state.layers.trendlines){(state.data.overlays.trendlines||[]).forEach(l=>{ctx.strokeStyle=l.side==='buy'?'#72bc8f':'#e97366';ctx.lineWidth=2;ctx.beginPath();ctx.moveTo(timeX(l.start.time,cs,cw,pad.l),priceY(l.start.price,lo,hi,chartH,pad.t));ctx.lineTo(timeX(l.end.time,cs,cw,pad.l),priceY(l.end.price,lo,hi,chartH,pad.t));ctx.stroke()})}
  const maxVol=Math.max(1,...cs.map(c=>c.volume||0));cs.forEach((c,i)=>{const x=pad.l+i*cw+cw/2,yo=priceY(c.open,lo,hi,chartH,pad.t),yc=priceY(c.close,lo,hi,chartH,pad.t),yh=priceY(c.high,lo,hi,chartH,pad.t),yl=priceY(c.low,lo,hi,chartH,pad.t),up=c.close>=c.open,col=up?'#72bc8f':'#e97366';ctx.strokeStyle=col;ctx.beginPath();ctx.moveTo(x,yh);ctx.lineTo(x,yl);ctx.stroke();ctx.fillStyle=col;ctx.fillRect(x-Math.max(1,cw*.31),Math.min(yo,yc),Math.max(2,cw*.62),Math.max(1,Math.abs(yc-yo)));if(state.layers.volume){const vh=(c.volume||0)/maxVol*(volH-16);ctx.globalAlpha=.35;ctx.fillRect(x-Math.max(1,cw*.28),h-pad.b-vh,Math.max(2,cw*.56),vh);ctx.globalAlpha=1}});
  if(state.layers.signals){(state.data.signals||[]).forEach(s=>{const idx=nearestIndex(s.generated_at||cs[cs.length-1].time,cs),x=pad.l+idx*cw+cw/2,y=priceY(s.entry,lo,hi,chartH,pad.t);ctx.fillStyle=s.direction==='buy'?'#72bc8f':'#e97366';ctx.beginPath();if(s.direction==='buy'){ctx.moveTo(x,y-14);ctx.lineTo(x-7,y-3);ctx.lineTo(x+7,y-3)}else{ctx.moveTo(x,y+14);ctx.lineTo(x-7,y+3);ctx.lineTo(x+7,y+3)}ctx.closePath();ctx.fill();ctx.font='bold 10px Arial';ctx.fillText(s.strategy,x+9,y+4)})}
  if(state.layers.bots){(state.data.bot_trades||[]).forEach(t=>{const idx=nearestIndex(t.opened_at||cs[cs.length-1].time,cs),x=pad.l+idx*cw+cw/2,y=priceY(t.entry,lo,hi,chartH,pad.t),col=t.direction==='buy'?'#5e9fe8':'#de9255';
   if(t.status==='open'){ctx.setLineDash([5,4]);ctx.lineWidth=1;ctx.strokeStyle=col;ctx.beginPath();ctx.moveTo(x,y);ctx.lineTo(w-pad.r,y);ctx.stroke();const ysl=priceY(t.stop_loss,lo,hi,chartH,pad.t),ytp=priceY(t.take_profit,lo,hi,chartH,pad.t);ctx.strokeStyle='rgba(233,115,102,.55)';ctx.beginPath();ctx.moveTo(x,ysl);ctx.lineTo(w-pad.r,ysl);ctx.stroke();ctx.strokeStyle='rgba(114,188,143,.55)';ctx.beginPath();ctx.moveTo(x,ytp);ctx.lineTo(w-pad.r,ytp);ctx.stroke();ctx.setLineDash([])}
   ctx.fillStyle=col;ctx.beginPath();ctx.moveTo(x,y-8);ctx.lineTo(x+7,y);ctx.lineTo(x,y+8);ctx.lineTo(x-7,y);ctx.closePath();ctx.fill();ctx.font='bold 10px Arial';ctx.fillText('EA · '+((t.bot||'bot')+'').slice(0,14),x+11,y+4)})}
  if(state.mouse&&state.mouse.x<w-pad.r){ctx.setLineDash([4,4]);ctx.strokeStyle=(theme.getPropertyValue('--chart-cross')||'rgba(255,255,255,.28)').trim();ctx.beginPath();ctx.moveTo(state.mouse.x,pad.t);ctx.lineTo(state.mouse.x,h-pad.b);ctx.moveTo(pad.l,state.mouse.y);ctx.lineTo(w-pad.r,state.mouse.y);ctx.stroke();ctx.setLineDash([])}
 }
 function timeX(t,cs,cw,left){const i=nearestIndex(t,cs);return left+i*cw+cw/2} function nearestIndex(t,cs){let best=0,d=Infinity;cs.forEach((c,i)=>{const x=Math.abs(c.time-t);if(x<d){d=x;best=i}});return best}
 function formatPrice(v){return v>=1000?v.toLocaleString(undefined,{maximumFractionDigits:2}):v.toFixed(v<10?5:2)}
 function renderPlan(){const d=state.data,el=$('tradePlan'),p=d.primary_signal;
  if(d.feed.status==='demo'){el.innerHTML='<div class="terminal-empty">DEMO feed — no trade plan is generated.</div>';return}
  if(!p){el.innerHTML='<div class="terminal-empty">No clear setup right now.<br>Staying flat is the plan.</div>';return}
  const rr=p.risk_reward?Number(p.risk_reward).toFixed(1):'—';
  el.innerHTML=`<div class="plan-decision ${p.direction==='buy'?'plan-buy':'plan-sell'}"><span>${p.direction==='buy'?'▲ BUY':'▼ SELL'} ${d.symbol}</span><b>${p.confidence}%</b></div>
  <div class="plan-grid"><div><span>Entry</span><b>${formatPrice(p.entry)}</b></div><div><span>Stop loss</span><b class="down">${formatPrice(p.stop_loss)}</b></div><div><span>Take profit</span><b class="up">${formatPrice(p.take_profit)}</b></div><div><span>R:R</span><b>1:${rr}</b></div></div>
  <div class="plan-note">${p.strategy} · ${p.note||''}</div>
  ${p.expires_at?`<div class="plan-expiry">Valid until ${new Date(p.expires_at*1000).toLocaleTimeString()}</div>`:''}`}
 function renderAnalysis(){const a=(state.data&&state.data.analysis)||{};const list=$('analysisList');
  $('analysisMeta').textContent=a.confidence?`${(a.bias||'').toUpperCase()} · ${a.confidence}%`:'';
  const checks=(a.details&&a.details.checks)||[];
  list.innerHTML=checks.length?checks.map(c=>`<div class="analysis-row"><i class="analysis-dot dot-${c.state}"></i><span>${c.label}</span><b>${c.value}</b></div>`).join(''):'<div class="terminal-empty">Run a scan to build the analysis.</div>';
  $('analysisVerdict').textContent=(a.details&&a.details.verdict)||a.summary||''}
 function renderPanels(){const d=state.data;renderPlan();renderAnalysis();$('livePrice').textContent=formatPrice(d.price);$('liveChange').textContent=(d.change_pct>=0?'+':'')+Number(d.change_pct).toFixed(2)+'%';$('liveChange').className=d.change_pct>=0?'up':'down';$('feedBadge').textContent=(d.feed.status+' · '+d.feed.source).toUpperCase();$('feedBadge').className='feed-chip feed-'+d.feed.status;
  const bt=d.bot_trades||[];const bc=$('botCount');if(bc)bc.textContent=bt.filter(t=>t.status==='open').length+' open';const btl=$('botTrades');
  if(btl)btl.innerHTML=bt.length?bt.map(t=>`<div class="entry-row"><span class="badge ${t.direction==='buy'?'badge-buy':'badge-sell'}">${t.direction.toUpperCase()}</span><div><strong>${t.bot||'EA bot'} · ${String(t.mode||'').toUpperCase()}</strong><small>Entry ${formatPrice(t.entry)} · SL ${formatPrice(t.stop_loss)} · TP ${formatPrice(t.take_profit)} · risk $${Number(t.risk_amount).toFixed(2)}</small></div><b class="${t.status==='open'?'':(Number(t.pnl)>=0?'up':'down')}">${t.status==='open'?'OPEN':(Number(t.pnl)>=0?'+':'')+Number(t.pnl).toFixed(2)}</b></div>`).join(''):'<div class="terminal-empty">No bot entries on this market yet.</div>';
  const secondary=d.signals.filter(s=>!s.is_primary);
  $('entryList').innerHTML=secondary.length?secondary.map(s=>`<div class="entry-row"><span class="badge ${s.direction==='buy'?'badge-buy':'badge-sell'}">${s.direction.toUpperCase()}</span><div><strong>${s.strategy}</strong><small>Entry ${formatPrice(s.entry)} · SL ${formatPrice(s.stop_loss)}</small></div><b>${s.confidence}%</b></div>`).join(''):'<div class="terminal-empty">No supporting entries — the primary plan stands alone.</div>';
  const f=d.overlays.fvg||[];$('fvgCount').textContent=f.length+' zones';$('fvgList').innerHTML=f.slice().reverse().slice(0,6).map(z=>`<div class="fvg-row"><span class="${z.type==='bullish'?'up':'down'}">${z.type.toUpperCase()}</span><b>${formatPrice(z.bottom)}–${formatPrice(z.top)}</b></div>`).join('')||'<div class="terminal-empty">No recent FVG.</div>';
  const p=d.overlays.volume_profile||[];$('volumeProfile').innerHTML=p.slice(0,12).map(x=>`<div class="profile-row"><span>${formatPrice(x.price)}</span><i><em style="width:${x.percent}%"></em></i><b>${x.percent}%</b></div>`).join('');
  const warn=$('terminalWarning');if(d.feed.status==='demo'){warn.hidden=false;warn.textContent='DEMO feed: chart and signals must not be used for trading. '+(d.feed.error||'Configure a market provider.')}else warn.hidden=true;
 }
 async function load(fresh=false){$('terminalStatus').textContent='Updating…';try{const r=await fetch(root.dataset.endpoint+'?timeframe='+state.tf+(fresh?'&fresh=1':''),{headers:{Accept:'application/json'}});if(!r.ok)throw new Error('HTTP '+r.status);state.data=await r.json();renderPanels();draw();$('terminalStatus').textContent='Updated '+new Date().toLocaleTimeString();startCountdown()}catch(e){$('terminalStatus').textContent='Feed error';const w=$('terminalWarning');w.hidden=false;w.textContent='Unable to update terminal: '+e.message}}
 function startCountdown(){clearInterval(countdownTimer);const tick=()=>{if(!state.data)return;const left=Math.max(0,state.data.next_candle_at-Math.floor(Date.now()/1000));const hh=Math.floor(left/3600),mm=Math.floor((left%3600)/60),ss=left%60;$('candleCountdown').textContent=(hh?String(hh).padStart(2,'0')+':':'')+String(mm).padStart(2,'0')+':'+String(ss).padStart(2,'0')};tick();countdownTimer=setInterval(tick,1000)}
 document.querySelectorAll('.tf-button').forEach(b=>b.addEventListener('click',()=>{document.querySelectorAll('.tf-button').forEach(x=>x.classList.remove('active'));b.classList.add('active');state.tf=b.dataset.timeframe;load(true)}));document.querySelectorAll('[data-layer]').forEach(x=>x.addEventListener('change',()=>{state.layers[x.dataset.layer]=x.checked;draw()}));$('marketSelect').addEventListener('change',e=>location.href=e.target.value);
 if(canvas){
  canvas.addEventListener('mousemove',e=>{const r=canvas.getBoundingClientRect();state.mouse={x:e.clientX-r.left,y:e.clientY-r.top};if(state.data){const cs=state.data.candles,w=canvas.clientWidth,cw=(w-88)/cs.length,idx=Math.max(0,Math.min(cs.length-1,Math.floor((state.mouse.x-12)/cw))),c=cs[idx];$('ohlcTooltip').textContent=new Date(c.time*1000).toLocaleString()+'  O '+formatPrice(c.open)+'  H '+formatPrice(c.high)+'  L '+formatPrice(c.low)+'  C '+formatPrice(c.close)}draw()});
  canvas.addEventListener('mouseleave',()=>{state.mouse=null;draw()});
  new ResizeObserver(()=>requestAnimationFrame(fit)).observe(canvas);
 }
 window.addEventListener('fx-theme-change',draw);
 document.addEventListener('visibilitychange',()=>{if(document.hidden){clearInterval(timer);timer=null}else{load(false);timer=setInterval(()=>load(false),5000)}});
 fit();load(true);timer=setInterval(()=>load(false),5000);
})();
