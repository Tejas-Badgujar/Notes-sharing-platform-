<?php 
$level = '../'; 
$page_title = 'Settings';
include '../includes/header.php'; 

$user_theme = '';
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $s1 = $conn->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
    $s1->bind_param("i", $uid); $s1->execute();
    $s2 = $conn->prepare("SELECT theme FROM users WHERE id = ?");
    $s2->bind_param("i", $uid); $s2->execute();
    $themeQ = $s2->get_result();
    if ($themeQ && $row = $themeQ->fetch_assoc()) {
        $user_theme = $row['theme'] ?? '';
    }
}
?>
<html lang="en" class="<?= htmlspecialchars($user_theme) ?>">
<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>

    <main class="dashboard-content">
        <h2 class="animate-fade-in-up" style="margin-bottom: 2rem;">Settings</h2>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem;">
            
            <!-- Left Side: Theme & App Settings -->
            <div style="display: flex; flex-direction: column; gap: 2.5rem;">
                
                <!-- Theme Selector -->
                <section class="animate-fade-in-up">
                    <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.8rem;">
                        <i class="fa-solid fa-palette" style="color: var(--accent-cyan);"></i> Visual Themes
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.2rem;" id="theme-grid">
                        
                        <!-- 1. Default (Tech) -->
                        <div class="theme-card active" onclick="setTheme('', event)" data-theme="">
                            <div class="theme-preview" style="background: #05050a;">
                                <div style="background: #00f3ff; width: 40%; height: 6px; border-radius: 4px;"></div>
                                <div style="background: #8a2be2; width: 60%; height: 6px; border-radius: 4px;"></div>
                            </div>
                            <p>Default Tech</p>
                        </div>

                        <!-- 2. Black Gold -->
                        <div class="theme-card" onclick="setTheme('theme-gold', event)" data-theme="theme-gold">
                            <div class="theme-preview" style="background: #0a0a0a;">
                                <div style="background: #d4af37; width: 40%; height: 6px; border-radius: 4px;"></div>
                                <div style="background: #aa8833; width: 60%; height: 6px; border-radius: 4px;"></div>
                            </div>
                            <p>Black Gold</p>
                        </div>

                        <!-- 3. Pinky Kitten -->
                        <div class="theme-card" onclick="setTheme('theme-pinky', event)" data-theme="theme-pinky">
                            <div class="theme-preview" style="background: #fff0f5; border: 1px solid #ffd1dc;">
                                <div style="background: #ff69b4; width: 40%; height: 6px; border-radius: 4px;"></div>
                                <div style="background: #db7093; width: 60%; height: 6px; border-radius: 4px;"></div>
                                <i class="fa-solid fa-cat" style="position: absolute; bottom: 5px; right: 5px; font-size: 0.8rem; color: #ff69b4;"></i>
                            </div>
                            <p>Pinky Kitten</p>
                        </div>

                        <!-- 4. Demon -->
                        <div class="theme-card" onclick="setTheme('theme-demon', event)" data-theme="theme-demon">
                            <div class="theme-preview" style="background: #0d0000;">
                                <div style="background: #ff0000; width: 40%; height: 6px; border-radius: 4px;"></div>
                                <div style="background: #800000; width: 60%; height: 6px; border-radius: 4px;"></div>
                                <i class="fa-solid fa-skull" style="position: absolute; bottom: 5px; right: 5px; font-size: 0.8rem; color: #ff0000;"></i>
                            </div>
                            <p>Demon</p>
                        </div>

                        <!-- 5. Multicolor White -->
                        <div class="theme-card" onclick="setTheme('theme-light', event)" data-theme="theme-light">
                            <div class="theme-preview" style="background: #f8f9fa; border: 1px solid #ddd;">
                                <div style="background: #4cc9f0; width: 40%; height: 6px; border-radius: 4px;"></div>
                                <div style="background: #7209b7; width: 60%; height: 6px; border-radius: 4px;"></div>
                            </div>
                            <p>Light Glow</p>
                        </div>

                    </div>
                </section>

                <!-- Preferences -->
                <section class="animate-fade-in-up delay-100">
                    <h3 style="margin-bottom: 1.5rem;">Preferences</h3>
                    <div class="glass-panel" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="margin: 0; font-weight: 500;">Email Notifications</p>
                                <p style="margin: 0; font-size: 0.8rem; color: var(--text-secondary);">Receive updates about your note activity.</p>
                            </div>
                            <input type="checkbox" id="pref-email" onchange="savePref('email', this.checked)" style="width: 20px; height: 20px; accent-color: var(--accent-cyan);">
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="margin: 0; font-weight: 500;">High Quality Previews</p>
                                <p style="margin: 0; font-size: 0.8rem; color: var(--text-secondary);">Always load 2x quality PDF previews.</p>
                            </div>
                            <input type="checkbox" id="pref-quality" onchange="savePref('quality', this.checked)" style="width: 20px; height: 20px; accent-color: var(--accent-cyan);">
                        </div>
                    </div>
                </section>

            </div>

            <!-- Right Side: Account Quick Links -->
            
            </div>

        </div>
    </main>
</div>

<!-- SVG filter for DOM displacement (hidden) -->
<svg id="water-svg" xmlns="http://www.w3.org/2000/svg" style="position:fixed;width:0;height:0;pointer-events:none;">
  <defs>
    <filter id="wf" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">
      <feTurbulence id="wf-tb" type="turbulence" baseFrequency="0.012 0.008" numOctaves="3" seed="5" result="noise"/>
      <feDisplacementMap id="wf-dm" in="SourceGraphic" in2="noise" scale="0" xChannelSelector="R" yChannelSelector="G"/>
    </filter>
  </defs>
</svg>

<style>
.theme-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: var(--transition-smooth);
    position: relative;
}
.theme-card p { margin-top: 0.8rem; font-size: 0.85rem; font-weight: 500; }
.theme-preview {
    height: 80px; border-radius: 10px;
    display: flex; flex-direction: column;
    justify-content: center; align-items: center;
    gap: 8px; position: relative; overflow: hidden;
}
.theme-card.active {
    border-color: var(--accent-cyan);
    background: rgba(0,243,255,0.05);
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
.theme-card:hover { transform: translateY(-3px); }

/* Water canvas sits on top, drawn by JS */
#water-canvas {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 999997;
    mix-blend-mode: overlay;  /* overlay avoids white bleed-out */
}
</style>

<script>
/* ================================================================
   REALISTIC WATER RIPPLE ENGINE
   - 2D wave equation physics on a downscaled grid
   - Canvas rendered with surface normals + specular + caustics
   - SVG feDisplacementMap distorts DOM content
   - Web Audio API procedural splash sound
================================================================ */

const WATER = (() => {
  const S = 4; // grid cell size in pixels (lower = more detail, slower)
  let cols, rows, cur, prv, canvas, ctx, raf = null;
  let tint = {r:0,g:220,b:255};
  let domTarget = null;
  let turbEl, dispEl;
  let svgAnimRaf = null;
  let paused = false, stopTimer = null;

  const TINTS = {
    '':           {r:0,   g:220, b:255},
    'theme-gold': {r:255, g:195, b:40 },
    'theme-pinky':{r:255, g:80,  b:180},
    'theme-demon':{r:255, g:20,  b:20 },
    'theme-light':{r:140, g:60,  b:255},
  };

  function idx(x,y){ return y*cols+x; }

  function resize(){
    cols = Math.ceil(window.innerWidth  / S);
    rows = Math.ceil(window.innerHeight / S);
    cur  = new Float32Array(cols * rows);
    prv  = new Float32Array(cols * rows);
    if(canvas){ canvas.width=window.innerWidth; canvas.height=window.innerHeight; }
  }

  function hardStop(){
    if(cur) cur.fill(0);
    if(prv) prv.fill(0);
    if(raf){ cancelAnimationFrame(raf); raf=null; }
    if(svgAnimRaf){ cancelAnimationFrame(svgAnimRaf); svgAnimRaf=null; }
    if(domTarget) domTarget.style.filter='';
    if(dispEl) dispEl.setAttribute('scale','0');
    if(ctx && canvas) ctx.clearRect(0,0,canvas.width,canvas.height);
  }

  function init(){
    canvas = document.createElement('canvas');
    canvas.id = 'water-canvas';
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
    document.body.appendChild(canvas);
    ctx = canvas.getContext('2d');
    resize();
    window.addEventListener('resize', resize);
    turbEl = document.getElementById('wf-tb');
    dispEl = document.getElementById('wf-dm');
    domTarget = document.querySelector('.dashboard-layout') || document.body;
    /* Pause RAF when tab hidden; resume on return */
    document.addEventListener('visibilitychange',()=>{
      if(document.hidden){
        if(raf){ cancelAnimationFrame(raf); raf=null; }
        if(svgAnimRaf){ cancelAnimationFrame(svgAnimRaf); svgAnimRaf=null; }
        paused=true;
      } else if(paused){
        paused=false;
        if(cur && cur.some(v=>Math.abs(v)>0.3)) raf=requestAnimationFrame(animate);
      }
    });
  }

  function drop(px, py, radius, strength){
    const gx=Math.round(px/S), gy=Math.round(py/S), gr=Math.round(radius/S);
    for(let dy=-gr;dy<=gr;dy++) for(let dx=-gr;dx<=gr;dx++){
      const d=Math.sqrt(dx*dx+dy*dy);
      if(d<=gr){ const x=gx+dx,y=gy+dy;
        if(x>0&&x<cols-1&&y>0&&y<rows-1)
          cur[idx(x,y)] += strength*(1-d/gr)*Math.cos(d/gr*Math.PI*0.5);
      }
    }
  }

  function step(){
    for(let y=1;y<rows-1;y++) for(let x=1;x<cols-1;x++){
      const i=idx(x,y);
      prv[i]=( cur[idx(x-1,y)]+cur[idx(x+1,y)]+cur[idx(x,y-1)]+cur[idx(x,y+1)] )*0.5 - prv[i];
      prv[i]*=0.975; /* faster decay → ~2.5s lifetime */
    }
    const t=cur; cur=prv; prv=t;
  }

  function draw(){
    const W=canvas.width, H=canvas.height;
    const id=ctx.createImageData(cols,rows);
    const d=id.data;
    const {r:tr,g:tg,b:tb}=tint;
    let hasActivity=false;

    for(let y=0;y<rows;y++) for(let x=0;x<cols;x++){
      const h=cur[idx(x,y)];
      if(Math.abs(h)<0.3) continue;
      hasActivity=true;
      // surface normal from neighbours
      const hL=x>0?cur[idx(x-1,y)]:h, hR=x<cols-1?cur[idx(x+1,y)]:h;
      const hU=y>0?cur[idx(x,y-1)]:h, hD=y<rows-1?cur[idx(x,y+1)]:h;
      const nx=(hL-hR)*0.5, ny=(hU-hD)*0.5;
      // specular (light from top-left)
      const nLen=Math.sqrt(nx*nx+ny*ny+1);
      const spec=Math.pow(Math.max(0,(nx*0.577+ny*0.577+1)/nLen),12)*3;
      const caustic=Math.max(0,h/120)*1.8;
      const pi=(y*cols+x)*4;
      const intensity=Math.min(1,Math.abs(h)/100);
      if(h>0){
        d[pi  ]=Math.min(255,tr*intensity+spec*70+caustic*50);
        d[pi+1]=Math.min(255,tg*intensity+spec*60+caustic*40);
        d[pi+2]=Math.min(255,tb*intensity+spec*70+caustic*25);
        d[pi+3]=Math.min(155,intensity*150+spec*50);  /* no white bleed */
      } else {
        d[pi  ]=Math.min(255,tr*0.05*intensity);
        d[pi+1]=Math.min(255,tg*0.05*intensity);
        d[pi+2]=Math.min(255,tb*0.18*intensity);
        d[pi+3]=Math.min(90, intensity*100);
      }
    }

    // Draw scaled-up water to full canvas
    const tmp=document.createElement('canvas');
    tmp.width=cols; tmp.height=rows;
    tmp.getContext('2d').putImageData(id,0,0);
    ctx.clearRect(0,0,W,H);
    ctx.imageSmoothingEnabled=true;
    ctx.imageSmoothingQuality='high';
    ctx.drawImage(tmp,0,0,W,H);
    return hasActivity;
  }

  function animate(){
    step();
    const alive=draw();
    if(alive) raf=requestAnimationFrame(animate);
    else { ctx.clearRect(0,0,canvas.width,canvas.height); raf=null; }
  }

  /* SVG displacement animation driven by wave energy */
  function animateSVGDisplace(duration){
    if(svgAnimRaf) cancelAnimationFrame(svgAnimRaf);
    if(!turbEl||!dispEl||!domTarget) return;
    domTarget.style.filter='url(#wf)';
    const start=performance.now();
    function tick(ts){
      const t=Math.min((ts-start)/duration,1);
      // bell-shaped envelope: rises then falls
      const env=Math.sin(t*Math.PI)*(1-t*0.4);
      const scale=env*38;
      const freq=(0.006+t*0.022).toFixed(5);
      turbEl.setAttribute('baseFrequency',`${freq} ${(freq*0.65).toFixed(5)}`);
      turbEl.setAttribute('seed', String(Math.floor(t*80)));
      dispEl.setAttribute('scale', scale.toFixed(2));
      if(t<1) svgAnimRaf=requestAnimationFrame(tick);
      else { domTarget.style.filter=''; dispEl.setAttribute('scale','0'); }
    }
    svgAnimRaf=requestAnimationFrame(tick);
  }

  /* Stone-in-water splash sound via Web Audio API */
  function splashSound(){
    try{
      const AC=window.AudioContext||window.webkitAudioContext;
      const ac=new AC();
      const t=ac.currentTime;

      /* ① "Plop" — air bubble collapse: pitch drops 350→60 Hz */
      const osc=ac.createOscillator(), og=ac.createGain();
      osc.type='sine';
      osc.frequency.setValueAtTime(350,t);
      osc.frequency.exponentialRampToValueAtTime(55,t+0.22);
      og.gain.setValueAtTime(0.75,t);
      og.gain.exponentialRampToValueAtTime(0.001,t+0.28);
      osc.connect(og); og.connect(ac.destination);
      osc.start(t); osc.stop(t+0.28);

      /* ② Splash burst — band-pass filtered noise */
      const sr=ac.sampleRate, blen=Math.floor(sr*0.3);
      const nbuf=ac.createBuffer(1,blen,sr);
      const nd=nbuf.getChannelData(0);
      for(let i=0;i<blen;i++) nd[i]=(Math.random()*2-1)*Math.exp(-i/(sr*0.035));
      const ns=ac.createBufferSource(); ns.buffer=nbuf;
      const bp=ac.createBiquadFilter(); bp.type='bandpass';
      bp.frequency.value=1100; bp.Q.value=1.4;
      const ng=ac.createGain(); ng.gain.setValueAtTime(0.45,t);
      ns.connect(bp); bp.connect(ng); ng.connect(ac.destination);
      ns.start(t);

      /* ③ Low water-body rumble */
      const osc2=ac.createOscillator(), og2=ac.createGain();
      osc2.type='sine';
      osc2.frequency.setValueAtTime(95,t+0.02);
      osc2.frequency.exponentialRampToValueAtTime(35,t+1.1);
      og2.gain.setValueAtTime(0.28,t+0.03);
      og2.gain.exponentialRampToValueAtTime(0.001,t+1.2);
      osc2.connect(og2); og2.connect(ac.destination);
      osc2.start(t+0.02); osc2.stop(t+1.2);

      /* ④ Faint ripple echo at 180ms */
      const osc3=ac.createOscillator(), og3=ac.createGain();
      osc3.type='sine';
      osc3.frequency.setValueAtTime(180,t+0.18);
      osc3.frequency.exponentialRampToValueAtTime(50,t+0.45);
      og3.gain.setValueAtTime(0.18,t+0.18);
      og3.gain.exponentialRampToValueAtTime(0.001,t+0.5);
      osc3.connect(og3); og3.connect(ac.destination);
      osc3.start(t+0.18); osc3.stop(t+0.5);

      setTimeout(()=>{ try{ac.close();}catch(e){} },2800);
    }catch(e){}
  }

  function trigger(px, py, theme){
    if(stopTimer) clearTimeout(stopTimer);
    paused = false;
    tint = TINTS[theme] || TINTS[''];
    drop(px, py, 55, 600);
    setTimeout(()=>drop(px+8,py-5,22,220),80);
    setTimeout(()=>drop(px-6,py+4,15,150),140);
    if(!raf) raf=requestAnimationFrame(animate);
    animateSVGDisplace(2000); /* SVG distortion max 2s */
    splashSound();
    stopTimer = setTimeout(hardStop, 2500); /* hard cap at 2.5s */
  }

  return { init, trigger };
})();

/* ── Theme helpers ── */
const ALL_THEMES=['theme-gold','theme-pinky','theme-demon','theme-tech','theme-light'];

function applyThemeClasses(name){
  ALL_THEMES.forEach(t=>{
    document.documentElement.classList.remove(t);
    document.body.classList.remove(t);
  });
  if(name){ document.documentElement.classList.add(name); document.body.classList.add(name); }
}

function updateActiveCard(name){
  document.querySelectorAll('.theme-card').forEach(c=>{
    c.classList.toggle('active', c.getAttribute('data-theme')===name);
  });
}

function setTheme(themeName, event){
  let ox=window.innerWidth/2, oy=window.innerHeight/2;
  if(event){
    const r=event.currentTarget.getBoundingClientRect();
    ox=r.left+r.width/2; oy=r.top+r.height/2;
  }
  WATER.trigger(ox, oy, themeName);
  // Apply theme mid-wave (wave is spreading at ~250ms)
  setTimeout(()=>{
    applyThemeClasses(themeName);
    localStorage.setItem('notes-platform-theme', themeName);
    updateActiveCard(themeName);
    /* Save to DB if user is logged in (silent AJAX) */
    <?php if(isset($_SESSION['user_id'])): ?>
    fetch('../ajax/save_theme.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'theme=' + encodeURIComponent(themeName)
    }).catch(()=>{});
    <?php endif; ?>
  }, 350);
}

document.addEventListener('DOMContentLoaded',()=>{
  WATER.init();
  // Use DB theme as source of truth (set by PHP from the user's DB record)
  const dbTheme = '<?= htmlspecialchars($user_theme) ?>';
  applyThemeClasses(dbTheme); updateActiveCard(dbTheme);
  // Also sync localStorage for consistency
  localStorage.setItem('notes-platform-theme', dbTheme);
  // restore prefs
  ['email','quality'].forEach(k=>{
    const v=localStorage.getItem('notes-pref-'+k);
    const el=document.getElementById('pref-'+k);
    if(el) {
      if(v!==null) el.checked=(v==='true');
      else if(k!=='quality') el.checked=true;
    }
  });
});

function savePref(key, value) {
    localStorage.setItem('notes-pref-' + key, value);
    // Visual feedback
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; background: var(--accent-cyan); color: #000;
        padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 600; font-size: 0.9rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; animation: slideIn 0.4s forwards;
    `;
    toast.innerHTML = `<i class="fa-solid fa-check-circle"></i> Preference updated!`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.4s forwards';
        setTimeout(() => toast.remove(), 400);
    }, 2500);
}


</script>

<style>
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
</style>

<?php include '../includes/footer.php'; ?>
