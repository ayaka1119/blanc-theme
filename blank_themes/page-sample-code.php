<?php get_header(); ?>

<style>
  html{
    scroll-behavior: smooth;
  }
  .p-sample-code {
    display: flex;
    align-items: flex-start;
  }
  .p-sample-code__main {
    width: 80%;
    padding-right: 80px;
  }
  .p-sample-code__outline {
    width: 20%;
    height: calc(100vh - 70px);
    position: sticky;
    top: 90px;
    overflow-y: auto;
    padding-bottom: 100px;
  }
  @media screen and (max-width: 768px) {
    .p-sample-code__main {
      width: 100%;
      padding-right: 0;
    }
    .p-sample-code__outline {
      display: none;
    }
  }
  .p-sample-code__outline > ul > li > a,
  .p-sample-code__outline > ul > li > ul > li > a {
    display: block;
    color: #3d84bf;
    font-size: 13px;
    line-height: 1.5;
    padding: 4px 4px;
    transition: background-color .2s;
  }
  .p-sample-code__outline a.__active {
    background-color: #fffacf;
  }
  .p-sample-code__outline > ul > li > ul {
    padding-left: 1em;
  }
  .p-sample-code__outline > ul > li > ul > li {
    display: flex;
  }
  .p-sample-code__outline > ul > li > ul > li::before {
    content: "\30FB";
    display: block;
  }
  .p-sample-code__section h2 {
    font-size: 20px;
    font-weight: bold;
    border-bottom: solid 3px #333;
    margin: 100px 0 40px;
    padding-bottom: 8px;
  }
  .p-sample-code__section section {
    margin-bottom: 60px;
  }
  .p-sample-code__section h3 {
    font-weight: bold;
    margin-bottom: 8px;
  }
  .p-sample-code__section pre {
    margin-bottom: 8px;
  }
  .p-sample-code__section pre code {
    font-size: 14px;
    padding: 0 20px;
  }
  .p-sample-code__section summary {
    border-bottom: solid 1px #333;
    cursor: pointer;
    transition: opacity .2s;
  }
  .p-sample-code__section summary:hover {
    opacity: 0.5;
  }
  .p-sample-code__section a[target="_blank"] {
    padding-right: 23px;
    position: relative;
  }
  .p-sample-code__section a[target="_blank"]::before,
  .p-sample-code__section a[target="_blank"]::after {
    content: "";
    display: block;
    background-color: transparent;
    border: solid 2px #2b8af7;
    position: absolute;
    top: 3px;
    right: 0;
    bottom: auto;
    width: 10px;
    height: 8px;
    transform: none;
    transition: right .2s,top .2s;
  }
  .p-sample-code__section a[target="_blank"]::after {
    border-style: none solid solid none;
    top: 6px;
    right: -3px;
  }
</style>
<link rel="stylesheet" href="//unpkg.com/@highlightjs/cdn-assets@11.2.0/styles/monokai-sublime.min.css">
<script src="//unpkg.com/@highlightjs/cdn-assets@11.2.0/highlight.min.js" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', (_ev) => {
    (function() {
      Array.prototype.slice.call(document.querySelectorAll('pre code')).forEach(_elm => {
        hljs.highlightElement(_elm);
      });
    })();
    (function() {
      // 目次用のidと要素を生成
      (function() {
        const outline = document.querySelector('#js-outline');
        // h2を目次に追加
        [...document.querySelectorAll('.p-sample-code__section')].forEach((_section, _index) => {
          const title = _section.querySelector('h2');
          title.id = `sec-${_index}`;
          outline.insertAdjacentHTML('beforeend', `<li><a href="#sec-${_index}">${title.textContent}</a></li>`);
          // h3があれば目次に追加
          [..._section.querySelectorAll('section h3')].forEach((_heading, _childIndex) => {
            const targetList = outline.querySelector(`a[href="#sec-${_index}"]`);
            if(_childIndex === 0) {
              targetList.insertAdjacentHTML('afterend', '<ul></ul>');
            }
            _heading.id = `sec-${_index}-${_childIndex}`;
            targetList.nextElementSibling.insertAdjacentHTML('beforeend', `<li><a href="#sec-${_index}-${_childIndex}">${_heading.textContent}</a></li>`);
          });
        });
      })();
      // IntersectionObserverの設定
      const targets = document.querySelectorAll('.p-sample-code__section h2, .p-sample-code__section h3');
      console.log(targets)
      const options = {
        root: null,
        rootMargin: '1% 0px',
        threshold: 0,
      };
      const observer = new IntersectionObserver(doWhenIntersect, options);
      targets.forEach(box => {
        observer.observe(box);
      });
      // 交差した時に目次の色を変える
      function doWhenIntersect(_entries) {
        _entries.forEach(entry => {
          if (entry.isIntersecting) {
            const currentActiveIndex = document.querySelector('#js-outline .__active');
            if (currentActiveIndex !== null) {
              currentActiveIndex.classList.remove('__active');
            }
            const newActiveIndex = document.querySelector(`a[href='#${entry.target.id}']`);
            newActiveIndex.classList.add('__active');
          }
        });
      }
    })();
  });
</script>

<main class="l-main l-under" role="main">

  <div class="l-under__breadcrumb">
    <?php create_breadcrumb(); ?>
  </div>
  <div class="l-under__header">
    <h1 class="c-title--center"><?php the_title(); ?></h1>
  </div>

  <div class="l-under__content l-outer">

    <div class="p-sample-code l-inner">
      <div class="p-sample-code__main">
        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <!-- カテゴリー -->
        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <section class="p-sample-code__section">
          <h2>カテゴリー</h2>
          <section>
            <h3>「全てのカテゴリー」を取得</h3>
            <pre><code>$categories = get_categories();</code></pre>
            <details>
              <summary>出力例</summary>
              <?php
                echo('<pre>');
                print_r(get_categories());
                echo('</pre>');
              ?>
            </details>
          </section>
          <section>
            <h3>「全てのカテゴリー」をリンク付きでHTML出力</h3>
            <pre><code>$categories = get_categories();
foreach($categories as $category) {
  echo '&lt;a href="' . get_category_link($category->term_id) . '"&gt;' . $category->name . '&lt;/a&gt;';
}</code></pre>
            <details>
              <summary>出力例</summary>
              <?php
                $categories = get_categories();
                foreach($categories as $category) {
                  echo '<a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a>';
                }
              ?>
            </details>
          </section>
          <section>
            <h3>「投稿に設定されているカテゴリー」をリンク付きでHTML出力（1つのみ）</h3>
            <pre><code>$category = get_the_category()[0];
echo '&lt;a href="' . get_category_link($category->term_id) . '"&gt;' . $category->name . '&lt;/a&gt;';</code></pre>
          </section>
          <section>
            <h3>「カスタム投稿に設定されているターム」をリンク付きでHTML出力（1つのみ）</h3>
            <pre><code>$term = get_the_terms($post->ID, 'タクソノミーのスラッグ')[0];
echo '&lt;a href="' . get_term_link($term->slug, 'タクソノミーのスラッグ') . '"&gt;' . $term->name . '&lt;/a&gt;';</code></pre>
          </section>
        </section>

        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <!-- 投稿一覧 -->
        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <section class="p-sample-code__section">
          <h2>投稿一覧</h2>
            <section>
              <h3>投稿一覧を取得（メインループ）</h3>
              <pre><code>&lt;?php
  if (have_posts()) :
    while (have_posts()) : the_post();
      // タイトルなどの投稿情報をこの中に書く
    endwhile;
  else :
    echo '&lt;p&gt;投稿がありません。&lt;/p&gt;';
  endif;
?&gt;</code></pre>
            </section>
            <section>
              <h3>投稿一覧を取得（サブループ）</h3>
              <pre><code>&lt;?php
  $args = array(
    // パラメータ
  );
  $the_query = new WP_Query($args);
  if ($the_query->have_posts()) :
    while ($the_query->have_posts()) : $the_query->the_post();
      // タイトルなどの投稿情報をこの中に書く
    endwhile;
  else :
    echo '&lt;p&gt;投稿がありません。&lt;/p&gt;';
  endif;
  wp_reset_postdata();
?&gt;</code></pre>
<p>パラメータの例</p>
              <pre><code>$args = array(
  'post_type' => 'news',
  'taxonomy' => 'news-cat',
  'post_status' => 'publish',
  'orderby' => 'date',
  'order' => 'DESC',
  'posts_per_page' => 5,
);</code></pre>
              <p><a class="u-link" href="https://wemo.tech/160" target="_blank" rel="noreferrer noopener">パラメータ一覧（外部サイト）</a></p>
            </section>
        </section>

        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <!-- ページャー -->
        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <section class="p-sample-code__section">
          <h2>ページャー</h2>
          <section>
            <h3>ページャーを取得（get_the_posts_pagination）</h3>
            <p>メインループで主に使用</p>
            <pre><code>&lt;?php
  // デフォルトだとスクリーンリーダー用の要素が出力されるから、preg_replace()で消す
  $pagination = preg_replace(
    '/\&lt;h2 class=\"screen-reader-text\"\>(.*?)\&lt;\/h2\&gt;/',
    '',
    get_the_posts_pagination(array(
      'mid_size' =&gt; 2, // 現在のページの左右に表示するページ番号の数
      'prev_text' =&gt; '',
      'next_text' =&gt; ''
    ))
  );
  if ($pagination) {
    echo '&lt;div class="p-entries__pagination"&gt;';
    echo '  &lt;div class="c-pagination"&gt;' . $pagination . '&lt;/div&gt;';
    echo '&lt;/div&gt;';
  }
?&gt;</code></pre>
<p><a class="u-link" href="https://www.webdesignleaves.com/pr/wp/wp_func_pager.html" target="_blank" rel="noreferrer noopener">パラメータ一覧（外部サイト）</a></p>
          </section>
          <section>
            <h3>ページャーを取得（paginate_links）</h3>
            <p>サブループで主に使用</p>
            <pre><code>&lt;div class="p-entries"&gt;
  &lt;?php
    global $max_num_page;
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $args = array(
      // パラメータは任意のものを指定（ paged は必須）
      'post_type' =&gt; 'post',
      'posts_per_page' =&gt; 10,
      'orderby' =&gt; 'date',
      'order' =&gt; 'DESC',
      'post_status' =&gt; 'publish',
      'paged' =&gt; $paged,
    );
    $the_query = new WP_Query( $args );
    while ( $the_query-&gt;have_posts() ) : $the_query-&gt;the_post();
      // タイトルなどの投稿情報をこの中に書く
    endwhile;
    wp_reset_postdata();
  ?&gt;
  &lt;/div&gt;

  &lt;?php
    if ($the_query-&gt;max_num_pages &gt; 1) {
      echo '&lt;div class="p-entries__pagination"&gt;';
      echo paginate_links( array(
        'base' =&gt; get_pagenum_link(1).'%_%',
        'format' =&gt; 'page/%#%/',
        'current' =&gt; max(1, $paged),
        'total' =&gt; $the_query-&gt;max_num_pages,
        'type' =&gt; 'list',
        'mid_size' =&gt; '1',
        'prev_text' =&gt; '&lt;',
        'next_text' =&gt; '&gt;'
      ) );
      echo '&lt;/div&gt;';
    }
?></code></pre>
<p><a class="u-link" href="https://www.webdesignleaves.com/pr/wp/wp_func_pager.html" target="_blank" rel="noreferrer noopener">パラメータ一覧（外部サイト）</a></p>
          </section>
        </section>

        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <!-- 投稿情報 -->
        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <section class="p-sample-code__section">
          <h2>投稿情報（記事タイトルなど）</h2>
          <section>
            <h3>タイトル</h3>
            <pre><code>&lt;h1&gt;&lt;?php the_title(); ?&gt;&lt;/h1&gt;</code></pre>
          </section>
          <section>
            <h3>パーマリンク</h3>
            <pre><code>&lt;a href=&quot;&lt;?php the_permalink(); ?&gt;&quot;&gt;xxxxx&lt;/a&gt;</code></pre>
          </section>
          <section>
            <h3>投稿日</h3>
            <pre><code>&lt;time datetime=&quot;&lt;?php echo get_the_date(&#039;Y-m-d&#039;); ?&gt;&quot;&gt;
  &lt;?php echo get_the_date(&#039;Y.m.d&#039;); ?&gt;
&lt;/time&gt;</code></pre>
          </section>
          <section>
            <h3>更新日</h3>
            <pre><code>&lt;time datetime=&quot;&lt;?php echo get_the_modified_date(&#039;Y-m-d&#039;); ?&gt;&quot;&gt;
  &lt;?php echo get_the_modified_date(&#039;Y.m.d&#039;); ?&gt;
&lt;/time&gt;</code></pre>
          </section>
          <section>
            <h3>投稿日と更新日が違う場合のみどちらも表示（同じなら投稿日のみ表示）</h3>
            <pre><code>&lt;span&gt;投稿日&lt;/span&gt;
&lt;time datetime=&quot;&lt;?php echo get_the_date(&#039;Y-m-d&#039;); ?&gt;&quot;&gt;
  &lt;?php echo get_the_date(&#039;Y.m.d&#039;); ?&gt;
&lt;/time&gt;
&lt;?php if(get_the_date(&#039;Y-m-d&#039;) !== get_the_modified_date(&#039;Y-m-d&#039;)) : ?&gt;
  &lt;span&gt;更新日&lt;/span&gt;
  &lt;time datetime=&quot;&lt;?php echo get_the_modified_date(&#039;Y-m-d&#039;); ?&gt;&quot;&gt;
    &lt;?php echo get_the_modified_date(&#039;Y.m.d&#039;); ?&gt;
  &lt;/time&gt;
&lt;?php endif; ?&gt;</code></pre>
          </section>
          <section>
            <h3>最終更新日時（x日前、x時間前など）</h3>
            <pre><code>&lt;span&gt;最終更新：
&lt;?php echo human_time_diff( get_the_modified_date(&#039;U&#039;), current_time(&#039;timestamp&#039;) ); ?&gt;
前&lt;/span&gt;</code></pre>
          </section>
          <section>
            <h3>サムネイル</h3>
            <pre><code>&lt;?php
  if (get_the_post_thumbnail()) {
    the_post_thumbnail(&#039;full&#039;);
  } else {
    echo &#039;&lt;img src=&quot;&#039; . home_url('no-image.jpg') . &#039;&quot; alt=&quot;&quot;&gt;&#039;;
  };
?&gt;</code></pre>
          </section>
          <section>
            <h3>投稿本文</h3>
            <pre><code>&lt;?php if (have_posts()) : ?&gt;
  &lt;?php while (have_posts()) : the_post(); ?&gt;
    &lt;?php the_content(); ?&gt;
  &lt;?php endwhile; ?&gt;
&lt;?php endif; ?&gt;</code></pre>
          </section>
          <section>
            <h3>抜粋</h3>
            <pre><code>&lt;?php the_excerpt(); ?&gt;</code></pre>
          </section>
          <section>
            <p><a href="https://wemo.tech/400" target="_blank" rel="noreferrer noopener" class="u-link">その他の情報を出力するコード（外部リンク）</a></p>
          </section>
        </section>

        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <!-- テンプレ -->
        <!-- ######################################################################## -->
        <!-- ######################################################################## -->
        <!-- <section class="p-sample-code__section">
          <h2>テンプレ</h2>
          <section>
            <h3>テンプレを取得</h3>
            <pre><code></code></pre>
            <details>
              <summary>出力例</summary>
              <?php
                echo('<pre>');
                print_r();
                echo('</pre>');
              ?>
            </details>
          </section>
        </section> -->

      </div>
      <div class="p-sample-code__outline">
        <ul id="js-outline"></ul>
      </div>
    </div>

  </div>

</main>

<?php get_footer(); ?>