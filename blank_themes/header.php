<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="UTF-8">

  <?php /* CSS and JavaScript */ ?>
  <script src="<?php echo GET_PATH('js'); ?>/application.js?v=<?php echo esc_html(date_i18n('Ymd_His')); ?>" defer></script>
  <link rel="stylesheet" href="<?php echo GET_PATH('css'); ?>/style.css?v=<?php echo esc_html(date_i18n('Ymd_His')); ?>">

  <?php /* Base */ ?>
  <?php wp_head(); ?>
  <meta name="description" content="<?php bloginfo('description'); ?>">

  <?php /* Favicon */ ?>
  <link rel="shortcut icon" href="<?php echo GET_PATH(); ?>/favicon.ico">
  <link rel="apple-touch-icon" href="<?php echo GET_PATH(); ?>/apple-touch-icon.png">

  <?php /* Other */ ?>
  <link rel="canonical" href="<?php echo get_pagenum_link(1); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <?php /* noindex */ ?>
  <?php if (is_page('contact-thanks')) : ?>
    <meta name="robots" content="noindex,nofollow">
  <?php endif; ?>
</head>

<body <?php body_class(); ?>>
  <div class="l-wrapper">

    <header class="l-header" id="js-header">
      <div class="l-header__inner">
        <div class="l-header__meta">
          <div class="l-header__logo">
            <a href="<?php echo home_url(); ?>" class="c-logo">
              <img src="<?php echo GET_PATH(); ?>/logo.svg" alt="<?php bloginfo('name'); ?>" width="160" height="37">
            </a>
          </div>
        </div>
        <div class="l-header__nav">
          <div class="c-drawer" id="js-drawer-menu-nav">
            <nav class="c-nav" role="navigation" itemscope itemtype="https://schema.org/SiteNavigationElement">
              <?php wp_nav_menu(
                array(
                  'theme_location' => 'header-menu',
                  'link_before' => '<span itemprop="name">',
                  'link_after' => '</span>'
                )
              ); ?>
            </nav>
            <div class="c-drawer__close-btn">
              <button type="button" class="c-close-btn" id="js-drawer-close-btn" title="メニューを閉じる"></button>
            </div>
          </div>
          <button type="button" class="c-hamburger-menu" id="js-drawer-open-btn" title="メニューを開く">
            <span></span>
          </button>
          <div class="c-cover-bg" id="js-drawer-menu-bg"></div>
          <div class="u-ml20 is-lg-hide">
            <?php get_template_part('parts/input-search', null); ?>
          </div>
        </div>
      </div>
    </header>

    <div class="l-container">