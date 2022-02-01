<div class="c-sidebar">
  <div class="c-sidebar__block">
    <h3 class="c-sidebar__head">人気記事</h3>
    <div class="c-sidebar__body">
      <div class="c-ranking-sidebar c-sidebar__entry-wrap">
        <?php
        $args_ranking = array(
          'post_type' => 'post',
          'post_status' => 'publish',
          'meta_key' => 'post_views_count',
          'orderby' => 'meta_value_num',
          'order' => 'DESC', // ASC
          'posts_per_page' => 5,
        );
        $the_query = new WP_Query($args_ranking);
        if ($the_query->have_posts()) :
          while ($the_query->have_posts()) : $the_query->the_post();
            get_template_part('parts/entry-compact', null);
          endwhile;
        else :
          echo '<p>現在集計中です。</p>';
        endif;
        wp_reset_postdata();
        ?>
      </div>
    </div>
  </div>
  <div class="c-sidebar__block">
    <h3 class="c-sidebar__head">関連記事</h3>
    <p><small class="c-caption">（同カテゴリを持つ記事をランダム表示）</small></p>
    <div class="c-sidebar__body">
      <div class="c-sidebar__entry-wrap">
        <?php
        $args_rand = array(
          'post_type' => 'post',
          'category_name' => $args['term_slug'],
          'post_status' => 'publish',
          'orderby' => 'rand',
          'order' => 'DESC', // ASC
          'posts_per_page' => 5,
        );
        $the_query = new WP_Query($args_rand);
        if ($the_query->have_posts()) :
          while ($the_query->have_posts()) : $the_query->the_post();
            get_template_part('parts/entry-compact', null);
          endwhile;
        else :
          echo '<p>現在集計中です。</p>';
        endif;
        wp_reset_postdata();
        ?>
      </div>
    </div>
  </div>
</div>