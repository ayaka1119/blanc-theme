</div>

<footer class="p-footer">
  <div class="p-footer__copyright">
    <small>&copy; <?php echo date_i18n(__('Y', 'blankslate')); ?> <?php echo get_bloginfo('name'); ?></small>
  </div>
</footer>

</div>
<?php wp_footer(); ?>

<?php if (is_page('contact')) : ?>
  <script>
    document.addEventListener('wpcf7mailsent', function() {
      location = './thanks/';
    }, false);
  </script>
<?php endif; ?>

</body>

</html>