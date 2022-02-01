<div class="wrap">
  <h2>テーマ設定</h2>

  <?php
  if (isset($_GET['settings-updated'])) :
    if (true == $_GET['settings-updated']) :
      echo '<div id="settings_updated" class="updated notice is-dismissible"><p><strong>設定を保存しました。</strong></p></div>';
    endif;
  endif;
  ?>

  <nav class="c-tab">
    <ul>
      <li data-tab-nav="general">一般</li>
      <li data-tab-nav="themeinfo">テーマ情報</li>
    </ul>
  </nav>

  <form method="post" action="options.php" enctype="multipart/form-data" encoding="multipart/form-data">
    <?php
    settings_fields('custom-menu-group');
    do_settings_sections('custom-menu-group');
    ?>
    <div class="metabox-holder" data-tab-content="general">
      <div class="postbox">
        <h3 class="postbox-header"><span>一般</span></h3>
        <?php $value_general = get_option('general'); ?>
        <div class="inside">
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row"><label>テキスト</label></th>
                <td>
                  <input type="text" name="general[text]" value="<?php echo $value_general['text']; ?>">
                </td>
              </tr>
              <tr>
                <th scope="row"><label>テキストエリア</label></th>
                <td>
                  <textarea name="general[textarea]"><?php echo $value_general['textarea']; ?></textarea>
                </td>
              </tr>
              <tr>
                <th scope="row"><label>画像</label></th>
                <td>
                  <input type="text" id="general_img" name="general[img]" value="<?php echo $value_general['img']; ?>" readonly class="regular-text">
                  <input type="button" name="general_img__slect" value="選択">
                  <input type="button" name="general_img__clear" value="クリア">
                  <div id="general_img__thumb" class="uploded-thumb">
                    <?php if ($value_general['img']) : ?>
                      <img src="<?php echo $value_general['img']; ?>" alt="選択中の画像">
                    <?php endif ?>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="metabox-holder" data-tab-content="themeinfo">
      <div class="postbox">
        <h3 class="postbox-header"><span>テーマ情報</span></h3>
        <div class="inside">
          <?php
          $sep = '----------------------------------------------' . PHP_EOL;
          $all = $sep;

          //サイト情報
          $all .= 'サイト名：　' . get_bloginfo('name') . PHP_EOL;
          $all .= 'サイトURL：　' . site_url() . PHP_EOL;
          $all .= 'ホームURL：　' . home_url() . PHP_EOL;
          $all .= 'コンテンツURL：　' . str_replace(home_url(), '', content_url()) . PHP_EOL;
          $all .= 'インクルードURL：　' . str_replace(home_url(), '', includes_url()) . PHP_EOL;
          $all .= 'テンプレートURL：　' . str_replace(home_url(), '', get_template_directory_uri()) . PHP_EOL;
          $all .= 'スタイルシートURL：　' . str_replace(home_url(), '', get_stylesheet_directory_uri()) . PHP_EOL;
          $ip = @$_SERVER['REMOTE_ADDR'];
          if ($ip) {
            if (!preg_match('{^[0-9\.]+$}i', $ip)) {
              $host = gethostbyaddr($ip);
              $all .= 'サーバー：　' . $host . PHP_EOL;
            }
          }
          $all .= 'Wordpressバージョン：　' . get_bloginfo('version') . PHP_EOL;
          $all .= 'PHPバージョン：　' . phpversion() . PHP_EOL;
          if (isset($_SERVER['HTTP_USER_AGENT']))
            $all .= 'ブラウザ：　' . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;
          if (isset($_SERVER['SERVER_SOFTWARE']))
            $all .= 'サーバーソフト：　' . $_SERVER['SERVER_SOFTWARE'] . PHP_EOL;
          if (isset($_SERVER['SERVER_PROTOCOL']))
            $all .= 'サーバープロトコル：　' . $_SERVER['SERVER_PROTOCOL'] . PHP_EOL;
          if (isset($_SERVER['HTTP_ACCEPT_CHARSET']))
            $all .= '文字セット：　' . $_SERVER['HTTP_ACCEPT_CHARSET'] . PHP_EOL;
          if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
            $all .= 'エンコーディング：　' . $_SERVER['HTTP_ACCEPT_ENCODING'] . PHP_EOL;
          if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            $all .= '言語：　' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . PHP_EOL;
          $all .= $sep;
          include_once(ABSPATH . 'wp-admin/includes/plugin.php');
          $plugins = get_plugins();
          if (!empty($plugins)) {
            $all .= __('利用中のプラグイン：　') . PHP_EOL;
            foreach ($plugins as $path => $plugin) {
              if (is_plugin_active($path)) {
                $all .= $plugin['Name'];
                $all .= ' ' . $plugin['Version'] . PHP_EOL;
              }
            }
            $all .= $sep;
          }
          // var_dump($all);
          ?>
          <pre><?php echo $all; ?></pre>
        </div>
      </div>
    </div>

    <?php submit_button(); ?>

  </form>
</div>

<style>
  /* タブ */
  .c-tab ul {
    border-bottom: solid 1px #999;
  }

  .c-tab ul li {
    display: inline-block;
    background-color: #f1f1f1;
    border: 1px #999;
    border-style: solid solid none;
    margin: 0;
    padding: 5px 8px 3px;
    cursor: pointer;
  }

  .c-tab ul li.__active {
    background-color: #fff;
  }

  /* その他 */
  .uploded-thumb {
    margin: 10px 0 0;
    max-width: 300px;
  }

  .uploded-thumb img {
    width: 100%;
    height: auto;
  }

  input[type="text"]:not([readonly]) {
    width: 100%;
  }

  textarea {
    width: 100%;
    min-height: 120px;
  }
</style>

<script>
  (function($) {
    /**
     * WP アップローダー
     */
    function new_wp_uploader(_uniq_id) {
      var custom_uploader;
      $('input:button[name=' + _uniq_id + '__slect]').click(function(e) {
        e.preventDefault();
        if (custom_uploader) {
          custom_uploader.open();
          return;
        }
        custom_uploader = wp.media({
          title: '画像を選択してください',
          // ライブラリの一覧は画像のみにする
          library: {
            type: 'image'
          },
          button: {
            text: '画像の選択'
          },
          // 選択できる画像を1枚のみにする => false
          multiple: false
        });
        /**
         * 画像選択時
         */
        custom_uploader.on('select', function() {
          var images = custom_uploader.state().get('selection');
          images.each(function(file) {
            // リセット
            $('#' + _uniq_id + '__thumb').empty();
            $('#' + _uniq_id).val('');
            // 画像取得してセット
            $('#' + _uniq_id).val(file.attributes.sizes.full.url);
            $('#' + _uniq_id + '__thumb').append('<img src="' + file.attributes.sizes.full.url + '">');
          });
        });
        custom_uploader.open();
      });
      /**
       * クリアボタン クリック時
       */
      $('input:button[name=' + _uniq_id + '__clear]').click(function() {
        $('#' + _uniq_id).val('');
        $('#' + _uniq_id + '__thumb').empty();
      });
    };
    new_wp_uploader('general_img');

    /**
     * タブ
     */
    function tabInit() {
      var classActive = '__active';
      var attrNav = 'data-tab-nav';
      var attrContent = 'data-tab-content';
      var $nav = $('[' + attrNav + ']');
      var $content = $('[' + attrContent + ']');
      var initShow = 'general'; // 最初に表示するタブ
      var toggle = function(_elm, _key) {
        _elm.hide();
        if (_elm.attr(attrContent) === _key) _elm.show();
      }
      // ページ読み込み時
      $(document).ready(function() {
        $content.each(function() {
          toggle($(this), initShow);
        });
        $('[' + attrNav + '="' + initShow + '"]').addClass(classActive);
      });
      // タブクリック時
      $nav.on('click', function() {
        var key = $(this).attr(attrNav);
        $nav.each(function() {
          $(this).removeClass(classActive);
        });
        $(this).addClass(classActive);
        $content.each(function() {
          toggle($(this), key);
        });
      });
    }
    tabInit();

  })(jQuery);
</script>