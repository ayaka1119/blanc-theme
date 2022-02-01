<article class="c-entry" id="post-<?php the_ID(); ?>">
  <div class="c-entry__thumb">
    <a href="<?php the_permalink(); ?>">
      <?php
      if (get_the_post_thumbnail()) {
        the_post_thumbnail('medium');
      } else {
        echo '<img src="' . GET_PATH() . '/no-image.jpg" alt="">';
      }; ?>
    </a>
  </div>
  <div class="c-entry__meta">
    <div class="c-entry__header">
      <div class="c-category-tip __<?php echo $args['term']->slug; ?>">
        <a href="<?php echo get_category_link($args['term']->term_id); ?>"><?php echo $args['term']->name; ?></a>
      </div>
      <div class="c-entry__datetime">
        <time class="c-datetime" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
      </div>
    </div>
    <h2 class="c-entry__title"><a href="<?php echo the_permalink(); ?>"><?php the_title(); ?></a></h2>
  </div>
</article>