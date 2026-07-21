<?php
session_start();

// 1. Load koneksi
require 'koneksi.php';

// 2. Ambil data FAQ dari database (kalau tabel faqs tersedia)
$faqs = [];
try {
    $stmt = $pdo->query("SELECT id, category, question, answer FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC");
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback data statis kalau tabel belum ada / query gagal
    $faqs = [
        [
            'category' => 'Umum',
            'question' => 'Apa itu TopUpKu?',
            'answer'   => 'TopUpKu adalah platform top up game dan pulsa terpercaya, cepat, dan aman untuk semua kebutuhan digitalmu.'
        ],
        [
            'category' => 'Umum',
            'question' => 'Berapa lama proses top up?',
            'answer'   => 'Rata-rata transaksi diproses otomatis dalam hitungan detik hingga 5 menit, tergantung metode pembayaran.'
        ],
        [
            'category' => 'Pembayaran',
            'question' => 'Metode pembayaran apa saja yang tersedia?',
            'answer'   => 'Kami mendukung E-Wallet (Dana, OVO, GoPay, ShopeePay), Virtual Account, QRIS, dan pulsa.'
        ],
        [
            'category' => 'Pembayaran',
            'question' => 'Apakah ada biaya tambahan?',
            'answer'   => 'Biaya admin ditampilkan jelas sebelum kamu konfirmasi pembayaran, tidak ada biaya tersembunyi.'
        ],
        [
            'category' => 'Transaksi',
            'question' => 'Transaksi saya gagal tapi saldo terpotong, bagaimana?',
            'answer'   => 'Tenang, dana otomatis akan direfund dalam 1x24 jam. Kalau lebih dari itu, langsung hubungi admin kami.'
        ],
        [
            'category' => 'Transaksi',
            'question' => 'Bagaimana cara cek status transaksi?',
            'answer'   => 'Buka menu Riwayat Transaksi di akunmu, masukkan ID Transaksi atau nomor yang digunakan saat top up.'
        ],
        [
            'category' => 'Akun',
            'question' => 'Apakah harus punya akun untuk top up?',
            'answer'   => 'Tidak wajib, kamu bisa top up sebagai tamu. Tapi dengan akun, riwayat transaksimu tersimpan lebih rapi.'
        ],
    ];
}

$pageTitle = 'Bantuan - TopUpKu';
include 'header.php';
?>

<style>
/* =========================================================
   BANTUAN.PHP — STYLES
   Menggunakan design token yang sudah ada di web.css:
   --bg-body, --bg-card, --bg-nav, --text-primary, --text-muted,
   --neon-cyan, --neon-orange (sesuaikan nama var jika beda di file asli)
   ========================================================= */

/* ---------- Page Banner ---------- */
.page-banner {
    padding: 120px 0 60px;
    text-align: center;
    background:
        radial-gradient(ellipse at top, rgba(0, 229, 255, 0.08), transparent 60%),
        var(--bg-body);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    position: relative;
    overflow: hidden;
}

/* Efek aurora bergerak — dipakai ulang dari animasi hero (web.css) */
.page-banner::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 70% 50%, rgba(120,0,255,0.25) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 30% 60%, rgba(0,80,255,0.18) 0%, transparent 55%),
        radial-gradient(ellipse 40% 40% at 80% 20%, rgba(255,0,180,0.15) 0%, transparent 50%);
    animation: auroraML 6s ease-in-out infinite alternate;
    pointer-events: none;
    z-index: 0;
}
.page-banner::after {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 50% 50% at 20% 80%, rgba(0,200,255,0.12) 0%, transparent 55%),
        radial-gradient(ellipse 70% 40% at 60% 30%, rgba(180,0,255,0.15) 0%, transparent 50%);
    animation: auroraML2 8s ease-in-out infinite alternate;
    pointer-events: none;
    z-index: 0;
}
.page-banner > .container {
    position: relative;
    z-index: 2;
}

/* Efek bintang jatuh — dipakai ulang dari animasi hero (web.css) */
.page-banner .shooting-star {
    position: absolute;
    width: 3px; height: 3px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 6px 2px rgba(180,0,255,0.8), 0 0 20px rgba(180,0,255,0.4);
    z-index: 1;
}
.page-banner .shooting-star::after {
    content: '';
    position: absolute; top: 50%; right: 0;
    transform: translateY(-50%);
    width: 80px; height: 1px;
    background: linear-gradient(90deg, rgba(180,0,255,0.8), transparent);
}
.page-banner .s1 { top: 15%; left: 80%; animation: shoot1 4s linear infinite; }
.page-banner .s2 { top: 35%; left: 90%; animation: shoot1 6s linear infinite 1.5s; }
.page-banner .s3 { top: 55%; left: 75%; animation: shoot1 5s linear infinite 3s; }
.page-banner .s4 { top: 25%; left: 95%; animation: shoot1 7s linear infinite 0.8s; }

.page-banner__title {
    font-family: 'Rajdhani', sans-serif;
    font-weight: 700;
    font-size: clamp(2rem, 5vw, 3rem);
    color: var(--text-primary);
    margin: 16px 0 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.page-banner__subtitle {
    font-family: 'Exo 2', sans-serif;
    color: var(--text-muted);
    max-width: 560px;
    margin: 0 auto;
    font-size: 1rem;
}

.help-search {
    position: relative;
    max-width: 520px;
    margin: 32px auto 0;
}

.help-search__icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--neon-cyan, #00e5ff);
    font-size: 0.95rem;
}

.help-search__input {
    width: 100%;
    padding: 16px 20px 16px 48px;
    background: var(--bg-card);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius, 12px);
    color: var(--text-primary);
    font-family: 'Exo 2', sans-serif;
    font-size: 0.95rem;
    outline: none;
    transition: border-color .25s ease, box-shadow .25s ease;
}

.help-search__input:focus {
    border-color: var(--neon-cyan, #00e5ff);
    box-shadow: 0 0 0 3px rgba(0, 229, 255, 0.12);
}

.help-search__input::placeholder {
    color: var(--text-muted);
}

/* ---------- FAQ Section ---------- */
.faq-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-bottom: 32px;
}

.faq-filter__btn {
    padding: 8px 18px;
    border-radius: 999px;
    background: var(--bg-card);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: var(--text-muted);
    font-family: 'Exo 2', sans-serif;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all .2s ease;
}

.faq-filter__btn:hover {
    color: var(--text-primary);
    border-color: var(--neon-cyan, #00e5ff);
}

.faq-filter__btn.is-active {
    background: var(--neon-cyan, #00e5ff);
    color: #05060a;
    border-color: var(--neon-cyan, #00e5ff);
    font-weight: 600;
}

.faq-list {
    max-width: 760px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.faq-item {
    background: var(--bg-card);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: var(--radius, 12px);
    overflow: hidden;
    transition: border-color .25s ease;
}

.faq-item:hover {
    border-color: rgba(0, 229, 255, 0.25);
}

.faq-item__question {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 22px;
    background: none;
    border: none;
    color: var(--text-primary);
    font-family: 'Exo 2', sans-serif;
    font-weight: 600;
    font-size: 0.98rem;
    text-align: left;
    cursor: pointer;
}

.faq-item__icon {
    flex-shrink: 0;
    color: var(--neon-cyan, #00e5ff);
    transition: transform .25s ease;
}

.faq-item.is-open .faq-item__icon {
    transform: rotate(180deg);
}

.faq-item__answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height .3s ease;
}

.faq-item__answer p {
    padding: 0 22px 20px;
    color: var(--text-muted);
    font-family: 'Exo 2', sans-serif;
    font-size: 0.9rem;
    line-height: 1.6;
    margin: 0;
}

.faq-empty {
    text-align: center;
    color: var(--text-muted);
    padding: 32px 0;
    font-family: 'Exo 2', sans-serif;
}

/* ---------- Cara Top Up ---------- */
.how-to-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

@media (max-width: 900px) {
    .how-to-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 560px) {
    .how-to-grid { grid-template-columns: 1fr; }
}

.how-to-card {
    background: var(--bg-card);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: var(--radius, 12px);
    padding: 28px 22px;
    position: relative;
    transition: transform .25s ease, border-color .25s ease;
}

.how-to-card:hover {
    transform: translateY(-4px);
    border-color: var(--neon-orange, #ff7a00);
}

.how-to-card__number {
    font-family: 'Rajdhani', sans-serif;
    font-weight: 700;
    font-size: 2.2rem;
    background: linear-gradient(135deg, var(--neon-cyan, #00e5ff), var(--neon-orange, #ff7a00));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 12px;
}

.how-to-card__title {
    font-family: 'Rajdhani', sans-serif;
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1.15rem;
    margin: 0 0 8px;
    text-transform: uppercase;
}

.how-to-card__desc {
    font-family: 'Exo 2', sans-serif;
    color: var(--text-muted);
    font-size: 0.88rem;
    line-height: 1.55;
    margin: 0;
}

/* ---------- Hubungi Admin ---------- */
.contact-admin-card {
    background: linear-gradient(135deg, rgba(0, 229, 255, 0.06), rgba(255, 122, 0, 0.06)), var(--bg-card);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius, 16px);
    padding: 44px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 32px;
    align-items: center;
}

@media (max-width: 800px) {
    .contact-admin-card { grid-template-columns: 1fr; padding: 28px; }
}

.contact-admin-card__links {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.contact-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: var(--bg-nav);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: var(--radius, 10px);
    color: var(--text-primary);
    text-decoration: none;
    transition: transform .2s ease, border-color .2s ease;
}

.contact-link:hover {
    transform: translateX(4px);
}

.contact-link i {
    font-size: 1.4rem;
    width: 28px;
    text-align: center;
}

.contact-link span {
    display: flex;
    flex-direction: column;
    font-family: 'Exo 2', sans-serif;
}

.contact-link small {
    color: var(--text-muted);
    font-size: 0.78rem;
}

.contact-link--whatsapp i { color: #25D366; }
.contact-link--whatsapp:hover { border-color: #25D366; }

.contact-link--instagram:hover {
    border: 1px solid transparent;
    background:
        linear-gradient(var(--bg-nav), var(--bg-nav)) padding-box,
        linear-gradient(45deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d, #fcaf45) border-box;
}

.contact-link--instagram i {
    background: linear-gradient(45deg, #405de6, #833ab4, #c13584, #e1306c, #fcaf45);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.contact-link--email i { color: var(--neon-orange, #ff7a00); }
.contact-link--email:hover { border-color: var(--neon-orange, #ff7a00); }

/* ---------- Syarat & Ketentuan ---------- */
.terms-box {
    max-width: 780px;
    margin: 0 auto;
    background: var(--bg-card);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: var(--radius, 12px);
    padding: 32px 36px;
}

.terms-list {
    margin: 0;
    padding-left: 20px;
    color: var(--text-muted);
    font-family: 'Exo 2', sans-serif;
    font-size: 0.92rem;
    line-height: 1.9;
}

.terms-list li::marker {
    color: var(--neon-cyan, #00e5ff);
    font-weight: 700;
}
</style>

<!-- ========================= PAGE BANNER ========================= -->
<section class="page-banner">
    <div class="shooting-star s1"></div>
    <div class="shooting-star s2"></div>
    <div class="shooting-star s3"></div>
    <div class="shooting-star s4"></div>
    <div class="container" data-aos="fade-up">
        <span class="section-badge">
            <i class="fa-solid fa-headset"></i> Pusat Bantuan
        </span>
        <h1 class="page-banner__title">Ada yang bisa kami bantu?</h1>
        <p class="page-banner__subtitle">
            Temukan jawaban seputar top up, pembayaran, dan akun kamu di sini.
        </p>

        <div class="help-search" data-aos="fade-up" data-aos-delay="100">
            <i class="fa-solid fa-magnifying-glass help-search__icon"></i>
            <input
                type="text"
                id="faqSearchInput"
                class="help-search__input"
                placeholder="Cari pertanyaan... misal: refund, metode pembayaran"
                autocomplete="off"
            >
        </div>
    </div>
</section>

<!-- ========================= FAQ SECTION ========================= -->
<section class="section faq-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge"><i class="fa-solid fa-circle-question"></i> FAQ</span>
            <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
            <p class="section-subtitle">Klik pertanyaan untuk melihat jawaban lengkapnya</p>
        </div>

        <div class="faq-filter" data-aos="fade-up" data-aos-delay="100">
            <button type="button" class="faq-filter__btn is-active" data-filter="all">Semua</button>
            <?php
            $categories = array_unique(array_column($faqs, 'category'));
            foreach ($categories as $cat):
            ?>
                <button type="button" class="faq-filter__btn" data-filter="<?= htmlspecialchars($cat) ?>">
                    <?= htmlspecialchars($cat) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="faq-list" id="faqList" data-aos="fade-up" data-aos-delay="150">
            <?php foreach ($faqs as $i => $faq): ?>
                <div class="faq-item" data-category="<?= htmlspecialchars($faq['category']) ?>">
                    <button type="button" class="faq-item__question" aria-expanded="false">
                        <span><?= htmlspecialchars($faq['question']) ?></span>
                        <i class="fa-solid fa-chevron-down faq-item__icon"></i>
                    </button>
                    <div class="faq-item__answer">
                        <p><?= htmlspecialchars($faq['answer']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <p class="faq-empty" id="faqEmpty" style="display:none;">
                Tidak ada pertanyaan yang cocok dengan pencarianmu.
            </p>
        </div>
    </div>
</section>

<!-- ========================= CARA TOP UP ========================= -->
<section class="section how-to-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge"><i class="fa-solid fa-bolt"></i> Panduan</span>
            <h2 class="section-title">Cara Top Up di TopUpKu</h2>
            <p class="section-subtitle">Cuma 4 langkah, prosesnya cepat dan gampang</p>
        </div>

        <div class="how-to-grid">
            <div class="how-to-card" data-aos="fade-up" data-aos-delay="0">
                <div class="how-to-card__number">01</div>
                <h3 class="how-to-card__title">Pilih Produk</h3>
                <p class="how-to-card__desc">Pilih game atau layanan yang ingin kamu top up dari daftar produk kami.</p>
            </div>
            <div class="how-to-card" data-aos="fade-up" data-aos-delay="100">
                <div class="how-to-card__number">02</div>
                <h3 class="how-to-card__title">Masukkan ID</h3>
                <p class="how-to-card__desc">Isi User ID / Server sesuai akun game kamu dengan teliti.</p>
            </div>
            <div class="how-to-card" data-aos="fade-up" data-aos-delay="200">
                <div class="how-to-card__number">03</div>
                <h3 class="how-to-card__title">Pilih Nominal & Bayar</h3>
                <p class="how-to-card__desc">Pilih nominal top up, lalu selesaikan pembayaran lewat metode favoritmu.</p>
            </div>
            <div class="how-to-card" data-aos="fade-up" data-aos-delay="300">
                <div class="how-to-card__number">04</div>
                <h3 class="how-to-card__title">Selesai!</h3>
                <p class="how-to-card__desc">Item otomatis masuk ke akun game kamu dalam hitungan detik.</p>
            </div>
        </div>
    </div>
</section>

<!-- ========================= HUBUNGI ADMIN ========================= -->
<section class="section contact-admin-section">
    <div class="container">
        <div class="contact-admin-card" data-aos="zoom-in">
            <div class="contact-admin-card__text">
                <span class="section-badge"><i class="fa-solid fa-comments"></i> Butuh Bantuan Lebih?</span>
                <h2 class="section-title">Hubungi Admin Kami</h2>
                <p class="section-subtitle">
                    Tim support kami siap bantu 24/7 lewat channel berikut ini.
                </p>
            </div>
            <div class="contact-admin-card__links">
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="contact-link contact-link--whatsapp">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>
                        <strong>WhatsApp</strong>
                        <small>Respon cepat, 24 jam</small>
                    </span>
                </a>
                <a href="https://www.instagram.com/lana_pleaseimprove" target="_blank" rel="noopener" class="contact-link contact-link--instagram">
                    <i class="fa-brands fa-instagram"></i>
                    <span>
                        <strong>Instagram</strong>
                        <small>@lana_pleaseimprove</small>
                    </span>
                </a>
                <a href="mailto:support@topupku.com" class="contact-link contact-link--email">
                    <i class="fa-solid fa-envelope"></i>
                    <span>
                        <strong>Email</strong>
                        <small>support@topupku.com</small>
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ========================= SYARAT & KETENTUAN ========================= -->
<section class="section terms-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge"><i class="fa-solid fa-file-shield"></i> Legal</span>
            <h2 class="section-title">Syarat &amp; Ketentuan</h2>
            <p class="section-subtitle">Harap dibaca sebelum melakukan transaksi</p>
        </div>

        <div class="terms-box" data-aos="fade-up" data-aos-delay="100">
            <ol class="terms-list">
                <li>Pastikan data User ID / Server yang dimasukkan sudah benar. Kesalahan input menjadi tanggung jawab pengguna.</li>
                <li>Transaksi yang sudah diproses dan berhasil tidak dapat dibatalkan atau di-refund.</li>
                <li>TopUpKu tidak bertanggung jawab atas kendala yang disebabkan oleh pihak penyedia game/provider.</li>
                <li>Jika transaksi gagal namun saldo terpotong, dana akan dikembalikan otomatis dalam 1x24 jam.</li>
                <li>Segala bentuk penyalahgunaan sistem (abuse) akan dikenakan sanksi pemblokiran akun.</li>
                <li>TopUpKu berhak mengubah syarat dan ketentuan sewaktu-waktu tanpa pemberitahuan sebelumnya.</li>
            </ol>
        </div>
    </div>
</section>

<script>
/* =========================================================
   BANTUAN.PHP — SCRIPT
   Accordion FAQ, filter kategori, dan search FAQ.
   ========================================================= */
(function () {
    const faqList  = document.getElementById('faqList');
    if (!faqList) return; // Hanya jalan kalau ada di halaman bantuan.php

    const faqItems    = Array.from(faqList.querySelectorAll('.faq-item'));
    const filterBtns  = document.querySelectorAll('.faq-filter__btn');
    const searchInput = document.getElementById('faqSearchInput');
    const emptyState  = document.getElementById('faqEmpty');

    let activeFilter = 'all';
    let searchTerm   = '';

    // ── Accordion toggle ──────────────────────────────────
    faqItems.forEach((item) => {
        const question = item.querySelector('.faq-item__question');
        const answer   = item.querySelector('.faq-item__answer');

        question.addEventListener('click', () => {
            const isOpen = item.classList.contains('is-open');

            // Tutup item lain (accordion mode single-open)
            faqItems.forEach((other) => {
                if (other !== item) {
                    other.classList.remove('is-open');
                    other.querySelector('.faq-item__answer').style.maxHeight = null;
                    other.querySelector('.faq-item__question').setAttribute('aria-expanded', 'false');
                }
            });

            if (isOpen) {
                item.classList.remove('is-open');
                answer.style.maxHeight = null;
                question.setAttribute('aria-expanded', 'false');
            } else {
                item.classList.add('is-open');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                question.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // ── Filter kategori ────────────────────────────────────
    filterBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            filterBtns.forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            activeFilter = btn.dataset.filter;
            applyFilters();
        });
    });

    // ── Search FAQ ─────────────────────────────────────────
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchTerm = e.target.value.trim().toLowerCase();
            applyFilters();
        });
    }

    function applyFilters() {
        let visibleCount = 0;

        faqItems.forEach((item) => {
            const category = item.dataset.category;
            const text = item.querySelector('.faq-item__question span').textContent.toLowerCase();

            const matchesFilter = activeFilter === 'all' || category === activeFilter;
            const matchesSearch = searchTerm === '' || text.includes(searchTerm);
            const visible = matchesFilter && matchesSearch;

            item.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }
})();
</script>

<?php include 'footer.php'; ?>