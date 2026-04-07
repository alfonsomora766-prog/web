// =====================================================
// FUNDACITE CARABOBO — main.js
// =====================================================

'use strict';

/* ── SIDEBAR TOGGLE ── */
function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  if (window.innerWidth <= 768) {
    sb.classList.toggle('open');
  } else {
    sb.classList.toggle('hidden');
    document.querySelector('.main-wrapper').style.marginLeft =
      sb.classList.contains('hidden') ? '0' : 'var(--sidebar-w)';
  }
}

/* ── NOTIFICATION PANEL ── */
function toggleNotifPanel() {
  const panel = document.getElementById('notifPanel');
  panel.classList.toggle('open');
}
document.addEventListener('click', (e) => {
  const wrapper = document.querySelector('.notif-wrapper');
  if (wrapper && !wrapper.contains(e.target)) {
    document.getElementById('notifPanel')?.classList.remove('open');
  }
});

/* ── CLOSE SIDEBAR ON MOBILE OUTSIDE CLICK ── */
document.addEventListener('click', (e) => {
  if (window.innerWidth <= 768) {
    const sb = document.getElementById('sidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    if (sb && sb.classList.contains('open') && !sb.contains(e.target) && e.target !== toggle) {
      sb.classList.remove('open');
    }
  }
});

/* ── AUTO DISMISS FLASH ── */
document.addEventListener('DOMContentLoaded', () => {
  const flash = document.getElementById('flashMsg');
  if (flash) setTimeout(() => flash.style.opacity = '0', 4000);

  // Set page title in topbar
  const h1 = document.querySelector('.page-title');
  const tb = document.getElementById('pageTitle');
  if (h1 && tb) tb.textContent = h1.textContent;
});

/* ── TOGGLE PASSWORD VISIBILITY ── */
function togglePass(inputId, btn) {
  const input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    btn.textContent = '🙈';
  } else {
    input.type = 'password';
    btn.textContent = '👁';
  }
}

/* ── REAL-TIME SEARCH FILTER ── */
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.querySelector('input[name="q"]');
  if (searchInput) {
    let debounce;
    searchInput.addEventListener('input', () => {
      clearTimeout(debounce);
      debounce = setTimeout(() => {
        // Let the form submit naturally, or we can filter table rows client-side:
        const term = searchInput.value.toLowerCase();
        document.querySelectorAll('.table tbody tr').forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(term) ? '' : 'none';
        });
      }, 300);
    });
  }
});

/* ── CHARTS (Dashboard) ── */
// Inline mini chart library using Canvas 2D (no external deps)
function initCharts(byMonthData, byUserData, totals) {

  /* Pie Chart */
  const pieCanvas = document.getElementById('pieChart');
  if (pieCanvas) {
    const ctx = pieCanvas.getContext('2d');
    const data = [
      { label: 'Realizadas',    value: totals.realizadas,    color: '#22c55e' },
      { label: 'Pendientes',    value: totals.pendientes,    color: '#f59e0b' },
      { label: 'No Realizadas', value: totals.no_realizadas, color: '#ef4444' },
      { label: 'Por Aprobar',   value: totals.por_aprobar,   color: '#a855f7' },
    ].filter(d => d.value > 0);

    const total = data.reduce((s, d) => s + d.value, 0);
    if (total === 0) { pieCanvas.style.display = 'none'; return; }

    drawPie(ctx, pieCanvas, data, total);
  }

  /* Bar Chart — Actividades por Mes */
  const barCanvas = document.getElementById('barChart');
  if (barCanvas && byMonthData.length > 0) {
    drawBar(barCanvas.getContext('2d'), barCanvas, byMonthData);
  }

  /* User Chart */
  const userCanvas = document.getElementById('userChart');
  if (userCanvas && byUserData.length > 0) {
    drawHorizontalBar(userCanvas.getContext('2d'), userCanvas, byUserData);
  }
}

function drawPie(ctx, canvas, data, total) {
  const w = canvas.offsetWidth || 300;
  canvas.width  = w;
  canvas.height = 200;
  const cx = w / 2, cy = 95, r = 80;
  let angle = -Math.PI / 2;

  data.forEach(d => {
    const slice = (d.value / total) * Math.PI * 2;
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.arc(cx, cy, r, angle, angle + slice);
    ctx.closePath();
    ctx.fillStyle = d.color;
    ctx.fill();
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    ctx.stroke();
    angle += slice;
  });

  // Center hole
  ctx.beginPath();
  ctx.arc(cx, cy, r * 0.55, 0, Math.PI * 2);
  ctx.fillStyle = '#fff';
  ctx.fill();

  // Center text
  ctx.fillStyle = '#1a3a5c';
  ctx.font = 'bold 22px Sora, sans-serif';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText(total, cx, cy - 6);
  ctx.font = '11px Sora, sans-serif';
  ctx.fillStyle = '#64748b';
  ctx.fillText('total', cx, cy + 14);

  // Legend
  const legendY = 185;
  const step = w / data.length;
  data.forEach((d, i) => {
    const x = step * i + step / 2;
    ctx.fillStyle = d.color;
    ctx.fillRect(x - 30, legendY - 8, 10, 10);
    ctx.fillStyle = '#64748b';
    ctx.font = '10px Sora, sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText(`${d.label} (${d.value})`, x - 17, legendY);
  });
}

function drawBar(ctx, canvas, data) {
  const w = canvas.offsetWidth || 400;
  canvas.width  = w;
  canvas.height = 250;
  const pad = { top: 20, right: 20, bottom: 50, left: 40 };
  const cw  = w - pad.left - pad.right;
  const ch  = canvas.height - pad.top - pad.bottom;
  const max = Math.max(...data.map(d => d.total), 1);
  const bw  = Math.min(40, cw / data.length - 8);

  // Gridlines
  for (let i = 0; i <= 4; i++) {
    const y = pad.top + ch - (i / 4) * ch;
    ctx.strokeStyle = '#e2eaf2';
    ctx.lineWidth = 1;
    ctx.beginPath(); ctx.moveTo(pad.left, y); ctx.lineTo(pad.left + cw, y); ctx.stroke();
    ctx.fillStyle = '#94a3b8'; ctx.font = '10px Sora'; ctx.textAlign = 'right';
    ctx.fillText(Math.round(max * i / 4), pad.left - 6, y + 4);
  }

  data.forEach((d, i) => {
    const x = pad.left + (i / data.length) * cw + (cw / data.length - bw) / 2;
    const bh = (d.total / max) * ch;
    const y = pad.top + ch - bh;

    // Gradient bar
    const grad = ctx.createLinearGradient(0, y, 0, y + bh);
    grad.addColorStop(0, '#1a6cad');
    grad.addColorStop(1, '#00c4d4');
    ctx.fillStyle = grad;
    ctx.beginPath();
    ctx.roundRect ? ctx.roundRect(x, y, bw, bh, [4, 4, 0, 0]) : ctx.rect(x, y, bw, bh);
    ctx.fill();

    // Value
    ctx.fillStyle = '#1a3a5c';
    ctx.font = 'bold 11px Sora';
    ctx.textAlign = 'center';
    ctx.fillText(d.total, x + bw / 2, y - 5);

    // Label
    ctx.fillStyle = '#64748b';
    ctx.font = '10px Sora';
    const label = d.mes ? d.mes.substring(5) + '/' + d.mes.substring(2, 4) : '';
    ctx.fillText(label, x + bw / 2, pad.top + ch + 18);
  });
}

function drawHorizontalBar(ctx, canvas, data) {
  const w = canvas.offsetWidth || 400;
  canvas.height = data.length * 36 + 20;
  canvas.width  = w;
  const padL = 160, padR = 60, bh = 20;
  const max = Math.max(...data.map(d => d.total), 1);
  const cw  = w - padL - padR;

  data.forEach((d, i) => {
    const y = i * 36 + 10;
    const bw = (d.total / max) * cw;

    // Label
    ctx.fillStyle = '#1e293b'; ctx.font = '12px Sora'; ctx.textAlign = 'right';
    const name = d.usuario.length > 20 ? d.usuario.substring(0, 19) + '…' : d.usuario;
    ctx.fillText(name, padL - 8, y + 14);

    // Bar
    const grad = ctx.createLinearGradient(padL, 0, padL + bw, 0);
    grad.addColorStop(0, '#1a3a5c');
    grad.addColorStop(1, '#1a6cad');
    ctx.fillStyle = grad;
    ctx.beginPath();
    ctx.roundRect ? ctx.roundRect(padL, y, bw, bh, 4) : ctx.rect(padL, y, bw, bh);
    ctx.fill();

    // Value
    ctx.fillStyle = '#1a3a5c'; ctx.font = 'bold 11px Sora'; ctx.textAlign = 'left';
    ctx.fillText(d.total, padL + bw + 8, y + 14);
  });
}

/* ── CONFIRM WRAPPER ── */
function confirmAction(msg) {
  return window.confirm(msg || '¿Estás seguro?');
}

/* ── RESIZE CHARTS ON WINDOW RESIZE ── */
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    if (typeof byMonthData !== 'undefined') {
      initCharts(byMonthData, byUserData, totals);
    }
  }, 300);
});
