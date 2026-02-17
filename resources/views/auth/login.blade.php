<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>تسجيل الدخول</title>

  <!-- Rive (Canvas runtime) -->
  <script src="https://unpkg.com/@rive-app/canvas@latest"></script>
  <!-- Anime.js (تأثيرات الحروف والآيكونز والزر) -->
 <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script> -->
  <!-- Particles.js (الخلفية التفاعلية) -->
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
  <!-- Lottie (رس   وم JSON) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

  <style>
    /* هزّة الكارت */
    @keyframes shakeX {
      10%, 90% { transform: translateX(-2px); }
      20%, 80% { transform: translateX(4px); }
      30%, 50%, 70% { transform: translateX(-8px); }
      40%, 60% { transform: translateX(8px); }
    }
    .card.error-anim { animation: shakeX 0.6s ease both; }
    .field.error input {
      border-color: #ef4444;
      box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.15);
    }

    :root{
      --bg:#0f172a;
      --card:#ffffff;
      --text:#1f2937;
      --muted:#6b7280;
      --primary:#2563eb;
      --primary-dark:#1e40af;
      --ring: rgba(37,99,235,0.2);
      --ok:#16a34a;
      --danger:#ef4444;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Arabic", "Cairo", Arial, "Apple Color Emoji","Segoe UI Emoji";
      color:var(--text);
      display:flex; align-items:center; justify-content:center; padding:24px;
      overflow:hidden;
    }

    .bg-gradient{
      position: fixed; inset:0; z-index:-2;
      background: radial-gradient(1200px 800px at 80% -10%, #1e3a8a, transparent 60%),
                  radial-gradient(1000px 700px at -10% 110%, #581c87, transparent 60%),
                  linear-gradient(135deg, #0f172a, #111827 30%, #0b1221 70%, #111827);
      filter: hue-rotate(0deg);
      animation: hue 18s linear infinite;
    }
    @keyframes hue { to { filter: hue-rotate(360deg); } }

    #particles-js{
      position: fixed; inset:0; z-index:-1; pointer-events:none;
    }

    .card{
      width:min(420px,92vw);
      background:var(--card);
      border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.15);
      padding:24px 22px 26px; position:relative; overflow:hidden;
      border:1px solid #e5e7eb;
      backdrop-filter: blur(3px);
    }
    .rive-wrap{display:flex; justify-content:center; margin-block:8px 6px; position:relative;}
    canvas{width:320px; height:320px; max-width:75vw}
    h1{margin:0 0 6px; font-weight:700; font-size:22px}
    p.lead{margin:0 0 18px; color:var(--muted); font-size:14px}

    label{display:block; font-weight:700; margin-bottom:8px}
    .field{margin-bottom:14px}
    .input-wrap{position:relative}
    input[type="text"],input[type="password"]{
      width:100%; height:52px; border-radius:10px; border:1px solid #e5e7eb; padding:0 44px 0 14px;
      font-size:16px; background:#f9fbff;
      outline:none; transition:border-color .2s, box-shadow .2s, background-color .2s;
    }
    input:focus{border-color:var(--primary); box-shadow:0 0 0 6px var(--ring)}
    .actions{margin-top:10px}

    .btn{
      position:relative;
      width:100%; height:52px; border:none; border-radius:10px; cursor:pointer;
      background:var(--primary); color:#fff; font-weight:800; font-size:18px;
      transition:background .2s, transform .04s, box-shadow .2s;
      overflow:hidden;
    }
    .btn:hover{background:var(--primary-dark)}
    .btn:active{transform:translateY(1px)}
    .btn .ripple{
      position:absolute; border-radius:50%; transform:scale(0); opacity:.4;
      width:20px; height:20px; background:#fff; pointer-events:none;
      animation:ripple .6s ease-out forwards;
      mix-blend-mode: overlay;
    }
    @keyframes ripple {
      to { transform:scale(18); opacity:0; }
    }
    .btn.loading{ pointer-events:none; background:#1d4ed8; }
    .btn.loading .btn-label{opacity:0}
    .btn .loader{ position:absolute; inset:0; display:none; align-items:center; justify-content:center; }
    .btn.loading .loader{display:flex}
    .spinner{
      width:22px; height:22px; border:3px solid rgba(255,255,255,.35); border-top-color:#fff; border-radius:50%;
      animation:spin .9s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }
    .btn.success{ background:var(--ok); }
    .btn.error-soft{ background:var(--danger); }

    .error{
      background:#fee2e2; border:1px solid #fecaca; color:#991b1b;
      padding:10px 12px; border-radius:10px; font-size:14px; margin-bottom:12px;
      transform-origin: top center; animation: pop .24s ease;
    }
    @keyframes pop {
      0%{ transform: scale(.86); opacity:.2; }
      100%{ transform: scale(1); opacity:1; }
    }
/* تمييز الحقول عند الخطأ بدون التأثير على رسالة الخطأ */
.field.field-error input {
  border-color: #ef4444;
  box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.15);
}

    .ghost-layer{ position:absolute; inset:0; pointer-events:none; overflow:hidden; }
    .ghost-char{
      position:absolute; top:50%; transform:translate(-50%,-50%);
      font-size:16px; color:#0f172a; opacity:0; font-weight:700;
      will-change: transform, opacity; user-select:none;
    }

    .pass-ghost-layer{
      position:absolute; inset:0; pointer-events:none; overflow:hidden;
      display:flex; align-items:center; padding-inline:14px 44px; gap:8px;
    }
    .pass-ghost-layer.rtl{ justify-content:flex-end; }
    .pass-ghost-layer.ltr{ justify-content:flex-start; }

    .pass-dot svg{ width:10px; height:10px; fill:#fff; opacity:.9; }

    .mask-password{ color: transparent !important; caret-color: #111827; text-shadow:none; }

    .toggle-visibility{
      position:absolute; inset-inline-end: 8px; top:50%; transform: translateY(-50%);
      width:34px; height:34px; border:none; background:transparent; cursor:pointer; border-radius:8px;
      display:grid; place-items:center;
    }
    .toggle-visibility:hover{ background:#eef2ff; }
    .toggle-visibility svg{ width:22px; height:22px; }
    .icon-eye, .icon-lock{ display:block; }

    .lottie-wrap{ height:64px; display:flex; justify-content:center; align-items:center; margin-bottom:8px; }
    .lottie-wrap[hidden]{ display:none; }

    /* ترقيعات */
    .lottie-wrap { height: 0 !important; overflow: hidden; transition: height .25s ease; }
    .lottie-wrap.show { height: 200px !important; }
    @media (max-width:480px){ .lottie-wrap.show { height: 150px !important; } }

    canvas#riveCanvas.rive-hidden { opacity: .001; pointer-events: none; }

    /* لوتي فوق الدب */
    #lottieHello {
      position: absolute !important;
      inset: 0 !important;
      margin: 0 !important;
      height: 100% !important;
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 2;
    }
    #lottieHello.show { display: flex; }
    #riveCanvas.is-hidden { visibility: hidden; }
  </style>
</head>
<body>

<div class="bg-gradient" aria-hidden="true"></div>
<div id="particles-js" aria-hidden="true"></div>

<div class="card" id="card">
  <div class="rive-wrap">
    <canvas id="riveCanvas" width="320" height="320" aria-label="Animated Login Mascot"></canvas>
    <!-- هننقل لوتي هنا برضه بالسكربت -->
  </div>

  <!-- لوتي ترحيب -->
  <div class="lottie-wrap" id="lottieHello" hidden></div>

  <h1>تسجيل الدخول</h1>
  <p class="lead">اكتب اسم المستخدم وكلمة المرور.</p>

  <!-- رسائل الأخطاء من Laravel -->
  @if($errors->any())
    <div class="error" role="alert">
      {{ $errors->first() }}
    </div>
  @endif
  <div id="bladeError" data-has-error="{{ $errors->any() ? '1' : '0' }}" style="display:none"></div>

  <form id="loginForm" method="POST" action="{{ route('login') }}">
    @csrf

    <div class="field">
      <label for="loginName">اسم المستخدم</label>
      <div class="input-wrap">
        <input id="loginName" name="name" type="text" value="{{ old('name') }}" autocomplete="username" required>
        <div class="ghost-layer" id="nameGhost" aria-hidden="true"></div>
      </div>
    </div>

    <div class="field">
      <label for="loginPassword">كلمة المرور</label>
      <div class="input-wrap">
        <input id="loginPassword" name="password" type="password" autocomplete="current-password" required class="mask-password">
        <div class="pass-ghost-layer" id="passGhost" aria-hidden="true"></div>

        <button type="button" class="toggle-visibility" id="togglePass" aria-label="إظهار/إخفاء كلمة المرور">
          <svg class="icon-eye" viewBox="0 0 24 24" fill="none">
            <path d="M12 5c5.5 0 9.5 4.5 10.8 6-1.3 1.5-5.3 6-10.8 6S2.5 12.5 1.2 11C2.5 9.5 6.5 5 12 5Z" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="12" cy="11" r="3.2" fill="currentColor"/>
          </svg>
          <svg class="icon-lock" viewBox="0 0 24 24" fill="none" style="display:none">
            <rect x="4" y="10" width="16" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/>
            <path d="M8 10V8a4 4 0 1 1 8 0v2" stroke="currentColor" stroke-width="1.6"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="field" style="margin-bottom: 18px;">
      <label style="display: flex; align-items: center; font-weight: 400; cursor: pointer; user-select: none;">
        <input type="checkbox" name="remember" id="rememberMe" style="width: 18px; height: 18px; margin-left: 8px; cursor: pointer; accent-color: var(--primary);">
        <span style="font-size: 14px;">تذكرني</span>
      </label>
    </div>

    <div class="actions">
      <button id="loginBtn" type="submit" class="btn">
        <span class="btn-label">تسجيل الدخول</span>
        <span class="loader"><span class="spinner"></span></span>
      </button>
    </div>
  </form>
</div>

<script>
  /* ===================== إعداد عام ===================== */
  const RIVE_SRC = "/rive/teddy_login.riv";
  const STATE_MACHINE = "Login Machine";

  let riveApp = null;
  let inputs = {};

  const nameInput   = document.getElementById('loginName');
  const passInput   = document.getElementById('loginPassword');
  const form        = document.getElementById('loginForm');
  const canvas      = document.getElementById('riveCanvas');
  const cardEl      = document.getElementById('card');
  const bladeFlagEl = document.getElementById('bladeError');
  const loginBtn    = document.getElementById('loginBtn');

  const nameGhost = document.getElementById('nameGhost');
  const passGhost = document.getElementById('passGhost');

  const togglePassBtn = document.getElementById('togglePass');
  const iconEye  = togglePassBtn.querySelector('.icon-eye');
  const iconLock = togglePassBtn.querySelector('.icon-lock');

  const _c = document.createElement('canvas');
  const _ctx = _c.getContext('2d');

  function _applyInputFont(el){
    const cs = getComputedStyle(el);
    _ctx.font = `${cs.fontStyle || 'normal'} ${cs.fontWeight || '400'} ${cs.fontSize} ${cs.fontFamily}`;
    return cs;
  }
  function _transformByTextTransform(text, cs){
    const tt = (cs.textTransform || '').toLowerCase();
    if (tt === 'uppercase') return text.toUpperCase();
    if (tt === 'lowercase') return text.toLowerCase();
    if (tt === 'capitalize') return text.replace(/\b\p{L}/gu, m => m.toUpperCase());
    return text;
  }
  function caretPercent(el){
    const cs = _applyInputFont(el);
    const pl = parseFloat(cs.paddingLeft)  || 0;
    const pr = parseFloat(cs.paddingRight) || 0;
    const ls = parseFloat(cs.letterSpacing) || 0;
    const dir = (el.dir || cs.direction || document.dir || 'ltr').toLowerCase();

    const contentWidth = el.clientWidth - pl - pr;
    let pos = el.selectionEnd;
    if (pos == null) pos = el.value.length;

    const leftText = _transformByTextTransform(el.value.slice(0, pos), cs);
    const baseW = _ctx.measureText(leftText).width;
    const measured = baseW + (ls > 0 ? ls * Math.max(0, leftText.length - 1) : 0);

    let ratio = contentWidth > 0 ? Math.max(0, Math.min(1, measured / contentWidth)) : 0;
    if (dir === 'rtl') ratio = 1 - ratio;
    return Math.round(ratio * 100);
  }

  function getInput(name){
    return inputs[name] && inputs[name][0] ? inputs[name][0] : null;
  }

  function updateLookFromCaret(){
    const nl = getInput('numLook');
    if (!nl) return;
    nl.value = caretPercent(nameInput);
  }

  /* ===================== تأثيرات الحروف (anime.js) ===================== */
  let prevNameLen = nameInput.value.length;
  let prevPassLen = passInput.value.length;

  function spawnNameGhostChar(char){
    if (!char) return;
    const pct = caretPercent(nameInput);
    const x = nameGhost.clientWidth * (pct / 100);
    const y = nameGhost.clientHeight * 0.5;

    const span = document.createElement('span');
    span.className = 'ghost-char';
    span.textContent = char;
    span.style.left = x + 'px';
    span.style.top  = y + 'px';
    nameGhost.appendChild(span);

    anime({
      targets: span,
      translateY: [{value: 8, duration: 0}, {value: -12, duration: 420, easing: 'easeOutQuad'}],
      opacity: [{value: 0, duration: 0}, {value: 1, duration: 140, easing:'linear'}, {value: 0, duration: 220, delay: 200}],
      rotateZ: [{value: (Math.random()*8 - 4), duration: 420, easing:'easeOutQuad'}],
      complete: () => span.remove()
    });
  }

  const PASS_ICONS = [
    '<svg viewBox="0 0 24 24"><path d="M12 2l2.6 5.3L20 8l-4 3.9L17 18l-5-2.6L7 18l1-6.1L4 8l5.4-.7L12 2z"/></svg>',
    '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/></svg>',
    '<svg viewBox="0 0 24 24"><rect x="7" y="7" width="10" height="10" rx="3"/></svg>'
  ];

  function renderPassGhost(direction){
    passGhost.classList.toggle('rtl', direction === 'rtl');
    passGhost.classList.toggle('ltr', direction !== 'rtl');

    const len = passInput.value.length;
    const current = passGhost.childElementCount;

    if (len > current){
      for (let i = current; i < len; i++){
        const dot = document.createElement('span');
        dot.className = 'pass-dot';
        dot.innerHTML = PASS_ICONS[i % PASS_ICONS.length];
        passGhost.appendChild(dot);

        anime({
          targets: dot,
          scale: [{value:.4, duration:0}, {value:1, duration:320, easing:'easeOutBack'}],
          opacity: [{value:0, duration:0}, {value:.95, duration:220, easing:'linear'}],
        });
      }
    }
    else if (len < current){
      const toRemove = Array.from(passGhost.children).slice(len);
      toRemove.forEach((el, idx) => {
        anime({
          targets: el,
          scale: [{value:1, duration:0}, {value:.3, duration:180, easing:'easeInBack'}],
          opacity: [{value:.95, duration:0}, {value:0, duration:160}],
          delay: idx * 10,
          complete: () => el.remove()
        });
      });
    }
  }

  function playErrorAnimation({ highlightFields = true } = {}){
    try {
      const tf = getInput('trigFail');
      if (tf && typeof tf.fire === 'function') tf.fire();
    } catch(_) {}

    cardEl.classList.remove('error-anim');
    cardEl.offsetHeight;
    cardEl.classList.add('error-anim');

    if (highlightFields){
[nameInput, passInput].forEach(inp => inp.closest('.field')?.classList.add('field-error'));
setTimeout(() => {
  [nameInput, passInput].forEach(inp => inp.closest('.field')?.classList.remove('field-error'));
}, 800);}


    if (navigator.vibrate) navigator.vibrate(80);

    loginBtn.classList.add('error-soft');
    setTimeout(()=> loginBtn.classList.remove('error-soft'), 420);
  }

  function afterRiveReady(fn, tries = 0){
    const ready = getInput('trigFail') || getInput('isChecking') || getInput('numLook') || getInput('isHandsUp') || getInput('trigSuccess');
    if (ready) { fn(); return; }
    if (tries > 40) { fn(); return; }
    setTimeout(() => afterRiveReady(fn, tries + 1), 50);
  }

  function wireEvents(){
    nameInput.addEventListener('focus', () => {
      const ic = getInput('isChecking'); if (ic) ic.value = true;
      updateLookFromCaret();
    });
    nameInput.addEventListener('blur', () => {
      const ic = getInput('isChecking'); if (ic) ic.value = false;
    });
    ['input','keyup','click','select'].forEach(ev => {
      nameInput.addEventListener(ev, () => {
        updateLookFromCaret();
        const curLen = nameInput.value.length;
        if (curLen === prevNameLen + 1) {
          const ch = nameInput.value.slice(-1);
          spawnNameGhostChar(ch);
        }
        prevNameLen = curLen;
      });
    });
    window.addEventListener('resize', updateLookFromCaret);

    const passDir = (passInput.dir || getComputedStyle(passInput).direction || document.dir || 'ltr').toLowerCase();
    renderPassGhost(passDir);
    passInput.addEventListener('input', () => {
      const curLen = passInput.value.length;
      renderPassGhost(passDir);
      if (curLen > prevPassLen && curLen - prevPassLen === 1) {
        const last = passGhost.lastElementChild;
        if (last){
          anime({ targets:last, scale: [{value:1.2, duration:120, easing:'easeOutQuad'}, {value:1, duration:160, easing:'easeOutBack'}] });
        }
      }
      prevPassLen = curLen;
    });

    togglePassBtn.addEventListener('click', () => {
      const show = passInput.type === 'password';
      passInput.type = show ? 'text' : 'password';
      passInput.classList.toggle('mask-password', !show);
      passGhost.style.display = show ? 'none' : 'flex';

      if (show){
        iconEye.style.display = 'none';
        iconLock.style.display = 'block';
        anime({ targets: iconLock, rotateZ:[-20,0], scale:[.8,1], opacity:[0,1], duration:260, easing:'easeOutBack' });
      } else {
        iconLock.style.display = 'none';
        iconEye.style.display = 'block';
        anime({ targets: iconEye, rotateZ:[20,0], scale:[.8,1], opacity:[0,1], duration:260, easing:'easeOutBack' });
      }

      anime({ targets: passInput, backgroundColor: [ '#eef2ff', '#f9fbff' ], duration: 480, easing:'easeOutQuad' });
    });

    [nameInput, passInput].forEach(inp => {
      inp.addEventListener('invalid', () => playErrorAnimation());
    });

    loginBtn.addEventListener('click', (e) => {
      const rect = loginBtn.getBoundingClientRect();
      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      const size = Math.max(rect.width, rect.height);
      ripple.style.width = ripple.style.height = size + 'px';
      const x = (e.clientX - rect.left) - size/2;
      const y = (e.clientY - rect.top)  - size/2;
      ripple.style.left = x + 'px';
      ripple.style.top  = y + 'px';
      loginBtn.appendChild(ripple);
      setTimeout(()=>ripple.remove(), 650);
    }, {passive:true});

    form.addEventListener('submit', () => {
      const ic = getInput('isChecking'); if (ic) ic.value = false;
      const ih = getInput('isHandsUp'); if (ih) ih.value = false;

      loginBtn.classList.add('loading');
      setTimeout(() => {
        loginBtn.classList.remove('loading');
      }, 1200);
    });
  }

  (function initRive(){
    try{
      riveApp = new rive.Rive({
        src: RIVE_SRC,
        canvas,
        autoplay: true,
        stateMachines: STATE_MACHINE,
        onLoad: () => {
          const smInputs = riveApp.stateMachineInputs(STATE_MACHINE) || [];
          inputs.isChecking  = smInputs.filter(i => i.name === 'isChecking');
          inputs.numLook     = smInputs.filter(i => i.name === 'numLook');
          inputs.isHandsUp   = smInputs.filter(i => i.name === 'isHandsUp');
          inputs.trigSuccess = smInputs.filter(i => i.name === 'trigSuccess');
          inputs.trigFail    = smInputs.filter(i => i.name === 'trigFail');

          wireEvents();
          maybePlayServerErrorAnimation();
        },
      });
    }catch(e){
      console.error('Rive init error', e);
      canvas.style.display = 'none';
      wireEvents();
      maybePlayServerErrorAnimation(true);
    }
  })();

  function hasServerErrorFlag(){ return bladeFlagEl?.dataset?.hasError === '1'; }
  function maybePlayServerErrorAnimation(forceNow){
    const run = () => playErrorAnimation();
    if (forceNow) { run(); return; }
    if (hasServerErrorFlag()){
      afterRiveReady(() => { requestAnimationFrame(() => requestAnimationFrame(run)); });
    }
  }

  (function initParticles(){
    if (!window.particlesJS) return;
    particlesJS('particles-js', {
      particles: {
        number: { value: 60, density: { enable: true, value_area: 900 } },
        color: { value: "#93c5fd" },
        shape: { type: "circle" },
        opacity: { value: 0.35, random: true },
        size: { value: 2.4, random: true },
        line_linked: { enable: true, distance: 130, color: "#60a5fa", opacity: 0.2, width: 1 },
        move: { enable: true, speed: 1.2, direction: "none", random: false, straight: false, out_mode: "out", bounce: false, attract: { enable: false } }
      },
      interactivity: {
        detect_on: "canvas",
        events: { onhover: { enable: true, mode: "repulse" }, onclick: { enable: false }, resize: true },
        modes: { repulse: { distance: 80, duration: 0.4 } }
      },
      retina_detect: true
    });
  })();

  (function initLottie(){
    try{
      const el = document.getElementById('lottieHello');
      const anim = lottie.loadAnimation({
        container: el,
        renderer: 'svg',
        loop: false,
        autoplay: true,
        path: '/lottie/hello.json'
      });
      el.hidden = false;
      anim.setSpeed(1.1);
    }catch(e){
      document.getElementById('lottieHello').hidden = true;
    }
  })();

  document.addEventListener('DOMContentLoaded', () => {
    if (hasServerErrorFlag()) maybePlayServerErrorAnimation();
  });

  passInput.addEventListener('focus', () => {
    const ih = getInput('isHandsUp'); if (ih) ih.value = true;
  });
  passInput.addEventListener('blur', () => {
    const ih = getInput('isHandsUp'); if (ih) ih.value = false;
  });
</script>

<!-- أدوات مساعدة + success/fail -->
<script>
function getRiveInput(name){
  try{
    return (window.rive && window.riveApp && typeof riveApp.stateMachineInputs==='function')
      ? (riveApp.stateMachineInputs("Login Machine")||[]).find(i=>i.name===name) || null
      : null;
  }catch{ return null; }
}

window.triggerLoginFail = function(){
  try{ const tf=getRiveInput('trigFail'); if(tf && typeof tf.fire==='function') tf.fire(); }catch{}
  try{
    const cardEl = document.getElementById('card');
    cardEl?.classList.remove('error-anim'); cardEl && cardEl.offsetHeight; cardEl?.classList.add('error-anim');
['loginName','loginPassword'].forEach(id=>{
  const el=document.getElementById(id);
  el?.closest('.field')?.classList.add('field-error');
  setTimeout(()=> el?.closest('.field')?.classList.remove('field-error'), 800);
});

    const btn=document.getElementById('loginBtn');
    btn?.classList.add('error-soft'); setTimeout(()=>btn?.classList.remove('error-soft'),420);
    if (navigator.vibrate) navigator.vibrate(80);
  }catch{}

  // إظهار الدب وإخفاء لوتي
  const canvas = document.getElementById('riveCanvas');
  if (canvas) canvas.classList.remove('is-hidden');
  const el = document.getElementById('lottieHello');
  if (el) {
    el.classList.remove('show'); el.hidden = true;
    try { window.lottie && window.lottie.destroy(); } catch(_){}
    el.innerHTML = '';
  }
};

window.triggerLoginSuccess = function(){
  try{ const ts=getRiveInput('trigSuccess'); if(ts && typeof ts.fire==='function') ts.fire(); }catch{}
  try{
    const btn=document.getElementById('loginBtn');
    btn?.classList.remove('loading'); btn?.classList.add('success');
  }catch{}

  // خبّي الدب وأظهر لوتي مكانه
  (function(){
    const wrap=document.getElementById('lottieHello');
    if(!wrap) return;
    const canvas=document.getElementById('riveCanvas');
    if (canvas) canvas.classList.add('is-hidden');
    wrap.hidden=false; wrap.classList.add('show'); wrap.innerHTML='';
    if (window.lottie){
      const anim=lottie.loadAnimation({ container:wrap, renderer:'svg', loop:false, autoplay:true, path:'/lottie/hello.json' });
      try{ anim.setSpeed(1.1); }catch{}
    }
  })();
};
</script>

<script>
/* إخفِ لوتي افتراضيًا */
(function(){
  const wrap = document.getElementById('lottieHello');
  if (!wrap) return;
  wrap.hidden = true;
  wrap.classList.remove('show');
})();
</script>

<!-- الدالة اللي بتميّز ريدايركت راجع لنفس صفحة اللوجين -->
<script>
function isRedirectBackToLogin(urlStr){
  try{
    const u = new URL(urlStr, window.location.origin);
    const here = new URL(window.location.href);
    return u.origin === here.origin && u.pathname === here.pathname;
  }catch{ return false; }
}
</script>

<!-- ⚠️ كان هنا بلوك خارج السياق بيكسّر الجافاسكربت – سبناه كتعليق بدون حذف -->
<script>
/*
 // Redirect
 if (res.redirected){
   if (isRedirectBackToLogin(res.url)) {
     btn.classList.remove('loading');
     window.triggerLoginFail();
     showInlineError('اسم المستخدم أو كلمة المرور غير صحيحة.');
     return;
   } else {
     window.triggerLoginSuccess();
     setTimeout(()=> window.location.assign(res.url), 900);
     return;
   }
 }
*/
</script>

<!-- AJAX Login -->
<script>
(function enhanceAjaxLogin(){
  const form  = document.getElementById('loginForm');
  const btn   = document.getElementById('loginBtn');
  const nameI = document.getElementById('loginName');
  const passI = document.getElementById('loginPassword');

  if (!form || !btn) return;

  function showInlineError(msg){
    let el = document.querySelector('.error');
    if(!el){
      el = document.createElement('div');
      el.className = 'error';
      form.insertAdjacentElement('beforebegin', el);
    }
    el.textContent = msg || 'حدث خطأ غير متوقع.';
  }

  async function ajaxLogin(e){
    e.preventDefault();

    if (!nameI.value || !passI.value){
      window.triggerLoginFail();
      showInlineError('من فضلك اكتب اسم المستخدم وكلمة المرور.');
      return;
    }

    btn.classList.add('loading');
    const lottieEl = document.getElementById('lottieHello');
    if (lottieEl) { lottieEl.classList.remove('show'); lottieEl.hidden = true; try{ lottie?.destroy(); }catch(_){} lottieEl.innerHTML=''; }
    const canvasEl = document.getElementById('riveCanvas');
    if (canvasEl) canvasEl.classList.remove('is-hidden');

    try{
      const fd = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        body: fd,
        credentials: 'same-origin',
        redirect: 'follow'
      });

      // Redirect (Laravel)
      if (res.redirected){
        if (isRedirectBackToLogin(res.url)) {
          btn.classList.remove('loading');
          window.triggerLoginFail();
          showInlineError('اسم المستخدم أو كلمة المرور غير صحيحة.');
          return;
        } else {
          window.triggerLoginSuccess();
          setTimeout(()=> window.location.assign(res.url), 900);
          return;
        }
      }

      const ctype = (res.headers.get('content-type')||'').toLowerCase();
      if (ctype.includes('application/json')){
        const data = await res.json().catch(()=> ({}));
        if (res.ok && (data.redirect || data.intended)){
          window.triggerLoginSuccess();
          setTimeout(()=> window.location.assign(data.redirect || data.intended || '/'), 900);
          return;
        } else {
          btn.classList.remove('loading');
          window.triggerLoginFail();
          const msg = data?.message || (data?.errors && (Object.values(data.errors)[0]?.[0])) || 'بيانات غير صحيحة.';
          showInlineError(msg);
          return;
        }
      }

      btn.classList.remove('loading');
      window.triggerLoginFail();
      showInlineError(res.status===419 ? 'انتهت صلاحية الجلسة. حدّث الصفحة وحاول مرة أخرى.' : 'تعذر تسجيل الدخول.');
    }catch(err){
      btn.classList.remove('loading');
      window.triggerLoginFail();
      showInlineError('تعذر الاتصال بالسيرفر.');
    }
  }

  form.addEventListener('submit', ajaxLogin);
})();
</script>

<!-- Fallback Rive -->
<script>
(function(){
  if (window.rive && window.rive.Rive) return;

  const FALLBACKS = [
    'https://unpkg.com/@rive-app/canvas@2.17.4/rive.min.js',
    'https://cdn.jsdelivr.net/npm/@rive-app/canvas@2.17.4/rive.min.js',
    '/vendor/rive/rive.min.js'
  ];

  function loadScript(src){
    return new Promise((resolve,reject)=>{
      const s=document.createElement('script');
      s.src=src; s.async=true;
      s.onload=()=>resolve(src);
      s.onerror=()=>reject(new Error('failed '+src));
      document.head.appendChild(s);
    });
  }

  (async function tryFallbacks(){
    for (const url of FALLBACKS){
      try{
        await loadScript(url);
        if (window.rive && window.rive.Rive){
          console.log('[Rive] loaded from fallback:', url);
          tryReInitRive();
          return;
        }
      }catch(e){
        console.warn('[Rive] fallback failed:', url);
      }
    }
    const canvas = document.getElementById('riveCanvas');
    if (canvas) canvas.classList.add('rive-hidden');
  })();

  function tryReInitRive(){
    if (window.__riveInitDone) return;
    const canvas = document.getElementById('riveCanvas');
    if (!canvas) return;

    try{
      const app = new rive.Rive({
        src: RIVE_SRC,
        canvas,
        autoplay: true,
        stateMachines: STATE_MACHINE,
        onLoad: ()=>{
          console.log('[Rive] re-init success after fallback load');
          window.__riveInitDone = true;
          try{
            const sm = app.stateMachineInputs(STATE_MACHINE) || [];
            if (!sm.length) { try{ app.play(); }catch{} }
          }catch{}
          canvas.classList.remove('rive-hidden');
        },
        onError: (err)=>{
          console.warn('[Rive] re-init onError:', err);
          canvas.classList.add('rive-hidden');
        }
      });
    }catch(e){
      console.warn('[Rive] re-init exception:', e);
      const canvas = document.getElementById('riveCanvas');
      if (canvas) canvas.classList.add('rive-hidden');
    }
  }
})();
</script>

<!-- فحص .riv -->
<script>
(function(){
  const RIVE_SRC = "/rive/teddy_login.riv";
  const canvas = document.getElementById('riveCanvas');
  if (!canvas) return;

  fetch(RIVE_SRC, { method:'HEAD', cache:'no-store' }).then(res=>{
    if (!res.ok){
      console.warn('[Rive] .riv not found:', RIVE_SRC, res.status);
      canvas.classList.add('rive-hidden');
    }
  }).catch(()=>{});
})();
</script>

<!-- نقل لوتي فوق الكانفاس + إطفاء التشغيل التلقائي -->
<script>
(function moveLottieIntoRiveWrap(){
  const wrap = document.querySelector('.rive-wrap');
  const lottie = document.getElementById('lottieHello');
  if (wrap && lottie && lottie.parentElement !== wrap) {
    wrap.appendChild(lottie);
    lottie.hidden = true;
    lottie.classList.remove('show');
    lottie.innerHTML = '';
  }
})();

(function forceHideAutoLottie(){
  const el = document.getElementById('lottieHello');
  if (!el) return;
  try {
    if (window.lottie && el.firstChild) {
      window.lottie.destroy();
    }
  } catch(_) {}
  el.hidden = true;
  el.classList.remove('show');
  el.innerHTML = '';
})();
</script>

<script>
  // تأكيد إن Enter مايبعتش فورم تقليدي ويمرّ عبر ajaxLogin
  (function preventNativeEnterSubmit(){
    const form = document.getElementById('loginForm');
    if (!form) return;
    form.addEventListener('keydown', (e)=>{
      if (e.key === 'Enter') {
        e.preventDefault();
        try {
          const evt = new Event('submit', {cancelable:true});
          form.dispatchEvent(evt);
        } catch(_) {}
      }
    });
  })();
</script>

</body>
</html>
