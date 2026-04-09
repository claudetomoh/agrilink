  <!-- Footer Scripts -->
  <script src="<?= APP_URL ?>/js/app.js"></script>
  <?php if (!empty($extraJS)) foreach ($extraJS as $js): ?>
  <script src="<?= htmlspecialchars($js) ?>"></script>
  <?php endforeach; ?>
</body>
</html>
