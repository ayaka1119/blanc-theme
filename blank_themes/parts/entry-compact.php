<article class="c-entry-compact" id="post-<?php the_ID(); ?>">
  <a href="<?php echo the_permalink(); ?>" class="c-entry-compact__link">
    <div class="c-entry-compact__thumb">
      <?php
      if (get_the_post_thumbnail()) {
        the_post_thumbnail('thumbnail');
      } else {
        echo '<img src="' . GET_PATH() . '/no-image.jpg" alt="">';
      }; ?>
    </div>
    <div class="c-entry-compact__meta">
      <h4 class="c-entry-compact__title"><?php the_title(); ?></h4>
    </div>
  </a>
</article>