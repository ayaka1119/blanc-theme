<?php

/**
 * ************************************************************************
 *  グローバル変数 / 関数
 * ************************************************************************
 */

// パス
$WP_ROOT_PATH = get_stylesheet_directory_uri();
$WP_IMG_PATH = esc_html($WP_ROOT_PATH . '/assets/img');
$WP_CSS_PATH = esc_html($WP_ROOT_PATH . '/assets/css');
$WP_JS_PATH = esc_html($WP_ROOT_PATH . '/assets/js');
function GET_PATH(string $_type = 'img')
{
  global $WP_IMG_PATH;
  global $WP_CSS_PATH;
  global $WP_JS_PATH;
  switch ($_type) {
    case 'img':
      return $WP_IMG_PATH;
      break;
    case 'css':
      return $WP_CSS_PATH;
      break;
    case 'js':
      return $WP_JS_PATH;
      break;
    default:
      return '';
      break;
  }
}

// メインループの表示件数制御
$WP_ROOP_VIEW_ARCHIVE = 3;
$WP_ROOP_VIEW_TAX = 3;

// OGP用
$FACEBOOK_APP_ID = '';
$TWITTER_ACCOUNT_ID = '';

/**
 * ************************************************************************
 *  初期化
 * ************************************************************************
 */

add_action('after_setup_theme', 'my_after_setup');
function my_after_setup()
{
  // 翻訳ファイルの場所を指定
  load_theme_textdomain('blankslate', get_template_directory() . '/languages');
  // 管理画面の設定ページで設定したタイトルを<title>に使用する
  add_theme_support('title-tag');
  // 投稿でサムネイルを有効にする
  add_theme_support('post-thumbnails');
  // YouTubeなどの埋め込みコンテンツをレスポンシブ対応にする
  add_theme_support('responsive-embeds');
  // 投稿とコメントのRSSフィードのリンクを有効にする
  add_theme_support('automatic-feed-links');
  // html5で出力する
  add_theme_support('html5', array(
    'comment-list',
    'comment-form',
    'search-form',
    'gallery',
    'caption',
  ));
  // ナビゲーション
  register_nav_menus(array('header-menu' => esc_html__('ヘッダーメニュー', 'blankslate')));
}

/**
 * 投稿者一覧ページを自動で生成されないようにする
 */
add_filter('author_rewrite_rules', '__return_empty_array');

/**
 * /?author=1 などでアクセスしたらリダイレクトさせる
 * @see https://www.webdesignleaves.com/pr/wp/wp_user_enumeration.html
 */
if (!is_admin()) {
  // default URL format
  if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) die();
  add_filter('redirect_canonical', 'my_shapespace_check_enum', 10, 2);
}
function my_shapespace_check_enum($redirect, $request) {
  // permalink URL format
  if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) die();
  else return $redirect;
}

/**
 * WP REST API を無効にする（必要に応じて一部プラグインのみ有効にさせる）
 * @see https://www.webdesignleaves.com/pr/wp/wp_user_enumeration.html
 */
add_filter('rest_pre_dispatch', 'deny_rest_api_except_permitted', 10, 3);
function deny_rest_api_except_permitted($result, $wp_rest_server, $request)
{
  // permit oembed, Contact Form 7, Akismet
  // $permitted_routes に有効にしたいプラグインを指定
  $permitted_routes = ['oembed', 'contact-form-7', 'akismet'];
  $route = $request->get_route();
  foreach ($permitted_routes as $r) {
    if (strpos($route, "/$r/") === 0) return $result;
  }
  // permit Gutenberg（ユーザーが投稿やページの編集が可能な場合）
  if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
    return $result;
  }
  return new WP_Error('rest_disabled', __('The REST API on this site has been disabled.'), array('status' => rest_authorization_required_code()));
}

/**
 * ************************************************************************
 *  <head>タグに関する処理
 * ************************************************************************
 */

/**
 * <title>の区切り文字を変更
 */
add_filter('document_title_separator', 'my_document_title_separator');
function my_document_title_separator($sep)
{
  $sep = '|';
  return $sep;
}

/**
 * <title>のテキストの形式を変える
 */
add_filter('document_title_parts', 'my_document_title_parts', 10, 1);
function my_document_title_parts($title)
{
  if (is_home() || is_front_page()) {
    unset($title['tagline']);
  } else if (is_category()) {
    $title['title'] = '「' . single_term_title('', false) . '」カテゴリー一覧';
  } else if (is_tax()) {
    $title['title'] = '「' . single_term_title('', false) . '」カテゴリー一覧';
  } else if (is_tag()) {
    $title['title'] = '「' . single_term_title('', false) . '」タグ一覧';
  }
  return $title;
}

/**
 * wp_head()で出力されるタグの内、不要なものを削除
 */
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'wp_print_styles', 8);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
add_filter('wp_resource_hints', 'remove_dns_prefetch', 10, 2);
function remove_dns_prefetch($hints, $relation_type)
{
  if ('dns-prefetch' === $relation_type) {
    return array_diff(wp_dependencies_unique_hosts(), $hints);
  }
  return $hints;
}
add_action('wp_enqueue_scripts', 'my_dequeue_plugins_style', 9999);
function my_dequeue_plugins_style()
{
  wp_dequeue_style('wp-block-library');
}

/**
 * OGP関係のタグを出力
 * @see https://saruwakakun.com/html-css/wordpress/ogp
 */
add_action('wp_head', 'my_add_meta_ogp');
function my_add_meta_ogp()
{
  if (is_front_page() || is_home() || is_singular()) {
    global $WP_IMG_PATH;
    global $FACEBOOK_APP_ID;
    global $TWITTER_ACCOUNT_ID;
    global $post;
    $ogp_title = '';
    $ogp_descr = '';
    $ogp_url = '';
    $ogp_img = '';
    $insert = '';

    if (is_singular() && !is_page()) {
      setup_postdata($post);
      $ogp_title = $post->post_title;
      $ogp_descr = mb_substr(get_the_excerpt(), 0, 100);
      $ogp_url = get_permalink();
      wp_reset_postdata();
    } else {
      $ogp_title = get_bloginfo('name');
      $ogp_descr = get_bloginfo('description');
      $ogp_url = home_url();
    }

    // og:type
    $ogp_type = (is_front_page() || is_home()) ? 'website' : 'article';

    // og:image
    if (is_singular() && has_post_thumbnail()) {
      $ps_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
      $ogp_img = $ps_thumb[0];
    } else {
      $ogp_img = $WP_IMG_PATH . '/ogp.jpg';
    }

    // タグ出力
    $insert .= '<meta property="og:title" content="' . esc_attr($ogp_title) . '">' . "\n";
    $insert .= '<meta property="og:description" content="' . esc_attr($ogp_descr) . '">' . "\n";
    $insert .= '<meta property="og:type" content="' . $ogp_type . '">' . "\n";
    $insert .= '<meta property="og:url" content="' . esc_url($ogp_url) . '">' . "\n";
    $insert .= '<meta property="og:image" content="' . esc_url($ogp_img) . '">' . "\n";
    $insert .= '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    $insert .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $insert .= '<meta name="twitter:site" content="' . $TWITTER_ACCOUNT_ID . '">' . "\n";
    $insert .= '<meta property="og:locale" content="ja_JP">' . "\n";
    $insert .= '<meta property="fb:app_id" content="' . $FACEBOOK_APP_ID . '">' . "\n";
    echo $insert;
  }
}

/**
 * ************************************************************************
 *  管理画面に対する処理
 * ************************************************************************
 */

/**
 * 管理画面全体にCSS適用
 */
add_action('admin_enqueue_scripts', 'my_add_admin_style');
function my_add_admin_style()
{
  global $WP_CSS_PATH;
  wp_enqueue_style('my_add_admin_style', $WP_CSS_PATH . '/style-admin.css');
}

/**
 * ビジュアルエディタにCSS適用
 */
add_action('admin_init', 'my_add_editor_style');
function my_add_editor_style()
{
  global $WP_CSS_PATH;
  add_editor_style(str_replace('/' . get_stylesheet_directory_uri(), '', $WP_CSS_PATH) . '/style-editor.css');
}

/**
 * 管理画面全体にjs適用
 */
add_action('admin_enqueue_scripts', 'my_add_admin_js');
function my_add_admin_js($hook)
{
  global $WP_JS_PATH;
  wp_enqueue_script('my_admin_script', $WP_JS_PATH . '/admin.js');
}

/**
 * 不要なメニューを非表示
 * （コメントアウトした行のメニューは表示される）
 */
add_action('admin_menu', 'my_add_remove_admin_menus');
function my_add_remove_admin_menus()
{
  global $menu;
  unset($menu[2]);  // ダッシュボード
  unset($menu[4]);  // メニューの線1
  // unset($menu[5]);  // 投稿
  // unset($menu[10]); // メディア
  // unset($menu[15]); // リンク
  // unset($menu[20]); // ページ
  unset($menu[25]); // コメント
  unset($menu[59]); // メニューの線2
  // unset($menu[60]); // テーマ
  // unset($menu[65]); // プラグイン
  // unset($menu[70]); // プロフィール
  // unset($menu[75]); // ツール
  // unset($menu[80]); // 設定
  unset($menu[90]); // メニューの線3
}

/**
 * 投稿の自動整形を無効（ダブルクオーテーションなど）
 */
add_filter('run_wptexturize', '__return_false');

/**
 * 設定ページ生成
 * （関連ファイル：admin-theme-setting-page.php）
 * ↑で<input>を追加したら、↓を実行する
 * register_setting('custom-menu-group', '↑で定義した<input>のname値');
 *
 * 画像アップローダーについて
 * @see https://nelog.jp/media-uploader-javascript-api
 * admin-theme-setting-page.phpにて以下を実行
 * 1. <input>を追加
 * 2. ファイル下部で new_wp_uploader('<input>のid') を実行（ボタンなどの属性値も変える）
 */
add_action('admin_menu', 'my_add_admin_setting_page');
function my_add_admin_setting_page()
{
  add_menu_page('テーマ設定', 'テーマ設定', 'manage_options', 'custom-setting', 'my_setting_file_path', 'dashicons-admin-generic', 90);
  add_action('admin_init', 'my_register_setting');
}
function my_setting_file_path()
{
  $return_url = '../wp-content/themes/blank_themes/admin-theme-setting-page.php';
  require $return_url;
}
function my_register_setting()
{
  register_setting('custom-menu-group', 'general');
  // register_setting('custom-menu-group', '2つ目のname値');
  // register_setting('custom-menu-group', '3つ目のname値');
}
// メディアアップローダーAPIを管理画面へ読み込ませる
add_action('admin_print_scripts', 'my_add_setting_media_api_scripts');
function my_add_setting_media_api_scripts()
{
  wp_enqueue_media();
}


/**
 * ************************************************************************
 *  ユーザー側画面に対する処理
 * ************************************************************************
 */

/**
 * ツールバー非表示
 */
add_filter('show_admin_bar', '__return_false');

/**
 * ループの表示件数制御
 */
add_action('pre_get_posts', 'my_pre_get_posts');
function my_pre_get_posts($query)
{
  global $WP_ROOP_VIEW_ARCHIVE;
  global $WP_ROOP_VIEW_TAX;
  if (is_admin() || !$query->is_main_query()) return;

  // 表示件数を制御
  $query->set('posts_per_page', $WP_ROOP_VIEW_ARCHIVE);

  // ページごとに件数を変える場合は以下のように条件分岐する
  // if ($query->is_archive()) {
  //   $query->set('posts_per_page', $WP_ROOP_VIEW_ARCHIVE);
  //   return;
  // }
  // if ($query->is_post_type_archive()) {
  //   $query->set('posts_per_page', $WP_ROOP_VIEW_ARCHIVE);
  //   return;
  // }
  // if ($query->is_tax()) {
  //   $query->set('posts_per_page', $WP_ROOP_VIEW_TAX);
  //   return;
  // }
}

/**
 * 投稿アーカイブページの作成
 */
add_filter('register_post_type_args', 'my_post_has_archive', 10, 2);
function my_post_has_archive( $args, $post_type ) {
	if ('post' === $post_type) {
		$args['rewrite'] = true;
		$args['has_archive'] = 'posts'; // スラッグを指定（これがURLになる）
	}
	return $args;
}

/**
 * 検索結果ファイルを使い分ける（カスタム投稿newsならsearch-news.phpを作る）
 */
add_filter('template_include','my_search_template');
function my_search_template($template){
  if (is_search()){
    $post_types = get_query_var('post_type');
    foreach((array) $post_types as $post_type)
      $templates[] = "search-{$post_type}.php";
      $templates[] = 'search.php';
      $template = get_query_template('search', $templates);
    }
  return $template;
}


/**
 * ************************************************************************
 *  パンくず生成関数
 *  @see https://www.webdesignleaves.com/pr/wp/wp_breadcrumbs.html
 * ************************************************************************
 */

function create_breadcrumb($args = array())
{
  global $post;
  // デフォルトの値
  $defaults = array(
    'nav_div' => 'nav',
    'aria_label' => '',
    'id' => '',
    'nav_div_class' => 'c-breadcrumb',
    'ul_class' => 'c-breadcrumb__lists',
    'li_class' => '',
    'li_active_class' => '',
    'aria_current' => '',
    'show_home' => true,
    'show_current' => true,
    'home' => 'TOP',
    'blog_home' => 'TOP',
    'search' => 'で検索した結果',
    'tag' => 'タグ : ',
    'author' => '投稿者',
    'notfound' => '404 Not found',
    // 'separator' => "\n" . '<li class="separator">&nbsp;&gt;&nbsp;</li>' . "\n",
    'separator' => "",
    'cat_off' => false,
    'cat_parents_off' => false,
    'tax_off' => false,
    'tax_parents_off' => false,
    'show_cpta' => true,
    'show_cat_tag_for_cpt' => false,
  );
  //引数の値とデフォルトをマージ
  $args = wp_parse_args($args, $defaults);
  //マージした値を変数として抽出
  extract($args, EXTR_SKIP);

  //マージした値を元に出力するかどうかを設定
  $aria_label = $aria_label ? ' aria-label="' . $aria_label . '" ' : '';
  $id = $id ? ' id="' . $id . '" ' : '';
  $nav_div_class = $nav_div_class ? ' class="' . $nav_div_class . '" ' : '';
  $ul_class = $ul_class ? ' class="' . $ul_class . '" ' : '';
  $li_class = $li_class ? ' class="' . $li_class . '" ' : '';
  $li_active_class = $li_active_class ? ' class="' . $li_active_class . '" ' : '';
  $aria_current = $aria_current ? ' aria-current="' . $aria_current . '"' : '';

  //パンくずリストのマークアップ文字列の初期化
  $str = '';

  //ホーム・フロントページの場合  
  if (is_front_page() || is_home()) {
    if ($show_home) {
      $label = is_front_page() ? $home : $blog_home;
      echo  '<' . $nav_div . $id . $nav_div_class . $aria_label . '><ul' . $ul_class . '><li' . $li_active_class . $aria_current . '>' . $label . '</li></ul></' . $nav_div . '>';
    }
  }
  //ホーム・フロントページでない場合（且つ管理ページでない場合）
  if (!is_front_page() && !is_home() && !is_admin()) {
    //ホームへのリンクを含むリストを生成
    $str .= '<' . $nav_div . $id . $nav_div_class . $aria_label . '>' . "\n";
    $str .= '<ul' . $ul_class . '>' . "\n";
    $str .= '<li' . $li_class . '><a href="' . home_url() . '/">' . $home . '</a></li>';
    //$wp_query の query_vars から get_query_var() でクエリ変数の値を取得
    //タクソノミー名を取得（タクソノミーアーカイブの場合のみ取得可能）
    $my_taxonomy = get_query_var('taxonomy');
    //投稿タイプ名を取得（カスタム投稿タイプ個別ページの場合のみ取得可能）
    $cpt = get_query_var('post_type');
    //カスタムタクソノミーアーカイブページ
    //タクソノミー名が取得できて且つカスタムタクソノミーアーカイブページの場合
    if ($my_taxonomy &&  is_tax($my_taxonomy)) {
      //タームオブジェクト（現在のページのオブジェクト）を取得
      $my_term = get_queried_object();
      //タクソノミーの object_type プロパティは配列
      $post_types = get_taxonomy($my_taxonomy)->object_type;
      //配列の0番目からカスタム投稿タイプのスラッグ（カスタム投稿タイプ名）を取得
      $cpt = $post_types[0];
      //get_post_type_archive_link()：指定した投稿タイプのアーカイブページのリンク
      //get_post_type_object($cpt)->label：指定した投稿タイプのオブジェクトのラベル（名前）
      //カスタム投稿のアーカイブページへのリンクを追加
      $str .= $separator;
      $str .= '<li' . $li_class . '><a href="' . esc_url(get_post_type_archive_link($cpt)) . '">' . get_post_type_object($cpt)->label . '</a></li>';
      //タームオブジェクトに親があればそれらを取得してリンクを生成してリストに追加
      if ($my_term->parent != 0) {
        //祖先タームオブジェクトの ID の配列を取得し逆順に（取得される配列の並びは階層の下から上）
        $ancestors = array_reverse(get_ancestors($my_term->term_id, $my_term->taxonomy));
        //全ての祖先タームオブジェクトのアーカイブページへのリンクを生成してリストに追加
        foreach ($ancestors as $ancestor) {
          $str .= $separator;
          $str .= '<li' . $li_class . '><a href="' . esc_url(get_term_link($ancestor, $my_term->taxonomy)) . '">' . get_term($ancestor, $my_term->taxonomy)->name . '</a></li>';
        }
      }
      //ターム名を追加 
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>' . $my_term->name . '</li>';
      //カテゴリーのアーカイブページ
    } elseif (is_category()) {
      //カテゴリーオブジェクトを取得
      $cat = get_queried_object();
      //取得したカテゴリーオブジェクトに親があればそれらを取得してリンクを生成してリストに追加
      if ($cat->parent != 0) {
        $ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));
        foreach ($ancestors as $ancestor) {
          $str .= $separator;
          $str .= '<li' . $li_class . '><a href="' . esc_url(get_category_link($ancestor)) . '">' . get_cat_name($ancestor) . '</a></li>';
        }
      }
      //カテゴリー名を追加
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>' . $cat->name . '</li>';
      //カスタム投稿のアーカイブページ
    } elseif (is_post_type_archive()) {
      //カスタム投稿タイプ名を取得
      $cpt = get_query_var('post_type');
      //カスタム投稿タイプ名を追加
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>' . get_post_type_object($cpt)->label . '</li>';
      //カスタム投稿タイプの個別記事ページ
    } elseif ($cpt && is_singular($cpt)) {
      if ($show_cpta) {
        //カスタム投稿タイプアーカイブページへのリンクを生成してリストに追加
        $str .= $separator;
        $str .= '<li' . $li_class . '><a href="' . esc_url(get_post_type_archive_link($cpt)) . '">' . get_post_type_object($cpt)->label . '</a></li>';
      }
      //このカスタム投稿タイプに登録されている全てのタクソノミーオブジェクトの名前を取得
      $taxes = get_object_taxonomies($cpt);
      //タクソノミーオブジェクトの名前が取得できれば
      if (count($taxes) !== 0) {
        //タクソノミーを表示する場合
        if (!$tax_off) {
          //配列の先頭のタクソノミーオブジェクトの名前（複数ある可能性があるので先頭のものを使う）
          //デフォルトでは標準のカテゴリーやタグが追加されている場合はインデックスを変更 
          //但し、show_cat_tag_for_cpt が true の場合はカテゴリーを取得可能に
          $tax_index = 0;
          if (!$show_cat_tag_for_cpt) {
            for ($i = 0; $i < count($taxes); $i++) {
              if ($taxes[$i] !== 'category' && $taxes[$i] !== 'post_tag' && $taxes[$i] !== 'post_format') {
                $tax_index = $i;
                break;
              }
            }
          }
          $mytax = $taxes[$tax_index] ? $taxes[$tax_index] : null;
          //カスタムフィールドに優先するタクソノミーのラベルが記載されていればそのタクソノミーを選択
          //タクソノミーのラベルを取得
          $my_pref_tax_label = get_post_meta(get_the_ID(), 'my_pref_tax', true) ? esc_attr(get_post_meta(get_the_ID(), 'my_pref_tax', true)) : null;
          //ラベルからタクソノミーを取得（戻り値はタクソノミーの名前の配列）
          $my_pref_tax_name = get_taxonomies(array('label' => $my_pref_tax_label));
          //タクソノミー名の初期化
          $my_pref_tax = '';
          //取得した配列が1つの場合、その値が優先されるタクソノミーの名前
          if (count($my_pref_tax_name) == 1) {
            $my_pref_tax = $my_pref_tax_name[key($my_pref_tax_name)];
          }
          //タクソノミーの名前が取得できて且つそのタクソノミーが現在の投稿タイプに属している場合は、そのタクソノミーを使用
          if ($my_pref_tax && is_object_in_taxonomy($post->post_type, $my_pref_tax)) {
            $mytax = $my_pref_tax;
          }
          //投稿に割り当てられたタームオブジェクト（配列）を取得
          $terms = get_the_terms($post->ID, $mytax);
          //カスタムフィールドに優先するタームが記載されていればその値を取得して $myterm へ
          $myterm = get_post_meta(get_the_ID(), 'myterm', true) ? esc_attr(get_post_meta(get_the_ID(), 'myterm', true)) : null;
          //$terms が取得できていれば一番下の階層のタームを取得（できない場合は null に）  
          $my_term = $terms ? get_deepest_term($terms, $mytax, $myterm) : null;
          //タームが取得できていれば
          if (!empty($my_term)) {
            //$tax_parents_off がfalse（初期値）でタームに親があればそれらを取得してリンクを生成してリストに追加
            if ($my_term->parent != 0 && !$tax_parents_off) {
              $ancestors = array_reverse(get_ancestors($my_term->term_id, $mytax));
              foreach ($ancestors as $ancestor) {
                $str .= $separator;
                $str .= '<li' . $li_class . '><a href="' . esc_url(get_term_link($ancestor, $mytax)) . '">' . get_term($ancestor, $mytax)->name . '</a></li>';
              }
            }
            //タームのリンクを追加
            $str .= $separator;
            $str .= '<li' . $li_class . '><a href="' . esc_url(get_term_link($my_term, $mytax)) . '">' . $my_term->name . '</a></li>';
          }
        }
      }
      if ($show_current) {
        $str .= $separator;
        //$post->post_title には HTML タグが入っている可能性があるのでタグを除去
        //wp_strip_all_tags() の代わりに PHP の strip_tags() でも
        $str .= '<li' . $li_active_class . $aria_current . '>' . wp_strip_all_tags($post->post_title) . '</li>';
      }
      //個別投稿ページ（添付ファイルも true と判定されるので除外）
    } elseif (is_single() && !is_attachment()) {
      //投稿が属するカテゴリーオブジェクトの配列を取得
      $categories = get_the_category($post->ID);
      //カテゴリーを表示する場合
      if (!$cat_off) {
        //カスタムフィールドに優先するカテゴリーが記載されていればその値を取得して $myterm へ
        $myterm = get_post_meta(get_the_ID(), 'myterm', true) ? esc_attr(get_post_meta(get_the_ID(), 'myterm', true)) : null;
        //一番下の階層のカテゴリーを取得
        $cat = get_deepest_term($categories, 'category', $myterm);
        //$cat_parents_off が false（初期値）でカテゴリーに親があればそれらを取得してリンクを生成してリストに追加
        if ($cat->parent != 0 && !$cat_parents_off) {
          $ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));
          foreach ($ancestors as $ancestor) {
            $str .= $separator;
            $str .= '<li' . $li_class . '><a href="' . esc_url(get_category_link($ancestor)) . '">' . get_cat_name($ancestor) . '</a></li>';
          }
        }
        //カテゴリーのリンクを追加
        $str .= $separator;
        $str .= '<li' . $li_class . '><a href="' . esc_url(get_category_link($cat->term_id)) . '">' . $cat->name . '</a></li>';
      }
      if ($show_current) {
        $str .= $separator;
        $str .= '<li' . $li_active_class . $aria_current . '>' . wp_strip_all_tags($post->post_title) . '</li>';
      }
      //固定ページ
    } elseif (is_page()) {
      //固定ページに親があればそれらを取得してリンクを生成してリストに追加
      if ($post->post_parent != 0) {
        $ancestors = array_reverse(get_post_ancestors($post->ID));
        foreach ($ancestors as $ancestor) {
          $str .= $separator;
          $str .= '<li' . $li_class . '><a href="' . esc_url(get_permalink($ancestor)) . '">' . get_the_title($ancestor) . '</a></li>';
        }
      }
      //固定ページ名を追加
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>' . wp_strip_all_tags($post->post_title) . '</li>';
      //日付ベースのアーカイブページ
    } elseif (is_date()) {
      //年別アーカイブ
      if (get_query_var('day') != 0) {
        //日付アーカイブページでは get_query_var() でアーカイブページの年・月・日を取得できる
        //取得した値と get_year_link() などを使ってリンクを生成
        $str .= $separator;
        $str .= '<li' . $li_class . '><a href="' . get_year_link(get_query_var('year')) . '">' . get_query_var('year') . '年</a></li>';
        $str .= $separator;
        $str .= '<li' . $li_class . '><a href="' . get_month_link(get_query_var('year'), get_query_var('monthnum')) . '">' . get_query_var('monthnum') . '月</a></li>';
        $str .= $separator;
        $str .= '<li' . $li_active_class . $aria_current . '>' . get_query_var('day') . '日</li>';
        //月別アーカイブ
      } elseif (get_query_var('monthnum') != 0) {
        $str .= $separator;
        $str .= '<li' . $li_class . '><a href="' . get_year_link(get_query_var('year')) . '">' . get_query_var('year') . '年</a></li>';
        $str .= $separator;
        $str .= '<li' . $li_active_class . $aria_current . '>' . get_query_var('monthnum') . '月</li>';
        //年別アーカイブ
      } else {
        $str .= $separator;
        $str .= '<li' . $li_active_class . $aria_current . '>' . get_query_var('year') . '年</li>';
      }
      //検索結果表示ページ
    } elseif (is_search()) {
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>「' . get_search_query() . '」' . $search . '</li>';
      //投稿者のアーカイブページ
    } elseif (is_author()) {
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>' . $author . ' : ' . get_the_author_meta('display_name', get_query_var('author')) . '</li>';
      //タグのアーカイブページ
    } elseif (is_tag()) {
      $str .= $separator;
      //$str.='<li' .$li_active_class. $aria_current.'>'. $tag .' '. single_tag_title( '' , false ). '</li>';
      $str .= '<li' . $li_active_class . $aria_current . '>' . single_tag_title($tag, false) . '</li>';
      //添付ファイルページ
    } elseif (is_attachment()) {
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>' . wp_strip_all_tags($post->post_title) . '</li>';
      //404 Not Found ページ
    } elseif (is_404()) {
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>' . $notfound . '</li>';
      //その他
    } else {
      $str .= $separator;
      $str .= '<li' . $li_active_class . $aria_current . '>' . wp_get_document_title() . '</li>';
    }
    $str .= "\n" . '</ul>' . "\n";
    $str .= '</' . $nav_div . '>' . "\n";
  }
  echo $str;
}

function get_deepest_term($terms, $mytaxonomy, $myterm = null)
{
  global $post;
  if ($myterm) {
    //$myterm が指定されていれば値からタームオブジェクトを生成
    $my_pref_term =  get_term_by('name', $myterm, $mytaxonomy);
    //タームオブジェクトが取得できて且つそのタームが現在の投稿に属していれば
    if ($my_pref_term && is_object_in_term($post->ID, $mytaxonomy, $my_pref_term->term_id)) {
      //優先的にそのタームを返す
      return $deepest =  $my_pref_term;
    }
  }
  //配列の要素が１つの場合その要素を最も深いタームとする
  if (count($terms) == 1) {
    $deepest = $terms[key($terms)];
  } else {
    $deepest = $terms[key($terms)];
    //祖先オブジェクトの最大数の初期化
    $max = 0;
    //それぞれのタームについて調査
    for ($i = 0; $i < count($terms); $i++) {
      //上の階層から順番に取得した祖先オブジェクトの ID の配列
      $ancestors = array_reverse(get_ancestors($terms[$i]->term_id, $terms[$i]->taxonomy));
      //祖先オブジェクトの数
      $ancestors_count = count($ancestors);
      //祖先オブジェクトの数を比較して最大数より大きければ
      if ($ancestors_count > $max) {
        //祖先オブジェクトの最大数を更新
        $max = $ancestors_count;
        //その要素を最も深いタームとする
        $deepest = $terms[$i];
      }
    }
  }
  return $deepest;
}

/**
 * ************************************************************************
 *  カスタム投稿
 * ************************************************************************
 */

/**
 * 定義
 */
add_action('init', 'my_add_custom_post');
function my_add_custom_post()
{
  register_post_type(
    'news',
    array(
      'label' => 'ニュース',
      'labels' => array(
        'name' => 'ニュース',
        'singular_name' => 'ニュース',
        'all_items' => 'ニュース一覧',
      ),
      'public' => true,
      'has_archive' => true,
      'menu_position' => 5,
      'supports' => array(
        'title',
        'editor',
        'custom-fields',
        'thumbnail',
        'revisions',
      ),
      'show_in_rest' => true, // Gutenbergエディタを有効
    )
  );
}

/**
 * カスタムタクソノミー
 */
add_action('init', 'my_add_taxonomy');
function my_add_taxonomy()
{
  register_taxonomy(
    'news-cat', // タクソノミー名
    'news', // カスタム投稿タイプ名
    array(
      'label' => 'ニュースカテゴリー',
      'labels' => array(
        'all_items' => '全てのカテゴリー',
        'add_new_item' => 'カテゴリーを追加'
      ),
      'public' => true,
      'hierarchical' => true,
      'show_in_rest' => true,
      'rewrite' => array(
        'slug' => 'news',
        'hierarchical' => true
      ),
    )
  );
}
// カスタムタクソノミーURLリライト
// デフォルトの /news-cat/xxx/ から /news/xxx/ でアクセスできるように
add_action('init', 'my_rewrite_rule_tax');
function my_rewrite_rule_tax()
{
  add_rewrite_rule(
    '^news/([^/]+)/?$',
    'index.php?taxonomy=news-cat&term=$matches[1]',
    'top'
  );
  add_rewrite_rule(
    'news/([^/]+)/page/?([0-9]{1,})/?$',
    'index.php?news-cat=$matches[1]&paged=$matches[2]',
    'top'
  );
}

/**
 * 管理画面に任意項目を表示
 */

// 列を作成
add_filter('manage_edit-news_columns', 'my_add_admin_columns');
function my_add_admin_columns($columns)
{
  $columns['my_column_taxonomy_name'] = 'カテゴリー';
  // 更新日を一番最後に移動
  unset($columns['date']);
  $columns['author'] = '作成者';
  $columns['date'] = '日時';
  $columns['pv'] = 'PV';
  return $columns;
}

// 作成した列にタクソノミーを表示
add_action('manage_news_posts_custom_column', 'my_add_admin_columns_content', 10, 2);
function my_add_admin_columns_content($column_name, $post_id)
{
  if ($column_name == 'my_column_taxonomy_name') {
    echo get_the_term_list($post_id, 'news-cat', '', ', ');
  }
  if ($column_name == 'pv') {
    $pv = get_post_meta($post_id, 'post_views_count', true);
    echo ($pv) ? $pv : '－';
  }
  if (isset($stitle) && $stitle) {
    echo esc_attr($stitle);
  }
}

// ソートする列の指定
add_filter('manage_edit-news_sortable_columns', 'my_add_sort_columns');
function my_add_sort_columns($columns)
{
  $columns['my_column_taxonomy_name'] = 'ID';
  $columns['author'] = 'ID';
  $columns['pv'] = 'PV';
  return $columns;
}

// タクソノミーで絞り込みする機能を追加
add_action('restrict_manage_posts', 'my_add_filter_columns');
function my_add_filter_columns()
{
  global $post_type;
  if ('news' === $post_type) :
    echo
    '<select name="news-cat">' .
      '<option value="">全てのカテゴリー</option>';
    foreach (get_terms('news-cat') as $term) :
      if ($_GET['news-cat'] === $term->slug) {
        echo '<option value="' . $term->slug . '" selected>' . $term->name . '</option>';
      } else {
        echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
      }
    endforeach;
    echo
    '</select>';
  endif;
}

// 投稿ページのURLリライト
add_action('init', 'my_rewrite_custom_post');
function my_rewrite_custom_post()
{
  global $wp_rewrite;
  // * ↓ ↓ ↓ 複数ある場合はこの単位で複製する ↓ ↓ ↓ *
  $wp_rewrite->add_rewrite_tag('%news%', '(news)', 'post_type=');
  $wp_rewrite->add_permastruct('news', '/%news%/%news-cat%/%post_id%/', false);
  // * ↑ ↑ ↑ 複数ある場合はこの単位で複製する ↑ ↑ ↑ *
}

add_filter('post_type_link', 'my_permalink_custom_post', 1, 3);
function my_permalink_custom_post($post_link, $id = 0, $leavename)
{
  global $wp_rewrite;
  $post_delivery = get_post($id);
  $post = $post_delivery;
  if (is_wp_error($post)) {
    return $post;
  }
  // * ↓ ↓ ↓ 複数ある場合はこの単位で複製する ↓ ↓ ↓ *
  if ('news' === $post->post_type) {
    $newlink = $wp_rewrite->get_extra_permastruct($post->post_type);
    $newlink = str_replace('%news%', $post->post_type, $newlink);

    $get_term = get_the_terms($post->ID, 'news-cat');
    if ($get_term) {
      $term_name = array_shift($get_term);
      $term_slug = $term_name->slug;
    } else {
      $term_slug = '';
    }

    $newlink = str_replace('%news-cat%', $term_slug, $newlink);
    $newlink = str_replace('%post_id%', $post->ID, $newlink);
    $newlink = home_url(user_trailingslashit($newlink));
    return $newlink;
  }
  // * ↑ ↑ ↑ 複数ある場合はこの単位で複製する ↑ ↑ ↑ *
  return $post_link;
}

/**
 * ************************************************************************
 *  投稿の人気記事（PV）取得
 *  各投稿のカスタムフィールドにPVフィールドを追加（投稿ページアクセス時に値を+1する）
 *  @see https://cage.tokyo/wordpress/wordpress-ranking/
 * ************************************************************************
 *
 * 投稿ページ（single-○○.php）の1行目に以下を記述
 * <?php if(!is_user_logged_in() && !is_bot()) { set_post_views(get_the_ID()); } ?>
 */

function set_post_views($postID)
{
  $count_key = 'post_views_count';
  $count = get_post_meta($postID, $count_key, true);
  if ($count == '') {
    $count = 0;
    delete_post_meta($postID, $count_key);
    add_post_meta($postID, $count_key, '0');
  } else {
    $count++;
    update_post_meta($postID, $count_key, $count);
  }
}

//クローラーのアクセスを判別するために追記
function is_bot()
{
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $bot = array(
    'Googlebot',
    'Yahoo! Slurp',
    'Mediapartners-Google',
    'msnbot',
    'bingbot',
    'MJ12bot',
    'Ezooms',
    'pirst; MSIE 8.0;',
    'Google Web Preview',
    'ia_archiver',
    'Sogou web spider',
    'Googlebot-Mobile',
    'AhrefsBot',
    'YandexBot',
    'Purebot',
    'Baiduspider',
    'UnwindFetchor',
    'TweetmemeBot',
    'MetaURI',
    'PaperLiBot',
    'Showyoubot',
    'JS-Kit',
    'PostRank',
    'Crowsnest',
    'PycURL',
    'bitlybot',
    'Hatena',
    'facebookexternalhit',
    'NINJA bot',
    'YahooCacheSystem',
    'NHN Corp.',
    'Steeler',
    'DoCoMo',
  );
  foreach ($bot as $bot) {
    if (stripos($ua, $bot) !== false) {
      return true;
    }
  }
  return false;
}

/**
 * ************************************************************************
 *  Contact Form 7
 * ************************************************************************
 */

/**
 * post成功時にセッションをセット
 * サンクスページアクセス時、このセッションが無いとTOPへリダイレクトさせる（URL直打ちのアクセス対策）
 */
add_action('wpcf7_mail_sent', 'my_wpcf7_mail_sent_session_start');
function my_wpcf7_mail_sent_session_start()
{
  session_start();
  $_SESSION['thanks_judge'] = true;
}

/**
 * Contact Form 7を使用するページのみ、関係ファイルを読み込ませる
 */
add_action('wp_enqueue_scripts', 'my_enqueue_wpcf7_files');
function my_enqueue_wpcf7_files()
{
  if (!is_page('contact')) {
    wp_dequeue_style('contact-form-7');
    wp_dequeue_script('contact-form-7');
    wp_dequeue_script('google-recaptcha');
  }
}
