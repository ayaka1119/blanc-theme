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

        <div class="p-contact">
          <div class="c-form" id="contact-form">
            <?php
            $get_wpcf7 = get_posts(array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1, 'name' => 'contact-form'))[0];
            echo do_shortcode('[contact-form-7 id="' . $get_wpcf7->ID . '" title="' . $get_wpcf7->post_title . '"]');
            ?>
          </div>
        </div>

      </div>
    </section>

  </div>

</main>

<?php get_footer(); ?>