document.addEventListener('DOMContentLoaded', () => {
  // ── AOS ──────────────────────────────────────────
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 500, once: true, offset: 40, easing: 'ease-out-cubic' });
  }

  // ── Smooth Scroll ─────────────────────────────────
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
  });

  // ── DB Badge log ──────────────────────────────────
  const dbBadge = document.querySelector('.db-badge');
  if (dbBadge) console.log('[TopUpKu] DB Status:', dbBadge.textContent.trim());

  // ── Hero Slider ───────────────────────────────────
  initSlider();
  initSlideParticles();

  // ── Filter Buttons (game section) ─────────────────
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  // ── Search (live filter dengan alias) ───────────────
  const ALIAS = {
    'ml': 'mobile legends',
    'mlbb': 'mobile legends',
    'ff': 'free fire',
    'pubg': 'pubg mobile',
    'rblx': 'roblox',
    'rb': 'roblox',
    'hok': 'honor of kings',
    'mc': 'magic chess',
  };

  const searchInput = document.querySelector('.search-input');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      let q = searchInput.value.toLowerCase().trim();
      if (ALIAS[q]) q = ALIAS[q];
      const cards = document.querySelectorAll('.game-card');
      if (q === '') {
        cards.forEach(card => {
          card.style.display = '';
          card.style.opacity = '1';
        });
        document.querySelector('.empty-search-msg')?.remove();
        return;
      }
      let found = 0;
      cards.forEach(card => {
        const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
        const matches = name.includes(q);
        card.style.display = matches ? '' : 'none';
        card.style.opacity = matches ? '1' : '';
        if (matches) found++;
      });
      document.querySelector('.empty-search-msg')?.remove();
      if (found === 0) {
        const grid = document.querySelector('.game-grid');
        const msg = document.createElement('p');
        msg.className = 'empty-state empty-search-msg';
        msg.textContent = `Game "${searchInput.value}" tidak ditemukan.`;
        grid.appendChild(msg);
      }
    });
  }

  // ── Particles BG (aktif di semua halaman, sama seperti admin) ──
  initParticles();

  // ── Topup Page Logic ──────────────────────────────
  initTopupPage();
});

// ════════════════════════════════════════════════════
//  SLIDER
// ════════════════════════════════════════════════════
let currentSlide = 0;
let slideTimer = null;
const SLIDE_COUNT = 3;
const SLIDE_INTERVAL = 5000;

function initSlider() {
  const track = document.getElementById('sliderTrack');
  if (!track) return;
  goSlide(0);
  startAutoSlide();
}

function goSlide(index) {
  currentSlide = (index + SLIDE_COUNT) % SLIDE_COUNT;
  const track = document.getElementById('sliderTrack');
  if (!track) return;
  track.style.transform = `translateX(-${currentSlide * 100}%)`;
  document.querySelectorAll('.dot').forEach((d, i) => d.classList.toggle('active', i === currentSlide));
}

function changeSlide(dir) {
  goSlide(currentSlide + dir);
  resetAutoSlide();
}

function startAutoSlide() {
  slideTimer = setInterval(() => goSlide(currentSlide + 1), SLIDE_INTERVAL);
}

function resetAutoSlide() {
  clearInterval(slideTimer);
  startAutoSlide();
}

window.goSlide = goSlide;
window.changeSlide = changeSlide;

function toggleMenu() {
  const menu = document.getElementById('mobileMenu');
  if (menu) menu.classList.toggle('open');
}
window.toggleMenu = toggleMenu;

// ════════════════════════════════════════════════════
//  PARTICLES BACKGROUND (sama seperti admin_transaksi.php)
// ════════════════════════════════════════════════════
function initParticles() {
  const canvas = document.getElementById('bg-canvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  let w, h, particles = [];
  const COLORS = ['rgba(0,245,255,', 'rgba(191,0,255,', 'rgba(0,255,136,'];
  const particleCount = 70;
  const maxDist = 130;

  const resize = () => { w = canvas.width = window.innerWidth; h = canvas.height = window.innerHeight; };
  window.addEventListener('resize', resize);
  resize();

  class Particle {
    constructor() { this.reset(); }
    reset() {
      this.x = Math.random() * w;
      this.y = Math.random() * h;
      this.vx = (Math.random() - 0.5) * 0.6;
      this.vy = (Math.random() - 0.5) * 0.6;
      this.size = Math.random() * 2 + 1;
      this.color = COLORS[Math.floor(Math.random() * COLORS.length)];
    }
    update() {
      this.x += this.vx; this.y += this.vy;
      if (this.x < 0 || this.x > w) this.vx *= -1;
      if (this.y < 0 || this.y > h) this.vy *= -1;
    }
    draw() {
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fillStyle = this.color + '0.8)';
      ctx.fill();
    }
  }
  for (let i = 0; i < particleCount; i++) particles.push(new Particle());
  const animate = () => {
    ctx.clearRect(0, 0, w, h);
    particles.forEach((p, i) => {
      p.update(); p.draw();
      for (let j = i + 1; j < particles.length; j++) {
        const dx = p.x - particles[j].x, dy = p.y - particles[j].y;
        const dist = Math.hypot(dx, dy);
        if (dist < maxDist) {
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(particles[j].x, particles[j].y);
          ctx.strokeStyle = 'rgba(0,245,255,' + (0.12 * (1 - dist / maxDist)) + ')';
          ctx.lineWidth = 1;
          ctx.stroke();
        }
      }
    });
    requestAnimationFrame(animate);
  };
  animate();
}

function initSlideParticles() {
  const slider = document.querySelector('.hero-slider');
  if (!slider) return;
  const canvas = document.createElement('canvas');
  canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:1;';
  slider.appendChild(canvas);
  const ctx = canvas.getContext('2d');
  let w, h;
  const resize = () => { w = canvas.width = slider.offsetWidth; h = canvas.height = slider.offsetHeight; };
  window.addEventListener('resize', resize);
  resize();
  const CONFIGS = [
    { count: 80, colors: ['rgba(200,0,255,', 'rgba(0,150,255,', 'rgba(255,0,200,', 'rgba(255,255,255,'], size: [2, 6], speed: 0.6, shape: 'star' },
    { count: 60, colors: ['rgba(255,80,0,', 'rgba(255,160,0,', 'rgba(255,40,0,'], size: [1, 4], speed: 0.7, shape: 'ember' },
    { count: 70, colors: ['rgba(150,180,255,', 'rgba(200,220,255,', 'rgba(100,140,255,'], size: [0.5, 2], speed: 1.2, shape: 'rain' },
  ];

  class Particle {
    constructor(cfg) { this.cfg = cfg; this.reset(true); }
    reset(initial = false) {
      const cfg = this.cfg;
      this.x = Math.random() * w;
      this.y = initial ? Math.random() * h : (cfg.shape === 'ember' ? h + 10 : cfg.shape === 'rain' ? -10 : Math.random() * h);
      this.size = cfg.size[0] + Math.random() * (cfg.size[1] - cfg.size[0]);
      this.color = cfg.colors[Math.floor(Math.random() * cfg.colors.length)];
      this.alpha = 0.4 + Math.random() * 0.6;
      this.speed = (0.5 + Math.random()) * cfg.speed;
      this.drift = (Math.random() - 0.5) * 0.8;
      this.life = 0;
      this.maxLife = 80 + Math.random() * 120;
    }
    update() {
      const cfg = this.cfg; this.life++;
      if (cfg.shape === 'ember') { this.y -= this.speed; this.x += this.drift; if (this.y < -10) this.reset(); }
      else if (cfg.shape === 'rain') { this.y += this.speed * 2; this.x += this.speed * 0.8; if (this.y > h + 10 || this.x > w + 10) this.reset(); }
      else { this.y -= this.speed * 0.3; this.x += this.drift * 0.5; if (this.y < -10) this.y = h + 10; if (this.x < -10) this.x = w + 10; if (this.x > w + 10) this.x = -10; }
    }
    draw() {
      const fade = this.life < 20 ? this.life / 20 : this.life > this.maxLife - 20 ? (this.maxLife - this.life) / 20 : 1;
      const twinkle = 0.6 + 0.4 * Math.sin(this.life * 0.15 + this.x);
      const finalAlpha = this.alpha * fade * twinkle;
      ctx.beginPath();
      if (this.cfg.shape === 'rain') {
        ctx.moveTo(this.x, this.y); ctx.lineTo(this.x - this.size, this.y + this.size * 5);
        ctx.strokeStyle = this.color + (finalAlpha) + ')'; ctx.lineWidth = this.size * 0.5; ctx.stroke();
      } else {
        ctx.shadowBlur = this.size * 8; ctx.shadowColor = this.color + '1)';
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2); ctx.fillStyle = this.color + finalAlpha + ')'; ctx.fill();
        ctx.beginPath(); ctx.arc(this.x, this.y, this.size * 0.4, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(255,255,255,' + (finalAlpha * 0.9) + ')'; ctx.shadowBlur = this.size * 4; ctx.shadowColor = 'rgba(255,255,255,0.8)'; ctx.fill(); ctx.shadowBlur = 0;
      }
    }
  }
  let particles = [];
  CONFIGS.forEach(cfg => { for (let i = 0; i < cfg.count; i++) particles.push({ p: new Particle(cfg), cfg }); });
  const animate = () => {
    ctx.clearRect(0, 0, w, h);
    const cfg = CONFIGS[currentSlide];
    particles.filter(item => item.cfg === cfg).forEach(item => { item.p.update(); item.p.draw(); });
    requestAnimationFrame(animate);
  };
  animate();
}

// ── UPDATED TOPUP PAGE LOGIC ──────────────────────
function initTopupPage() {
  document.querySelectorAll('.nominal-item').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.nominal-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      updateTotal();
    });
  });

  document.querySelectorAll('.payment-item').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.payment-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      updateTotal();
    });
  });

  const checkBtn = document.getElementById('check-id-btn');
  if (checkBtn) {
    checkBtn.addEventListener('click', async () => {
      const userId = document.getElementById('user_id')?.value.trim();
      const resultBox = document.getElementById('validation-result');
      if (!userId) {
        resultBox.className = 'validation-result error';
        resultBox.innerHTML = '❌ Masukkan User ID terlebih dahulu!';
        return;
      }
      checkBtn.disabled = true;
      const originalHTML = checkBtn.innerHTML;
      checkBtn.innerHTML = '<span class="loader"></span> Mengecek...';
      resultBox.className = 'validation-result';
      await new Promise(r => setTimeout(r, 1000));
      if (userId.length >= 4) {
        resultBox.className = 'validation-result success';
        resultBox.innerHTML = `✅ Akun ditemukan: <b>Player_${userId.slice(-4)}</b>`;
      } else {
        resultBox.className = 'validation-result error';
        resultBox.innerHTML = '❌ ID tidak valid. Periksa kembali.';
      }
      checkBtn.disabled = false;
      checkBtn.innerHTML = originalHTML;
    });
  }

  const buyBtn = document.getElementById('buy-btn');
  if (buyBtn) {
    buyBtn.addEventListener('click', async () => {
      const activeNominal = document.querySelector('.nominal-item.active');
      const activePayment = document.querySelector('.payment-item.active');
      const userId = document.getElementById('user_id')?.value.trim();
      const zoneId = document.getElementById('zone_id')?.value.trim() || '';

      if (!activeNominal) return alert('⚠️ Pilih nominal top up dulu!');
      if (!activePayment) return alert('⚠️ Pilih metode pembayaran dulu!');
      if (!userId) return alert('⚠️ Masukkan User ID dulu!');

      const resultBox = document.getElementById('validation-result');
      if (!resultBox || !resultBox.classList.contains('success')) {
        return alert('⚠️ Klik "Cek ID" dulu untuk memverifikasi akun!');
      }

      const originalHTML = buyBtn.innerHTML;
      buyBtn.disabled = true;
      buyBtn.innerHTML = '<span class="loader"></span> Memproses...';

      try {
        const response = await fetch('process_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            product_id: parseInt(activeNominal.dataset.id),
            payment_method_id: parseInt(activePayment.dataset.id),
            user_id: userId,
            zone_id: zoneId
          })
        });
        const data = await response.json();
        if (data.success) {
          window.location.href = data.redirect;
        } else {
          alert('❌ ' + (data.message || 'Terjadi kesalahan. Coba lagi.'));
          buyBtn.disabled = false;
          buyBtn.innerHTML = originalHTML;
        }
      } catch (err) {
        alert('❌ Gagal terhubung ke server.');
        buyBtn.disabled = false;
        buyBtn.innerHTML = originalHTML;
      }
    });
  }
}

function updateTotal() {
  const activeNominal = document.querySelector('.nominal-item.active');
  const activePayment = document.querySelector('.payment-item.active');
  const productPrice = activeNominal ? parseInt(activeNominal.dataset.price) || 0 : 0;
  const fee = activePayment ? parseInt(activePayment.dataset.fee) || 0 : 0;
  const sp = document.getElementById('summary-product');
  const sf = document.getElementById('summary-fee');
  const tp = document.getElementById('total-price');
  if (sp) sp.textContent = productPrice ? `Rp ${productPrice.toLocaleString('id-ID')}` : '-';
  if (sf) sf.textContent = `Rp ${fee.toLocaleString('id-ID')}`;
  if (tp) tp.textContent = `Rp ${(productPrice + fee).toLocaleString('id-ID')}`;
}