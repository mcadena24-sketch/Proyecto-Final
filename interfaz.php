<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIMICOIN — Blockchain</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Helvetica, Arial, sans-serif; background: #f0f4f6; color: #1a1f4e; }

nav {
  background: #1a1f4e;
  padding: 14px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.logo { font-size: 16px; font-weight: 700; color: #D4AF37; letter-spacing: .1em; }
.logo span { color: #fff; font-weight: 300; }
.conn { font-size: 11px; color: rgba(255,255,255,.5); display: flex; align-items: center; gap: 6px; }
.dot { width: 7px; height: 7px; border-radius: 50%; background: #aaa; }
.dot.on { background: #D4AF37; }

.page { max-width: 860px; margin: 0 auto; padding: 24px 16px; display: flex; flex-direction: column; gap: 16px; }

.card {
  background: #fff;
  border: 1px solid rgba(26,31,78,.12);
  border-radius: 10px;
  overflow: hidden;
}
.card-title {
  background: #1a1f4e;
  color: #D4AF37;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .1em;
  text-transform: uppercase;
  padding: 9px 16px;
}
.card-body { padding: 16px; }

.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.grid3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }

.stat {
  background: #f0f4f6;
  border-radius: 8px;
  padding: 12px 14px;
  text-align: center;
}
.stat-label { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 4px; }
.stat-value { font-size: 22px; font-weight: 700; color: #1a1f4e; }
.stat-value.gold { color: #B8860B; }
.stat-value.ok   { color: #2e7d32; font-size: 13px; }
.stat-value.bad  { color: #c62828; font-size: 13px; }

.btn {
  padding: 10px 16px;
  border-radius: 7px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  transition: opacity .15s, transform .1s;
  font-family: Helvetica, Arial, sans-serif;
}
.btn:active { transform: scale(.97); }
.btn:disabled { opacity: .4; cursor: not-allowed; }
.btn-gold { background: #D4AF37; color: #1a1f4e; width: 100%; }
.btn-gold:hover:not(:disabled) { background: #C5A059; }
.btn-outline { background: transparent; border: 1px solid rgba(26,31,78,.2); color: #1a1f4e; }
.btn-outline:hover:not(:disabled) { background: #f0f4f6; }
.btn-sm { padding: 6px 12px; font-size: 11px; }

lbl { display: block; font-size: 10px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: #999; margin-bottom: 4px; margin-top: 12px; }
lbl:first-child { margin-top: 0; }
input {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid rgba(26,31,78,.15);
  border-radius: 6px;
  font-family: monospace;
  font-size: 11px;
  color: #1a1f4e;
  background: #f0f4f6;
  outline: none;
}
input:focus { border-color: #C5A059; }
input[readonly] { color: #aaa; }

.log {
  font-family: monospace;
  font-size: 10px;
  background: #1a1f4e;
  color: #C5A059;
  border-radius: 7px;
  padding: 10px 12px;
  height: 90px;
  overflow-y: auto;
  margin-top: 12px;
}
.log .ok   { color: #81c784; }
.log .err  { color: #e57373; }
.log .warn { color: #D4AF37; }
.log .dim  { color: rgba(197,160,89,.5); }

.spin-row { display: none; align-items: center; gap: 6px; font-size: 11px; color: #B8860B; margin-top: 8px; }
.spin-row.on { display: flex; }
.spin { display: inline-block; animation: sp 1s linear infinite; }
@keyframes sp { to { transform: rotate(360deg); } }

.block-list { display: flex; flex-direction: column; gap: 8px; max-height: 60vh; overflow-y: auto; }
.block-list::-webkit-scrollbar { width: 3px; }
.block-list::-webkit-scrollbar-thumb { background: #C5A059; border-radius: 2px; }

.blk { border: 1px solid rgba(26,31,78,.12); border-radius: 8px; overflow: hidden; background: #fff; }
.blk.genesis { border-color: #D4AF37; }

.blk-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  cursor: pointer;
}
.blk-row:hover { background: #f0f4f6; }

.num {
  font-size: 10px; font-weight: 700; font-family: monospace;
  background: #1a1f4e; color: #D4AF37;
  border-radius: 5px; padding: 3px 8px; flex-shrink: 0;
}
.blk-info { flex: 1; min-width: 0; }
.blk-hash { font-family: monospace; font-size: 10px; color: #bbb; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.blk-ts   { font-size: 10px; color: #ccc; margin-top: 1px; }
.tx-pill {
  font-size: 10px; font-family: monospace;
  background: #fff8e1; color: #B8860B;
  border: 1px solid #ffe082; border-radius: 20px;
  padding: 2px 8px; flex-shrink: 0;
}
.arrow { color: #ccc; font-size: 11px; transition: transform .2s; }
.expanded .arrow { transform: rotate(180deg); }

.blk-detail { display: none; border-top: 1px solid rgba(26,31,78,.08); padding: 12px 14px; background: #f9fafb; }
.blk-detail.open { display: block; }

.dg { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 10px; }
.di { background: #fff; border: 1px solid rgba(26,31,78,.08); border-radius: 6px; padding: 8px 10px; }
.di.full { grid-column: 1/-1; }
.dk { font-size: 9px; text-transform: uppercase; letter-spacing: .07em; color: #bbb; margin-bottom: 3px; }
.dv { font-family: monospace; font-size: 10px; color: #1a1f4e; word-break: break-all; }

.tx-row {
  background: #fff; border: 1px solid rgba(26,31,78,.08);
  border-radius: 6px; padding: 7px 10px;
  display: flex; align-items: center; gap: 8px; margin-bottom: 5px;
  font-family: monospace; font-size: 10px;
}
.from { color: #B8860B; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.to   { color: #888;    flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.amt  { color: #B8860B; font-weight: 700; flex-shrink: 0; }
.arr  { color: #ddd; }

.pill {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11px; font-weight: 700;
  padding: 4px 12px; border-radius: 20px; margin-top: 10px;
}
.pill.ok  { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
.pill.bad { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }

.wbox {
  background: #f0f4f6; border: 1px solid rgba(26,31,78,.12);
  border-radius: 8px; padding: 12px; margin-bottom: 10px;
  font-family: monospace; font-size: 10px; word-break: break-all;
}
.waddr { color: #B8860B; font-weight: 700; font-size: 11px; margin-bottom: 4px; }
.wpub  { color: #aaa; }

.toast-wrap { position: fixed; bottom: 18px; right: 18px; display: flex; flex-direction: column; gap: 6px; z-index: 99; }
.toast {
  background: #fff; border-left: 3px solid #D4AF37;
  border: 1px solid rgba(26,31,78,.1); border-left: 3px solid #D4AF37;
  border-radius: 8px; padding: 10px 14px; font-size: 12px; max-width: 280px;
  animation: tin .18s ease; color: #1a1f4e;
}
.toast.ok  { border-left-color: #2e7d32; }
.toast.err { border-left-color: #c62828; }
@keyframes tin { from{opacity:0;transform:translateX(12px)} to{opacity:1;transform:none} }

.empty { text-align: center; padding: 24px; color: #ccc; font-size: 12px; }
.sep { height: 1px; background: rgba(26,31,78,.08); margin: 12px 0; }
.flex { display: flex; gap: 8px; align-items: center; }
.flex-end { display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; }

.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.6);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 999;
}
.modal.on { display: flex; }

.modal-box {
  width: 92%;
  max-width: 1100px;
  max-height: 90vh;
  overflow-y: auto;
  background: #fff;
  border-radius: 14px;
  border: 1px solid rgba(26,31,78,.12);
  overflow-x: hidden;
}

.modal-head {
  background: #1a1f4e;
  color: #D4AF37;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
}

.close-btn {
  background: transparent;
  border: none;
  color: #D4AF37;
  font-size: 20px;
  cursor: pointer;
}

.chain-btn-wrap {
  display:flex;
  justify-content:center;
  padding:20px;
}

/* ── ONBOARDING SCREEN ── */
#onboarding {
  position: fixed;
  inset: 0;
  background: #0e1235;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
  background-image:
    radial-gradient(ellipse 60% 50% at 50% 0%, rgba(212,175,55,.18) 0%, transparent 70%),
    radial-gradient(ellipse 40% 30% at 80% 80%, rgba(212,175,55,.07) 0%, transparent 60%);
}
#onboarding.hidden { display: none; }

.ob-box {
  width: 100%;
  max-width: 420px;
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 24px 80px rgba(0,0,0,.45);
}

.ob-head {
  background: #1a1f4e;
  padding: 28px 28px 20px;
  text-align: center;
  position: relative;
}
.ob-logo {
  font-size: 22px;
  font-weight: 900;
  color: #D4AF37;
  letter-spacing: .12em;
}
.ob-logo span { color: rgba(255,255,255,.7); font-weight: 300; }
.ob-subtitle {
  margin-top: 6px;
  font-size: 11px;
  color: rgba(255,255,255,.4);
  letter-spacing: .1em;
  text-transform: uppercase;
}

.ob-steps {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-top: 18px;
}
.ob-step-dot {
  width: 28px; height: 3px;
  background: rgba(255,255,255,.15);
  border-radius: 2px;
  transition: background .3s;
}
.ob-step-dot.active { background: #D4AF37; }

.ob-body { padding: 24px 28px; }

.ob-panel { display: none; }
.ob-panel.active { display: block; }

.ob-title {
  font-size: 15px;
  font-weight: 700;
  color: #1a1f4e;
  margin-bottom: 4px;
}
.ob-desc {
  font-size: 11px;
  color: #aaa;
  margin-bottom: 20px;
  line-height: 1.5;
}

.ob-lbl {
  display: block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: #999;
  margin-bottom: 5px;
}
.ob-input {
  width: 100%;
  padding: 10px 12px;
  border: 1.5px solid rgba(26,31,78,.15);
  border-radius: 8px;
  font-size: 13px;
  color: #1a1f4e;
  background: #f7f8fc;
  outline: none;
  font-family: Helvetica, Arial, sans-serif;
  transition: border-color .2s;
}
.ob-input:focus { border-color: #C5A059; background: #fff; }
.ob-input.mono { font-family: monospace; font-size: 11px; }

.ob-err {
  font-size: 11px;
  color: #c62828;
  margin-top: 6px;
  min-height: 16px;
}

.ob-wallet-card {
  background: #f0f4f6;
  border: 1px solid rgba(26,31,78,.12);
  border-radius: 10px;
  padding: 14px;
  margin: 14px 0;
  display: none;
}
.ob-wallet-card.visible { display: block; }
.ob-wk { font-size: 9px; text-transform: uppercase; letter-spacing: .07em; color: #bbb; margin-bottom: 4px; }
.ob-wv { font-family: monospace; font-size: 10px; color: #B8860B; font-weight: 700; word-break: break-all; }
.ob-wv.dim { color: #aaa; font-weight: 400; }
.ob-warn { font-size: 10px; color: #c62828; margin-top: 10px; display: flex; align-items: flex-start; gap: 5px; }

.ob-btn {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  font-family: Helvetica, Arial, sans-serif;
  transition: opacity .15s, transform .1s;
  margin-top: 8px;
}
.ob-btn:active { transform: scale(.97); }
.ob-btn:disabled { opacity: .4; cursor: not-allowed; }
.ob-btn-gold { background: #D4AF37; color: #1a1f4e; }
.ob-btn-gold:hover:not(:disabled) { background: #C5A059; }
.ob-btn-outline {
  background: transparent;
  border: 1.5px solid rgba(26,31,78,.2);
  color: #1a1f4e;
  margin-top: 6px;
}
.ob-btn-outline:hover:not(:disabled) { background: #f0f4f6; }

.ob-spinner { display: none; align-items: center; gap: 6px; font-size: 11px; color: #B8860B; margin-top: 8px; justify-content: center; }
.ob-spinner.on { display: flex; }

/* Nav user badge */
.nav-user {
  display: flex;
  align-items: center;
  gap: 8px;
}
.nav-avatar {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: #D4AF37;
  color: #1a1f4e;
  font-size: 12px;
  font-weight: 900;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.nav-uname {
  font-size: 11px;
  color: rgba(255,255,255,.75);
  font-weight: 600;
  max-width: 100px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

</style>
</head>
<body>

<nav>
  <div class="logo">SIMI<span>COIN</span></div>
  <div style="display:flex;align-items:center;gap:14px;">
    <div class="nav-user" id="nav-user" style="display:none;">
      <div class="nav-avatar" id="nav-avatar">?</div>
      <div class="nav-uname" id="nav-uname">—</div>
    </div>
    <div class="conn"><div class="dot" id="dot"></div><span id="stxt">Conectando…</span></div>
  </div>
</nav>

<!-- ── ONBOARDING OVERLAY ── -->
<div id="onboarding">
  <div class="ob-box">

    <div class="ob-head">
      <div class="ob-logo">SIMI<span>COIN</span></div>
      <div class="ob-subtitle">Blockchain · secp256k1</div>
      <div class="ob-steps">
        <div class="ob-step-dot active" id="step-dot-1"></div>
        <div class="ob-step-dot" id="step-dot-2"></div>
      </div>
    </div>

    <div class="ob-body">

      <!-- PASO 1: Usuario -->
      <div class="ob-panel active" id="ob-panel-1">
        <div class="ob-title">¡Bienvenido!</div>
        <div class="ob-desc">Elige un nombre de usuario para identificarte en la red.</div>
        <label class="ob-lbl" for="ob-username">Nombre de usuario</label>
        <input class="ob-input" id="ob-username" type="text" placeholder="ej. satoshi_mx" maxlength="24" autocomplete="off">
        <div class="ob-err" id="ob-err-1"></div>
        <button class="ob-btn ob-btn-gold" onclick="obNext1()">Continuar →</button>
      </div>

      <!-- PASO 2: Wallet -->
      <div class="ob-panel" id="ob-panel-2">
        <div class="ob-title">Tu wallet</div>
        <div class="ob-desc">Se ha generado tu par de llaves criptográficas. Guarda tu address para recibir SIMI.</div>

        <div class="ob-spinner" id="ob-spin"><span class="spin">◌</span> Generando llaves…</div>

        <div class="ob-wallet-card" id="ob-wallet-card">
          <div class="ob-wk">Address</div>
          <div class="ob-wv" id="ob-waddr">—</div>
          <div style="margin-top:10px;">
            <div class="ob-wk">Llave pública</div>
            <div class="ob-wv dim" id="ob-wpub">—</div>
          </div>
          <div class="ob-warn">⚠ Nunca compartas tu llave privada con nadie.</div>
        </div>

        <div class="ob-err" id="ob-err-2"></div>

        <button class="ob-btn ob-btn-outline" onclick="obBack()">← Volver</button>
        <button class="ob-btn ob-btn-gold" id="ob-enter-btn" onclick="obEnter()" disabled style="margin-top:10px;">Entrar al dashboard →</button>
      </div>

    </div>
  </div>
</div>

<div class="page">

  <!-- Stats -->
  <div class="grid3">
    <div class="stat">
      <div class="stat-label">Bloques</div>
      <div class="stat-value gold" id="s-blocks">—</div>
    </div>
    <div class="stat">
      <div class="stat-label">Tx pendientes</div>
      <div class="stat-value" id="s-pending">0</div>
    </div>
    <div class="stat">
      <div class="stat-label">Cadena</div>
      <div class="stat-value ok" id="s-valid">—</div>
    </div>
  </div>

  <!-- Minar + log -->
  <div class="card">
    <div class="card-title">Minería — Proof of Work (dificultad 4)</div>
    <div class="card-body">
      <button class="btn btn-gold" id="btn-mine" onclick="mine()">Minar bloque</button>
      <div class="spin-row" id="spin"><span class="spin">◌</span> Calculando nonce…</div>
      <div class="log" id="log"><span class="dim">// log del sistema</span></div>
    </div>
  </div>

  <!-- Wallet + Transacción en grid -->
  <div class="grid2">

    <div class="card">
      <div class="card-title">Wallet secp256k1</div>
      <div class="card-body">
        <div id="wallet-area"><div class="empty">Sin wallet activa</div></div>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Enviar transacción</div>
      <div class="card-body">
        <lbl>De (address)</lbl>
        <input id="tx-from" placeholder="Genera una wallet primero" readonly>
        <lbl>Para (address)</lbl>
        <input id="tx-to" placeholder="Address destinatario">
        <lbl>Monto (SIMI)</lbl>
        <input id="tx-amt" type="number" placeholder="0" min="0.01" step="0.01">
        <input type="hidden" id="tx-priv">
        <div style="margin-top:12px;">
          <button class="btn btn-gold" onclick="sendTx()">Firmar y enviar</button>
        </div>
      </div>
    </div>

  </div>

  <!-- Validar + Saldo en grid -->
  <div class="grid2">

    <div class="card">
      <div class="card-title">Validar cadena</div>
      <div class="card-body">
        <button class="btn btn-outline" style="width:100%;" onclick="validateChain()">Verificar integridad</button>
        <div id="valid-out"></div>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Consultar saldo</div>
      <div class="card-body">
        <lbl>Address</lbl>
        <input id="bal-addr" placeholder="Address en hex">
        <div style="margin-top:10px;">
          <button class="btn btn-outline" style="width:100%;" onclick="checkBal()">Consultar</button>
        </div>
        <div id="bal-out"></div>
      </div>
    </div>

  </div>

  <!-- Chain explorer -->
  <div class="card">
    <div class="card-title" style="display:flex;justify-content:space-between;align-items:center;">
      Cadena de bloques
      <div class="flex">
        <span id="chain-meta" style="font-size:10px;color:#C5A059;font-weight:400;">— bloques</span>
        <button class="btn btn-sm btn-outline" onclick="loadChain()">↻</button>
      </div>
    </div>

    <div class="card-body">

      <div class="chain-btn-wrap">
        <button class="btn btn-gold" onclick="openBlocks()">
          Ver todos los bloques
        </button>
      </div>

    </div>

  </div>

</div>

<div class="modal" id="blocks-modal">

  <div class="modal-box">

    <div class="modal-head">
      Explorador de bloques
      <button class="close-btn" onclick="closeBlocks()">✕</button>
    </div>

    <div style="padding:16px;">
      <div class="block-list" id="block-list">
        <div class="empty">Cargando bloques…</div>
      </div>
    </div>

  </div>

</div>

<div class="toast-wrap" id="toasts"></div>

<script>
const API = 'https://chewable-single-pupil.ngrok-free.dev';
let wallet = null, pending = 0;

function log(msg, type='') {
  const el = document.getElementById('log');
  const t = new Date().toLocaleTimeString('es-MX',{hour12:false});
  el.innerHTML += `<div><span style="color:rgba(197,160,89,.4)">[${t}]</span> <span class="${type}">${msg}</span></div>`;
  el.scrollTop = el.scrollHeight;
}

function toast(msg, type='') {
  const el = document.createElement('div');
  el.className = 'toast '+type;
  el.textContent = msg;
  document.getElementById('toasts').appendChild(el);
  setTimeout(() => el.remove(), 3200);
}

function trunc(h, n=12) { return h ? h.slice(0,n)+'…'+h.slice(-4) : '—'; }

async function api(path, opts={}) {
  const headers = {'Content-Type':'application/json','ngrok-skip-browser-warning':'true',...(opts.headers||{})};
  const r = await fetch(API+path, {...opts, headers});
  const d = await r.json();
  if (!r.ok) throw new Error(d.error||'Error');
  return d;
}

async function ping() {
  try {
    await api('/get_chain');
    document.getElementById('dot').className = 'dot on';
    document.getElementById('stxt').textContent = 'ngrok conectado';
    loadChain(); validateChain();
  } catch {
    document.getElementById('stxt').textContent = 'Sin conexión';
    log('No se pudo conectar. Ejecuta: python blockchain.py', 'err');
  }
}

async function loadChain() {
  try {
    const d = await api('/get_chain');
    renderChain(d.chain);
    document.getElementById('s-blocks').textContent = d.longitud;
    document.getElementById('chain-meta').textContent = d.longitud + ' bloque'+(d.longitud!==1?'s':'');
    pending = 0; document.getElementById('s-pending').textContent = 0;
  } catch(e) { log(e.message,'err'); }
}

function renderChain(chain) {
  const list = document.getElementById('block-list');
  if (!chain||!chain.length) { list.innerHTML='<div class="empty">Sin bloques</div>'; return; }
  list.innerHTML='';
  [...chain].reverse().forEach(b => {
    const txs = b.transacciones||[];
    const el = document.createElement('div');
    el.className = 'blk'+(b.index===1?' genesis':'');
    el.id = 'b'+b.index;
    const txRows = txs.map(tx=>`
      <div class="tx-row">
        <span class="from">${tx.remitente==='SISTEMA'?'SISTEMA':trunc(tx.remitente)}</span>
        <span class="arr">→</span>
        <span class="to">${trunc(tx.destinatario)}</span>
        <span class="amt">${tx.monto} SIMI</span>
      </div>`).join('')||'<div style="font-size:10px;color:#ccc;padding:4px 0">Sin transacciones</div>';
    el.innerHTML = `
      <div class="blk-row" onclick="tog(${b.index})">
        <div class="num">#${b.index}</div>
        <div class="blk-info">
          <div class="blk-hash">${trunc(b.previous_hash,20)}</div>
          <div class="blk-ts">${new Date(b.timestamp).toLocaleString('es-MX')}</div>
        </div>
        <span class="tx-pill">${txs.length} tx</span>
        <span class="arrow">▾</span>
      </div>
      <div class="blk-detail" id="d${b.index}">
        <div class="dg">
          <div class="di"><div class="dk">Nonce</div><div class="dv">${b.proof}</div></div>
          <div class="di"><div class="dk">Timestamp UTC</div><div class="dv">${b.timestamp}</div></div>
          <div class="di full"><div class="dk">Hash previo</div><div class="dv">${b.previous_hash}</div></div>
          <div class="di full"><div class="dk">Merkle Root</div><div class="dv">${b.merkle_root}</div></div>
        </div>
        <div style="font-size:9px;color:#bbb;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Transacciones</div>
        ${txRows}
      </div>`;
    list.appendChild(el);
  });
}

function tog(i) {
  const d = document.getElementById('d'+i);
  const h = d.previousElementSibling;
  d.classList.toggle('open');
  h.classList.toggle('expanded');
}

async function mine() {

  if (!wallet || !wallet.address) {
    toast('Primero genera una wallet', 'err');
    return;
  }

  const btn = document.getElementById('btn-mine');
  const sp  = document.getElementById('spin');

  btn.disabled = true;
  sp.classList.add('on');

  log(
    'Minando hacia wallet: ' +
    trunc(wallet.address),
    'warn'
  );

  try {

    const d = await api(
      '/mine_block/' + wallet.address
    );

    log(
      `Bloque #${d.index} minado → +${d.recompensa} SIMI`,
      'ok'
    );

    toast(
      '+' + d.recompensa + ' SIMI',
      'ok'
    );

    await loadChain();
    await validateChain();

    document.getElementById('bal-addr').value =
      wallet.address;

    await checkBal();

    setTimeout(() => {

      const el = document.getElementById(
        'b' + d.index
      );

      if (el) {

        el.scrollIntoView({
          behavior: 'smooth',
          block: 'nearest'
        });

        tog(d.index);
      }

    }, 100);

  } catch(e) {

    log(e.message, 'err');
    toast(e.message, 'err');

  } finally {

    btn.disabled = false;
    sp.classList.remove('on');
  }
}

async function newWallet() {
  try {
    const w = await api('/new_wallet');
    wallet = w;
    document.getElementById('wallet-area').innerHTML = `
      <div class="wbox">
        <div style="font-size:9px;color:#bbb;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Address</div>
        <div class="waddr">${w.address}</div>
        <div style="font-size:9px;color:#bbb;text-transform:uppercase;letter-spacing:.07em;margin:8px 0 3px;">Llave pública</div>
        <div class="wpub">${w.public_key.slice(0,40)}…</div>
      </div>
      <div style="font-size:10px;color:#c62828;">⚠ Nunca compartas tu llave privada.</div>`;
    document.getElementById('tx-from').value = w.address;
    document.getElementById('tx-priv').value = w.private_key;
    log('Wallet generada (secp256k1)','ok');
    toast('Wallet generada','ok');
  } catch(e) { log(e.message,'err'); toast(e.message,'err'); }
}

async function sendTx() {
  const from = document.getElementById('tx-from').value.trim();
  const to   = document.getElementById('tx-to').value.trim();
  const amt  = parseFloat(document.getElementById('tx-amt').value);
  const priv = document.getElementById('tx-priv').value.trim();
  if (!from||!to||!amt||!priv) { toast('Genera una wallet y completa los campos','err'); return; }
  try {
    log('Firmando con ECDSA…');
    const s = await api('/sign_transaction',{method:'POST',body:JSON.stringify({private_key:priv,remitente:from,destinatario:to,monto:amt})});
    await api('/add_transaction',{method:'POST',body:JSON.stringify({remitente:from,destinatario:to,monto:amt,public_key:s.public_key,firma:s.firma})});
    pending++; document.getElementById('s-pending').textContent = pending;
    log('Tx registrada en el pool','ok');
    toast('Transacción enviada','ok');
    document.getElementById('tx-to').value='';
    document.getElementById('tx-amt').value='';
  } catch(e) { log(e.message,'err'); toast(e.message,'err'); }
}

async function validateChain() {
  try {
    const d = await api('/is_valid');
    const out = document.getElementById('valid-out');
    const sv  = document.getElementById('s-valid');
    if (d.valida) {
      out.innerHTML = '<div class="pill ok">✓ '+d.mensaje+'</div>';
      sv.textContent='✓ Válida'; sv.className='stat-value ok';
    } else {
      out.innerHTML = '<div class="pill bad">✗ '+d.mensaje+'</div>';
      sv.textContent='✗ Inválida'; sv.className='stat-value bad';
    }
  } catch(e) { log(e.message,'err'); }
}

async function checkBal() {
  let addr = document.getElementById('bal-addr').value.trim();
  if (!addr && wallet) { addr=wallet.address; document.getElementById('bal-addr').value=addr; }
  if (!addr) { toast('Introduce una address','err'); return; }
  try {
    const d = await api('/get_balance/'+addr);
    document.getElementById('bal-out').innerHTML = `
      <div class="wbox" style="margin-top:10px;">
        <div style="font-size:9px;color:#bbb;margin-bottom:3px;">Address</div>
        <div class="waddr">${trunc(d.address,16)}</div>
        <div style="font-size:20px;font-weight:700;color:#1a1f4e;margin-top:4px;">${d.saldo} SIMI</div>
      </div>`;
    log(`Saldo de ${trunc(addr)}: ${d.saldo} SIMI`,'ok');
  } catch(e) { log(e.message,'err'); toast(e.message,'err'); }
}


function openBlocks() {
  document.getElementById('blocks-modal').classList.add('on');
}

function closeBlocks() {
  document.getElementById('blocks-modal').classList.remove('on');
}

window.addEventListener('click', (e) => {

  const modal = document.getElementById('blocks-modal');

  if (e.target === modal) {
    closeBlocks();
  }

});


// ── ONBOARDING ──
let currentUser = null;

function obNext1() {
  const val = document.getElementById('ob-username').value.trim();
  const err = document.getElementById('ob-err-1');
  if (!val) { err.textContent = 'Escribe un nombre de usuario.'; return; }
  if (val.length < 3) { err.textContent = 'Mínimo 3 caracteres.'; return; }
  if (!/^[a-zA-Z0-9_áéíóúÁÉÍÓÚüÜñÑ]+$/.test(val)) { err.textContent = 'Solo letras, números y guiones bajos.'; return; }
  err.textContent = '';
  currentUser = val;
  document.getElementById('ob-panel-1').classList.remove('active');
  document.getElementById('ob-panel-2').classList.add('active');
  document.getElementById('step-dot-1').classList.remove('active');
  document.getElementById('step-dot-2').classList.add('active');
  obGenWallet();
}

function obBack() {
  document.getElementById('ob-panel-2').classList.remove('active');
  document.getElementById('ob-panel-1').classList.add('active');
  document.getElementById('step-dot-2').classList.remove('active');
  document.getElementById('step-dot-1').classList.add('active');
}

async function obGenWallet() {
  const spin = document.getElementById('ob-spin');
  const err  = document.getElementById('ob-err-2');
  spin.classList.add('on');
  err.textContent = '';
  document.getElementById('ob-enter-btn').disabled = true;
  document.getElementById('ob-wallet-card').classList.remove('visible');
  try {
    const w = await api('/new_wallet');
    wallet = w;
    document.getElementById('ob-waddr').textContent = w.address;
    document.getElementById('ob-wpub').textContent  = w.public_key.slice(0,48)+'…';
    document.getElementById('ob-wallet-card').classList.add('visible');
    document.getElementById('ob-enter-btn').disabled = false;
    // Pre-fill main app fields
    document.getElementById('tx-from').value  = w.address;
    document.getElementById('tx-priv').value  = w.private_key;
    document.getElementById('wallet-area').innerHTML = `
      <div class="wbox">
        <div style="font-size:9px;color:#bbb;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Address</div>
        <div class="waddr">${w.address}</div>
        <div style="font-size:9px;color:#bbb;text-transform:uppercase;letter-spacing:.07em;margin:8px 0 3px;">Llave pública</div>
        <div class="wpub">${w.public_key.slice(0,40)}…</div>
      </div>
      <div style="font-size:10px;color:#c62828;">⚠ Nunca compartas tu llave privada.</div>`;
  } catch(e) {
    err.textContent = 'No se pudo conectar al servidor. Asegúrate de que blockchain.py esté corriendo.';
  } finally {
    spin.classList.remove('on');
  }
}

function obEnter() {
  if (!wallet) return;
  // Show user in nav
  const name = currentUser;
  document.getElementById('nav-avatar').textContent = name.charAt(0).toUpperCase();
  document.getElementById('nav-uname').textContent  = name;
  document.getElementById('nav-user').style.display = 'flex';
  // Hide onboarding
  document.getElementById('onboarding').classList.add('hidden');
  log(`Sesión iniciada como "${name}" · ${wallet.address.slice(0,10)}…`, 'ok');
  toast(`Bienvenido, ${name}!`, 'ok');
}

// Enter key on username field
document.getElementById('ob-username').addEventListener('keydown', e => {
  if (e.key === 'Enter') obNext1();
});

ping();
setInterval(ping, 30000);
</script>
</body>
</html>
