// show_thread.js
// jQuery 必須
// スレッドへのジャンプ用

// 投稿がクリックされたとき発動
$(document).on('click', '.link', function () {
  let id = $(this).attr("id");
  let id_list = id.split(/\s+/);
  // 指定してあるIDからURLを生成
  let url = '/thread/?thread_id=' + id_list[0].slice(1) + '&emph_id=' + id_list[1].slice(1) + '#' + id_list[1].slice(1);
  // URLにジャンプ
  window.location.href = url;
});