<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TopUpKu — Promo</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Exo+2:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    /* ══════════════════════════════════════════
       EXACT VARIABLES & BASE FROM style.css
    ══════════════════════════════════════════ */
    :root {
      --bg-body: #080a12;
      --bg-card: #0f1120;
      --bg-nav: rgba(8,10,18,0.95);
      --text-primary: #e8eaed;
      --text-muted: #7a8090;
      --neon-cyan:   #00f5ff;
      --neon-purple: #bf00ff;
      --neon-green:  #00ff88;
      --neon-orange: #ff6a00;
      --neon-pink:   #ff0090;
      --glow-cyan:   rgba(0,245,255,0.35);
      --glow-purple: rgba(191,0,255,0.35);
      --glow-green:  rgba(0,255,136,0.35);
      --glow-orange: rgba(255,106,0,0.35);
      --radius: 14px;
      --transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
      --font-display: 'Rajdhani', sans-serif;
      --font-body: 'Exo 2', sans-serif;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: var(--font-body);
      background: var(--bg-body);
      color: var(--text-primary);
      line-height: 1.6;
      overflow-x: hidden;
    }
    /* Grid lines — persis dari style.css */
    body::before {
      content: '';
      position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background-image:
        linear-gradient(rgba(0,245,255,0.025) 1px, transparent 1px),
        linear-gradient(90deg,rgba(0,245,255,0.025) 1px,transparent 1px);
      background-size: 60px 60px;
      z-index: -1; pointer-events: none;
    }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

    /* ── BG CANVAS (particles) ── */
    #bg-canvas {
      position: fixed; top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1; pointer-events: none;
      opacity: 0.3;
    }

    /* ── DB BADGE ── */
    .db-badge {
      position: fixed; bottom: 20px; right: 20px;
      padding: 6px 14px; border-radius: 8px;
      font-size: 0.75rem; font-weight: 700; z-index: 9999;
      font-family: var(--font-body);
      backdrop-filter: blur(10px); border: 1px solid transparent;
      letter-spacing: 0.03em;
      background: rgba(0,255,136,0.1); color: var(--neon-green);
      border-color: rgba(0,255,136,0.4);
      box-shadow: 0 0 12px rgba(0,255,136,0.2);
    }

    /* ── TOPBAR ── */
    .topbar {
      background: linear-gradient(90deg, #0d0015, #080a12, #001a0d);
      border-bottom: 1px solid rgba(0,245,255,0.08);
      padding: 7px 0;
    }
    .topbar-inner {
      display: flex; justify-content: space-between; align-items: center;
      max-width: 1200px; margin: 0 auto; padding: 0 24px;
    }
    .topbar-slogan {
      font-size: 0.78rem; font-weight: 700;
      font-family: var(--font-display);
      letter-spacing: 0.12em; text-transform: uppercase;
      color: var(--neon-cyan);
      text-shadow: 0 0 10px var(--glow-cyan);
    }
    .btn-outline-sm {
      padding: 5px 14px; border-radius: 6px;
      font-size: 0.75rem; font-weight: 700;
      border: 1px solid rgba(0,245,255,0.4);
      color: var(--neon-cyan); background: transparent;
      cursor: pointer; text-decoration: none;
      transition: var(--transition); font-family: var(--font-body);
      letter-spacing: 0.05em;
    }
    .btn-outline-sm:hover {
      background: rgba(0,245,255,0.1);
      box-shadow: 0 0 12px var(--glow-cyan);
    }

    /* ── NAVBAR ── */
    .navbar {
      position: sticky; top: 0; z-index: 200;
      background: var(--bg-nav);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(0,245,255,0.07);
      padding: 14px 0;
    }
    .navbar-inner {
      display: flex; align-items: center; gap: 32px;
      max-width: 1200px; margin: 0 auto; padding: 0 24px;
    }
    .brand {
      text-decoration: none; display: flex; align-items: center; gap: 8px; flex-shrink: 0;
    }
    .brand-icon { font-size: 1.4rem; filter: drop-shadow(0 0 8px var(--neon-cyan)); }
    .brand-text {
      font-family: var(--font-display); font-size: 1.7rem; font-weight: 700;
      color: #fff; letter-spacing: 0.04em;
    }
    .brand-highlight { color: var(--neon-cyan); text-shadow: 0 0 12px var(--glow-cyan); }
    .nav-menu { display: flex; list-style: none; gap: 4px; }
    .nav-link {
      text-decoration: none; color: var(--text-muted);
      font-family: var(--font-body); font-weight: 600;
      font-size: 0.95rem; padding: 8px 14px; border-radius: 8px;
      transition: var(--transition); position: relative; letter-spacing: 0.03em;
    }
    .nav-link::after {
      content: ''; position: absolute; bottom: 4px; left: 50%;
      transform: translateX(-50%); width: 0; height: 2px;
      background: var(--neon-cyan); box-shadow: 0 0 8px var(--glow-cyan);
      transition: width 0.3s ease; border-radius: 2px;
    }
    .nav-link:hover, .nav-link.active {
      color: var(--neon-cyan); background: rgba(0,245,255,0.06);
    }
    .nav-link:hover::after, .nav-link.active::after { width: 60%; }
    .nav-right { margin-left: auto; display: flex; align-items: center; gap: 14px; }
    .search-wrapper {
      display: flex; align-items: center; gap: 8px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(0,245,255,0.12);
      border-radius: 10px; padding: 8px 14px; transition: var(--transition);
    }
    .search-wrapper:focus-within {
      border-color: var(--neon-cyan);
      box-shadow: 0 0 0 3px rgba(0,245,255,0.12), 0 0 20px rgba(0,245,255,0.08);
    }
    .search-icon { font-size: 0.9rem; opacity: 0.5; }
    .search-input {
      background: none; border: none; outline: none;
      color: var(--text-primary); font-family: var(--font-body);
      font-size: 0.9rem; width: 170px;
    }
    .search-input::placeholder { color: var(--text-muted); }
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 6px; padding: 10px 22px; border-radius: 10px; font-weight: 700;
      cursor: pointer; border: none; transition: var(--transition);
      font-family: var(--font-body); font-size: 0.95rem;
      text-decoration: none; letter-spacing: 0.04em;
    }
    .btn-neon {
      background: linear-gradient(135deg, var(--neon-cyan), #0080ff);
      color: #000; box-shadow: 0 4px 18px rgba(0,245,255,0.35);
    }
    .btn-neon:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 30px rgba(0,245,255,0.6), 0 6px 25px rgba(0,245,255,0.4);
    }

    /* ══════════════════════════════════════════
       HERO PROMO — animated aurora background
    ══════════════════════════════════════════ */
    .promo-hero {
      position: relative; overflow: hidden;
      min-height: 300px;
      display: flex; align-items: center; justify-content: center;
      background: #060010;
      border-bottom: 1px solid rgba(0,245,255,0.07);
    }
    .promo-hero::before {
      content: '';
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 80% 60% at 70% 50%, rgba(120,0,255,0.3) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 30% 60%, rgba(0,80,255,0.2) 0%, transparent 55%),
        radial-gradient(ellipse 40% 40% at 80% 20%, rgba(0,245,255,0.15) 0%, transparent 50%);
      animation: auroraPromo 7s ease-in-out infinite alternate;
      pointer-events: none;
    }
    .promo-hero::after {
      content: '';
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 50% 50% at 20% 80%, rgba(0,200,255,0.12) 0%, transparent 55%),
        radial-gradient(ellipse 70% 40% at 60% 30%, rgba(191,0,255,0.18) 0%, transparent 50%);
      animation: auroraPromo2 9s ease-in-out infinite alternate;
      pointer-events: none;
    }
    @keyframes auroraPromo {
      0%   { transform: scale(1) translate(0,0); opacity: 0.8; }
      33%  { transform: scale(1.08) translate(-20px,15px); opacity: 1; }
      66%  { transform: scale(0.95) translate(15px,-10px); opacity: 0.9; }
      100% { transform: scale(1.05) translate(-10px,20px); opacity: 1; }
    }
    @keyframes auroraPromo2 {
      0%   { transform: scale(1.1) translate(10px,-15px); opacity: 0.6; }
      50%  { transform: scale(0.9) translate(-20px,10px); opacity: 1; }
      100% { transform: scale(1.05) translate(15px,-5px); opacity: 0.8; }
    }
    /* Shooting stars di hero */
    .shooting-star {
      position: absolute; width: 3px; height: 3px; border-radius: 50%;
      background: #fff;
      box-shadow: 0 0 6px 2px rgba(180,0,255,0.8), 0 0 20px rgba(180,0,255,0.4);
    }
    .shooting-star::after {
      content: ''; position: absolute; top: 50%; right: 0;
      transform: translateY(-50%); width: 80px; height: 1px;
      background: linear-gradient(90deg,rgba(180,0,255,0.8),transparent);
    }
    .ss1 { top: 15%; left: 80%; animation: shoot1 4s linear infinite; }
    .ss2 { top: 35%; left: 90%; animation: shoot1 6s linear infinite 1.5s; }
    .ss3 { top: 60%; left: 75%; animation: shoot1 5s linear infinite 3s; }
    .ss4 { top: 25%; left: 95%; animation: shoot1 7s linear infinite 0.8s; }
    @keyframes shoot1 {
      0%   { transform: translate(0,0) rotate(215deg); opacity: 1; }
      70%  { opacity: 1; }
      100% { transform: translate(-800px,400px) rotate(215deg); opacity: 0; }
    }
    .promo-hero-content {
      position: relative; z-index: 2; text-align: center; padding: 60px 24px 52px;
    }
    .hero-badge {
      display: inline-block; margin-bottom: 16px;
      padding: 5px 16px; border-radius: 20px;
      font-size: 0.75rem; font-weight: 700;
      font-family: var(--font-display); letter-spacing: 0.1em;
      background: rgba(191,0,255,0.15);
      border: 1px solid rgba(191,0,255,0.4);
      color: #d966ff;
      box-shadow: 0 0 12px rgba(191,0,255,0.2);
    }
    .promo-hero-content h1 {
      font-family: var(--font-display);
      font-size: 3rem; font-weight: 700; line-height: 1.1;
      margin-bottom: 12px;
      text-shadow: 0 2px 20px rgba(0,0,0,0.5);
    }
    .neon-text {
      color: var(--neon-cyan);
      text-shadow: 0 0 20px var(--glow-cyan), 0 0 40px rgba(0,245,255,0.3);
    }
    .promo-hero-content p {
      color: rgba(255,255,255,0.6); font-size: 1rem;
      max-width: 480px; margin: 0 auto;
    }

    /* ── FILTER TABS ── */
    .filter-bar {
      background: rgba(8,10,18,0.98);
      border-bottom: 1px solid rgba(0,245,255,0.07);
      padding: 16px 0;
      position: sticky; top: 64px; z-index: 100;
      backdrop-filter: blur(20px);
    }
    .filter-inner {
      display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;
      max-width: 1200px; margin: 0 auto; padding: 0 24px;
    }
    .filter-btn {
      padding: 7px 18px; border-radius: 8px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.08);
      color: var(--text-muted); font-family: var(--font-body);
      font-size: 0.85rem; font-weight: 600;
      cursor: pointer; transition: var(--transition);
    }
    .filter-btn:hover, .filter-btn.active {
      background: rgba(0,245,255,0.08);
      border-color: rgba(0,245,255,0.35);
      color: var(--neon-cyan);
      box-shadow: 0 0 12px rgba(0,245,255,0.12);
    }

    /* ── COUNTDOWN BANNER ── */
    .countdown-banner {
      background: linear-gradient(135deg, rgba(120,0,255,0.08), rgba(0,80,255,0.08));
      border: 1px solid rgba(191,0,255,0.25);
      border-radius: var(--radius);
      padding: 20px 28px;
      display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
      position: relative; overflow: hidden;
    }
    .countdown-banner::before {
      content: '';
      position: absolute; inset: 0;
      background: linear-gradient(90deg, transparent, rgba(0,245,255,0.03), transparent);
      animation: scanline 3s linear infinite;
    }
    @keyframes scanline {
      0%   { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    .cdown-icon { font-size: 2.2rem; flex-shrink: 0; }
    .cdown-text h3 {
      font-family: var(--font-display); font-size: 1.1rem; font-weight: 700;
      color: #ffd600; text-shadow: 0 0 12px rgba(255,214,0,0.4);
    }
    .cdown-text p { font-size: 0.85rem; color: var(--text-muted); }
    .cdown-timer {
      display: flex; gap: 10px; margin-left: auto; align-items: center; flex-shrink: 0;
    }
    .time-box {
      background: rgba(0,0,0,0.5); border: 1px solid rgba(0,245,255,0.2);
      border-radius: 10px; padding: 10px 14px; text-align: center; min-width: 58px;
      box-shadow: 0 0 12px rgba(0,245,255,0.08), inset 0 0 10px rgba(0,245,255,0.04);
    }
    .time-num {
      font-family: var(--font-display); font-size: 1.8rem; font-weight: 700;
      color: var(--neon-cyan); line-height: 1;
      text-shadow: 0 0 14px var(--glow-cyan);
    }
    .time-lbl { font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .time-sep {
      font-family: var(--font-display); font-size: 1.6rem; font-weight: 700;
      color: var(--neon-purple); align-self: center; margin-bottom: 12px;
      text-shadow: 0 0 10px var(--glow-purple);
    }

    /* ── SECTION HEADER ── */
    .section-header {
      display: flex; justify-content: space-between; align-items: center;
      margin: 40px 0 22px;
    }
    .section-title {
      font-family: var(--font-display); font-size: 1.4rem; font-weight: 700;
      display: flex; align-items: center; gap: 12px; letter-spacing: 0.04em;
    }
    .neon-bar {
      display: block; width: 4px; height: 28px;
      background: linear-gradient(180deg, var(--neon-cyan), var(--neon-purple));
      border-radius: 4px; box-shadow: 0 0 12px var(--glow-cyan);
    }
    .section-badge {
      padding: 4px 12px; border-radius: 6px;
      font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em;
      background: rgba(255,106,0,0.12); color: var(--neon-orange);
      border: 1px solid rgba(255,106,0,0.3);
      font-family: var(--font-body);
    }
    .see-all {
      font-size: 0.85rem; color: var(--neon-cyan); font-weight: 600;
      text-decoration: none; transition: var(--transition);
    }
    .see-all:hover { text-shadow: 0 0 10px var(--glow-cyan); }

    /* ── PROMO CARDS GRID ── */
    .promo-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }
    .promo-card {
      background: var(--bg-card);
      border-radius: var(--radius);
      overflow: hidden;
      border: 1px solid rgba(255,255,255,0.05);
      transition: var(--transition);
      position: relative; cursor: pointer;
    }
    /* Neon border glow on hover — persis dari game-card */
    .promo-card::before {
      content: '';
      position: absolute; inset: 0;
      border-radius: var(--radius); padding: 1px;
      background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple), var(--neon-cyan));
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: destination-out; mask-composite: exclude;
      opacity: 0; transition: opacity 0.3s ease; pointer-events: none;
    }
    .promo-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 20px 40px rgba(0,0,0,0.5), 0 0 25px rgba(0,245,255,0.15), 0 0 60px rgba(191,0,255,0.08);
    }
    .promo-card:hover::before { opacity: 1; }
    .promo-card:active {
      transform: translateY(-4px) scale(1.01);
      box-shadow: 0 10px 30px rgba(0,0,0,0.5), 0 0 40px rgba(0,245,255,0.4), 0 0 80px rgba(191,0,255,0.25);
    }

    .card-thumb {
      width: 100%; height: 160px;
      display: flex; align-items: center; justify-content: center;
      font-size: 3.5rem; position: relative; overflow: hidden;
    }
    .card-thumb-mlbb   { background: linear-gradient(135deg,#1a0d2e,#2d1a5e,#0d1a3e); }
    .card-thumb-ff     { background: linear-gradient(135deg,#1a0800,#3d1500,#0d0d00); }
    .card-thumb-pubg   { background: linear-gradient(135deg,#0d1a0d,#1a3300,#0d2200); }
    .card-thumb-val    { background: linear-gradient(135deg,#2e0d0d,#5c1a1a,#1a0d0d); }
    .card-thumb-cod    { background: linear-gradient(135deg,#1a1a0d,#3d3300,#0d0d00); }
    .card-thumb-gshin  { background: linear-gradient(135deg,#0d1a2e,#1a2d4a,#0a1020); }

    /* Hover overlay */
    .card-thumb-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(180deg,transparent 30%,rgba(0,0,0,0.7) 100%);
      opacity: 0; transition: opacity 0.3s ease;
    }
    .promo-card:hover .card-thumb-overlay { opacity: 1; }

    .card-ribbon {
      position: absolute; top: 12px; left: 12px;
      padding: 4px 10px; border-radius: 999px;
      font-size: 0.68rem; font-weight: 800;
      letter-spacing: 0.06em; text-transform: uppercase; z-index: 3;
      font-family: var(--font-body);
    }
    .ribbon-hot    { background: #f50057; color: #fff; box-shadow: 0 0 10px rgba(245,0,87,0.5); }
    .ribbon-new    { background: var(--neon-green); color: #000; box-shadow: 0 0 10px rgba(0,255,136,0.5); }
    .ribbon-promo  { background: var(--neon-orange); color: #000; box-shadow: 0 0 10px rgba(255,106,0,0.5); }
    .ribbon-ltd    { background: var(--neon-purple); color: #fff; box-shadow: 0 0 10px rgba(191,0,255,0.5); }

    .discount-badge {
      position: absolute; top: 12px; right: 12px;
      background: rgba(0,0,0,0.75);
      border: 1px solid rgba(255,214,0,0.5);
      color: #ffd600; font-family: var(--font-display);
      font-size: 1rem; font-weight: 700;
      padding: 4px 10px; border-radius: 8px; z-index: 3;
      text-shadow: 0 0 10px rgba(255,214,0,0.5);
    }

    .card-body { padding: 16px 18px 18px; }
    .card-game {
      font-size: 0.72rem; font-weight: 700; color: var(--text-muted);
      text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;
    }
    .card-title {
      font-family: var(--font-display); font-size: 1.05rem; font-weight: 700;
      margin-bottom: 8px; line-height: 1.25;
      transition: var(--transition);
    }
    .promo-card:hover .card-title {
      color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0,245,255,0.3);
    }
    .card-desc { font-size: 0.83rem; color: var(--text-muted); margin-bottom: 14px; line-height: 1.55; }
    .card-foot { display: flex; align-items: center; justify-content: space-between; }
    .card-expiry { font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
    .card-expiry.soon { color: #ff5252; }
    .btn-claim {
      padding: 8px 18px; border-radius: 8px;
      background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple));
      color: #fff; border: none;
      font-family: var(--font-display); font-size: 0.9rem; font-weight: 700;
      cursor: pointer; letter-spacing: 0.04em;
      box-shadow: 0 4px 15px rgba(0,245,255,0.25);
      transition: var(--transition);
    }
    .btn-claim:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 20px rgba(0,245,255,0.5), 0 0 40px rgba(191,0,255,0.2);
    }
    .btn-claim:active {
      box-shadow: 0 0 40px rgba(0,245,255,0.8);
    }

    /* ── VOUCHER SECTION ── */
    .voucher-list { display: flex; flex-direction: column; gap: 12px; }
    .voucher-row {
      background: var(--bg-card);
      border: 1px solid rgba(255,255,255,0.05);
      border-radius: var(--radius);
      display: flex; align-items: center; gap: 16px;
      padding: 16px 20px; transition: var(--transition); cursor: pointer;
      position: relative; overflow: hidden;
    }
    .voucher-row::before {
      content: '';
      position: absolute; inset: 0;
      border-radius: var(--radius); padding: 1px;
      background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple), var(--neon-cyan));
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: destination-out; mask-composite: exclude;
      opacity: 0; transition: opacity 0.3s ease; pointer-events: none;
    }
    .voucher-row:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 20px rgba(0,245,255,0.08); }
    .voucher-row:hover::before { opacity: 1; }
    .voucher-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;
    }
    .vi-green  { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.2); box-shadow: 0 0 12px rgba(0,255,136,0.15); }
    .vi-cyan   { background: rgba(0,245,255,0.1); border: 1px solid rgba(0,245,255,0.2); box-shadow: 0 0 12px rgba(0,245,255,0.15); }
    .vi-orange { background: rgba(255,106,0,0.1); border: 1px solid rgba(255,106,0,0.2); box-shadow: 0 0 12px rgba(255,106,0,0.15); }
    .vi-purple { background: rgba(191,0,255,0.1); border: 1px solid rgba(191,0,255,0.2); box-shadow: 0 0 12px rgba(191,0,255,0.15); }
    .voucher-info { flex: 1; }
    .voucher-info h4 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; margin-bottom: 3px; }
    .voucher-info p { font-size: 0.78rem; color: var(--text-muted); }
    .voucher-code-wrap { text-align: right; }
    .voucher-code {
      background: rgba(0,245,255,0.06);
      border: 1px dashed rgba(0,245,255,0.35);
      border-radius: 8px; padding: 8px 16px;
      font-family: var(--font-display); font-size: 1.05rem; font-weight: 700;
      color: var(--neon-cyan); letter-spacing: 2px; cursor: pointer;
      text-shadow: 0 0 10px rgba(0,245,255,0.4);
      transition: var(--transition);
    }
    .voucher-code:hover { background: rgba(0,245,255,0.14); box-shadow: 0 0 15px rgba(0,245,255,0.2); }
    .copy-hint { font-size: 0.68rem; color: var(--text-muted); margin-top: 3px; text-align: center; }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
      .nav-menu, .nav-right { display: none; }
      .promo-grid { grid-template-columns: 1fr; }
      .cdown-timer { margin-left: 0; }
      .countdown-banner { flex-direction: column; align-items: flex-start; }
      .section-header { flex-direction: column; align-items: flex-start; gap: 10px; }
      .promo-hero-content h1 { font-size: 2rem; }
    }
    @media (max-width: 480px) {
      .filter-inner { justify-content: flex-start; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px; }
      .filter-btn { white-space: nowrap; }
      .promo-hero-content h1 { font-size: 1.6rem; }
      .voucher-row { flex-wrap: wrap; }
    }
  </style>
</head>
<body>

<!-- Particles canvas — sama persis seperti halaman Game -->
<canvas id="bg-canvas"></canvas>

<!-- Topbar -->
<div class="topbar">
  <div class="topbar-inner">
    <span class="topbar-slogan">⚡ Instant Top Up! Instant Play!</span>
    <a href="#" class="btn-outline-sm">Cek Pesanan</a>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar">
  <div class="navbar-inner">
    <a href="#" class="brand">
      <span class="brand-icon">⚡</span>
      <span class="brand-text">TopUp<span class="brand-highlight">Ku</span></span>
    </a>
    <ul class="nav-menu">
      <li><a href="http://localhost/TUGAS-WEB/index.php#" class="nav-link">Game</a></li>
      <li><a href="promo.php" class="nav-link active">Promo</a></li>
      <li><a href="#" class="nav-link">Pesanan</a></li>
      <li><a href="#" class="nav-link">Bantuan</a></li>
    </ul>
    <div class="nav-right">
      <div class="search-wrapper">
        <span class="search-icon">🔍</span>
        <input class="search-input" type="text" placeholder="Cari game..."/>
      </div>
      <button class="btn btn-neon">Masuk</button>
    </div>
  </div>
</nav>

<!-- Hero Promo dengan animasi aurora + shooting stars -->
<section class="promo-hero">
  <div class="shooting-star ss1"></div>
  <div class="shooting-star ss2"></div>
  <div class="shooting-star ss3"></div>
  <div class="shooting-star ss4"></div>
  <div class="promo-hero-content">
    <div class="hero-badge">🔥 PROMO AKTIF</div>
    <h1>Promo &amp; <span class="neon-text">Bonus Terbaik</span></h1>
    <p>Hemat lebih banyak dengan promo eksklusif TopUpKu. Update setiap hari untuk semua game favoritmu!</p>
  </div>
</section>

<!-- Filter Bar -->
<div class="filter-bar">
  <div class="filter-inner">
    <button class="filter-btn active" onclick="setFilter(this)">Semua</button>
    <button class="filter-btn" onclick="setFilter(this)">Mobile Legends</button>
    <button class="filter-btn" onclick="setFilter(this)">Free Fire</button>
    <button class="filter-btn" onclick="setFilter(this)">PUBG Mobile</button>
    <button class="filter-btn" onclick="setFilter(this)">Valorant</button>
    <button class="filter-btn" onclick="setFilter(this)">COD Mobile</button>
    <button class="filter-btn" onclick="setFilter(this)">Voucher</button>
  </div>
</div>

<!-- Main Content -->
<div class="container" style="padding-top: 12px; padding-bottom: 60px;">

  <!-- Flash Sale Countdown -->
  <div class="countdown-banner" style="margin: 32px 0 0;">
    <div class="cdown-icon">⚡</div>
    <div class="cdown-text">
      <h3>Flash Sale Hari Ini!</h3>
      <p>Bonus diamond 30% untuk semua top up — berakhir dalam:</p>
    </div>
    <div class="cdown-timer">
      <div class="time-box"><div class="time-num" id="ch">07</div><div class="time-lbl">Jam</div></div>
      <div class="time-sep">:</div>
      <div class="time-box"><div class="time-num" id="cm">42</div><div class="time-lbl">Menit</div></div>
      <div class="time-sep">:</div>
      <div class="time-box"><div class="time-num" id="cs">18</div><div class="time-lbl">Detik</div></div>
    </div>
  </div>

  <!-- Promo Unggulan -->
  <div class="section-header">
    <div class="section-title">
      <span class="neon-bar"></span>
      🔥 Promo Unggulan
      <span class="section-badge">HOT</span>
    </div>
    <a href="#" class="see-all">Lihat Semua →</a>
  </div>

  <div class="promo-grid">
    <!-- Card 1 -->
    <div class="promo-card">
      <div class="card-thumb card-thumb-mlbb">💎<div class="card-thumb-overlay"></div></div>
      <div class="card-ribbon ribbon-hot">HOT</div>
      <div class="discount-badge">+20%</div>
      <div class="card-body">
        <div class="card-game">Mobile Legends</div>
        <div class="card-title">Bonus Diamond Extra 20%</div>
        <div class="card-desc">Dapatkan bonus 20% diamond setiap top up di atas Rp 50.000. Berlaku untuk semua metode pembayaran.</div>
        <div class="card-foot">
          <div class="card-expiry soon">🕐 Berakhir 3 hari lagi</div>
          <button class="btn-claim">Klaim</button>
        </div>
      </div>
    </div>
    <!-- Card 2 -->
    <div class="promo-card">
      <div class="card-thumb card-thumb-ff">💣<div class="card-thumb-overlay"></div></div>
      <div class="card-ribbon ribbon-promo">PROMO</div>
      <div class="discount-badge">+15%</div>
      <div class="card-body">
        <div class="card-game">Free Fire</div>
        <div class="card-title">Bonus Diamond FF Weekend</div>
        <div class="card-desc">Top up diamond Free Fire di akhir pekan dan dapatkan bonus 15% otomatis. Tidak perlu kode.</div>
        <div class="card-foot">
          <div class="card-expiry">📅 Sabtu – Minggu</div>
          <button class="btn-claim">Klaim</button>
        </div>
      </div>
    </div>
    <!-- Card 3 -->
    <div class="promo-card">
      <div class="card-thumb card-thumb-pubg">🎯<div class="card-thumb-overlay"></div></div>
      <div class="card-ribbon ribbon-new">NEW</div>
      <div class="discount-badge">+10%</div>
      <div class="card-body">
        <div class="card-game">PUBG Mobile</div>
        <div class="card-title">UC Bonus Pengguna Baru</div>
        <div class="card-desc">Member baru TopUpKu mendapatkan bonus 10% UC untuk top up pertama. Daftar sekarang!</div>
        <div class="card-foot">
          <div class="card-expiry">📅 Berlaku 30 hari</div>
          <button class="btn-claim">Klaim</button>
        </div>
      </div>
    </div>
    <!-- Card 4 -->
    <div class="promo-card">
      <div class="card-thumb card-thumb-val">🔫<div class="card-thumb-overlay"></div></div>
      <div class="card-ribbon ribbon-ltd">LIMITED</div>
      <div class="discount-badge">+25%</div>
      <div class="card-body">
        <div class="card-game">Valorant</div>
        <div class="card-title">Double VP Valorant Points</div>
        <div class="card-desc">Beli VP Valorant Rp 100.000+ dan dapatkan 25% VP ekstra langsung ke akun kamu.</div>
        <div class="card-foot">
          <div class="card-expiry soon">🕐 Berakhir 1 hari lagi</div>
          <button class="btn-claim">Klaim</button>
        </div>
      </div>
    </div>
    <!-- Card 5 -->
    <div class="promo-card">
      <div class="card-thumb card-thumb-cod">💥<div class="card-thumb-overlay"></div></div>
      <div class="card-ribbon ribbon-promo">PROMO</div>
      <div class="discount-badge">+12%</div>
      <div class="card-body">
        <div class="card-game">COD Mobile</div>
        <div class="card-title">CP Bonus Paket Bulanan</div>
        <div class="card-desc">Top up CP COD Mobile setiap bulan dan nikmati bonus 12% khusus member aktif.</div>
        <div class="card-foot">
          <div class="card-expiry">📅 Berakhir 14 hari lagi</div>
          <button class="btn-claim">Klaim</button>
        </div>
      </div>
    </div>
    <!-- Card 6 -->
    <div class="promo-card">
      <div class="card-thumb card-thumb-gshin">✨<div class="card-thumb-overlay"></div></div>
      <div class="card-ribbon ribbon-new">NEW</div>
      <div class="discount-badge">+18%</div>
      <div class="card-body">
        <div class="card-game">Genshin Impact</div>
        <div class="card-title">Genesis Crystal Spesial</div>
        <div class="card-desc">Bonus 18% Genesis Crystal untuk setiap top up Genshin Impact di atas Rp 75.000.</div>
        <div class="card-foot">
          <div class="card-expiry">📅 Berakhir 7 hari lagi</div>
          <button class="btn-claim">Klaim</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Voucher Section -->
  <div class="section-header" style="margin-top: 50px;">
    <div class="section-title">
      <span class="neon-bar" style="background:linear-gradient(180deg,var(--neon-cyan),var(--neon-green));"></span>
      🎟️ Kode Voucher
    </div>
    <span class="section-badge" style="background:rgba(0,245,255,0.08);color:var(--neon-cyan);border-color:rgba(0,245,255,0.3);">GRATIS</span>
  </div>

  <div class="voucher-list">
    <div class="voucher-row">
      <div class="voucher-icon vi-green">💚</div>
      <div class="voucher-info">
        <h4>Diskon 10% Semua Game</h4>
        <p>Berlaku untuk semua metode pembayaran · Min. transaksi Rp 20.000</p>
      </div>
      <div class="voucher-code-wrap">
        <div class="voucher-code" onclick="copyCode(this,'TOPUP10')">TOPUP10</div>
        <div class="copy-hint">Klik untuk salin</div>
      </div>
    </div>
    <div class="voucher-row">
      <div class="voucher-icon vi-cyan">💙</div>
      <div class="voucher-info">
        <h4>Cashback Rp 5.000</h4>
        <p>Cashback untuk top up pertama bulan ini · DANA &amp; OVO saja</p>
      </div>
      <div class="voucher-code-wrap">
        <div class="voucher-code" onclick="copyCode(this,'CB5RIBU')">CB5RIBU</div>
        <div class="copy-hint">Klik untuk salin</div>
      </div>
    </div>
    <div class="voucher-row">
      <div class="voucher-icon vi-orange">🧡</div>
      <div class="voucher-info">
        <h4>Gratis Ongkos Admin</h4>
        <p>Nol rupiah biaya admin untuk semua transaksi hari ini</p>
      </div>
      <div class="voucher-code-wrap">
        <div class="voucher-code" onclick="copyCode(this,'NOMIN')">NOMIN</div>
        <div class="copy-hint">Klik untuk salin</div>
      </div>
    </div>
    <div class="voucher-row">
      <div class="voucher-icon vi-purple">💜</div>
      <div class="voucher-info">
        <h4>Bonus 5% Semua E-Wallet</h4>
        <p>Top up pakai GoPay, OVO, Dana, atau ShopeePay dapat bonus 5%</p>
      </div>
      <div class="voucher-code-wrap">
        <div class="voucher-code" onclick="copyCode(this,'EWALLET5')">EWALLET5</div>
        <div class="copy-hint">Klik untuk salin</div>
      </div>
    </div>
  </div>

</div><!-- /container -->

<!-- DB Badge -->
<div class="db-badge">✅ DB: Terhubung</div>

<script>
// ══════════════════════════════════════════════
//  PARTICLES — persis dari main.js initParticles()
// ══════════════════════════════════════════════
(function initParticles() {
  const canvas = document.getElementById('bg-canvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  let w, h, particles = [];
  const COLORS = ['rgba(0,245,255,', 'rgba(191,0,255,', 'rgba(0,255,136,'];
  const particleCount = Math.min(55, Math.floor(window.innerWidth / 22));
  const maxDist = 130;
  let mouse = { x: null, y: null };

  const resize = () => { w = canvas.width = window.innerWidth; h = canvas.height = window.innerHeight; };
  window.addEventListener('resize', resize);
  resize();
  window.addEventListener('mousemove', e => { mouse.x = e.x; mouse.y = e.y; });

  class Particle {
    constructor() { this.reset(); }
    reset() {
      this.x = Math.random() * w;
      this.y = Math.random() * h;
      this.vx = (Math.random() - 0.5) * 0.45;
      this.vy = (Math.random() - 0.5) * 0.45;
      this.size = Math.random() * 1.8 + 0.8;
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
      ctx.fillStyle = this.color + '0.7)';
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
          ctx.strokeStyle = p.color + (0.12 * (1 - dist / maxDist)) + ')';
          ctx.lineWidth = 0.8;
          ctx.stroke();
        }
      }
      if (mouse.x !== null) {
        const dx = p.x - mouse.x, dy = p.y - mouse.y;
        const dist = Math.hypot(dx, dy);
        if (dist < 160) {
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(mouse.x, mouse.y);
          ctx.strokeStyle = p.color + (0.18 * (1 - dist / 160)) + ')';
          ctx.stroke();
        }
      }
    });
    requestAnimationFrame(animate);
  };
  animate();
})();

// ── Filter Tabs ──
function setFilter(el) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
}

// ── Countdown ──
let total = 7 * 3600 + 42 * 60 + 18;
setInterval(() => {
  if (total <= 0) return;
  total--;
  document.getElementById('ch').textContent = String(Math.floor(total / 3600)).padStart(2,'0');
  document.getElementById('cm').textContent = String(Math.floor((total % 3600) / 60)).padStart(2,'0');
  document.getElementById('cs').textContent = String(total % 60).padStart(2,'0');
}, 1000);

// ── Copy Voucher ──
function copyCode(el, code) {
  navigator.clipboard.writeText(code).catch(() => {});
  const orig = el.textContent;
  el.textContent = '✓ Disalin!';
  el.style.color = 'var(--neon-green)';
  el.style.borderColor = 'rgba(0,255,136,0.5)';
  el.style.textShadow = '0 0 12px rgba(0,255,136,0.6)';
  setTimeout(() => {
    el.textContent = orig;
    el.style.color = '';
    el.style.borderColor = '';
    el.style.textShadow = '';
  }, 1800);
}
</script>
</body>
</html>