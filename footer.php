<!-- =========================================================
       FOOTER.PHP — bagian umum (footer, canvas, script)
       Dipakai bersama oleh index.php, bantuan.php, dan halaman lain.
       ========================================================= -->

  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <a href="index.php" class="brand">
          <span class="brand-icon">⚡</span>
          <span class="brand-text">TopUp<span class="brand-highlight">Ku</span></span>
        </a>
        <p>Platform top up game terpercaya,<br>cepat, dan aman 24/7.</p>
      </div>
      <div class="footer-links">
        <h4>Menu</h4>
        <a href="index.php#games">Game</a>
        <a href="promo.php">Promo</a>
        <a href="pesanan.php">Pesanan</a>
        <a href="bantuan.php">Bantuan</a>
      </div>
      <div class="footer-links">
        <h4>Ikuti Kami</h4>
        <a href="https://www.instagram.com/lana_pleaseimprove" target="_blank">Instagram</a>
        <a href="https://wa.me/6281998861649" target="_blank">WhatsApp</a>
        <a href="mailto:emailbisnismu@gmail.com?subject=Tanya%20Seputar%20TopUpKu" target="_blank">Email</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> TopUpKu. All rights reserved.</p>
    </div>
  </footer>

  <canvas id="bg-canvas"></canvas>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="web.js"></script>
</body>
</html>