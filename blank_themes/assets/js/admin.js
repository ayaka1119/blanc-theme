/**
 * 投稿ページの公開ボタンクリック時に、確認アラートを表示させる
 * （旧エディタ用）
 */
function addPublishAlert() {
  var publishBtn = document.querySelector('#publish') || false;
  if (!publishBtn) return;
  var flag = false;
  publishBtn.onclick = function () {
    if (flag) return;
    flag = true;
    if (!confirm(publishBtn.value + 'します。よろしいですか？')) {
      flag = false;
      return false;
    }
  };
}

window.addEventListener('DOMContentLoaded', function () {
  // addPublishAlert();
});
