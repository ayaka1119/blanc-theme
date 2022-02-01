<?php
/*
    Template Name: 静的ページ テンプレート
  */
?>

<?php get_header(); ?>

<main class="l-main l-under" role="main">

  <div class="l-under__breadcrumb">
    <?php create_breadcrumb(); ?>
  </div>
  <div class="l-under__header">
    <h1 class="c-title--center"><?php the_title(); ?></h1>
  </div>

  <div class="l-under__content">

    <section class="l-section l-outer">
      <div class="l-section__inner l-inner">

        <div class="c-tag-style">
          <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
              <?php the_content(); ?>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>

      </div>
    </section>

  </div>

</main>

<?php get_footer(); ?>