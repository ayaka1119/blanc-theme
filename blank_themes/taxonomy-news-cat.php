<?php get_header(); ?>

<main class="l-main l-under" role="main">

  <div class="l-under__breadcrumb">
    <?php create_breadcrumb(); ?>
  </div>
  <div class="l-under__header">
    <h1 class="c-title--center"><?php echo single_term_title(); ?></h1>
  </div>

  <div class="l-under__content">

    <section class="l-section l-outer">
      <div class="l-section__inner l-inner">
        <div class="p-entries">
          <div class="p-entries__lists">
            <?php if (have_posts()) : ?>
              <?php while (have_posts()) : the_post(); ?>
                <?php
                $args = [
                  'term' => get_queried_object()
                ];
                get_template_part('parts/entry', null, $args);
                ?>
              <?php endwhile; ?>
            <?php endif; ?>
          </div>
          <?php
          // デフォルトだとスクリーンリーダー用の要素が出力されるから、preg_replace()で消す
          $pagination = preg_replace(
            '/\<h2 class=\"screen-reader-text\"\>(.*?)\<\/h2\>/',
            '',
            get_the_posts_pagination(array(
              'mid_size' => 2, // 現在のページの左右に表示するページ番号の数
              'prev_text' => '',
              'next_text' => ''
            ))
          );
          if ($pagination) {
            echo '<div class="p-entries__pagination">';
            echo '  <div class="c-pagination">' . $pagination . '</div>';
            echo '</div>';
          }
          ?>
        </div>
      </div>
    </section>

  </div>

</main>

<?php get_footer(); ?>