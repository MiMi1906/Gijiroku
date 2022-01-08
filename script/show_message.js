// show_message.js
// jQuery 必須
// メッセージごとの閲覧用

// 投稿がクリックされたとき発動
$(document).on('click', '.link', function () {
  let id = $(this).attr("id");
  let id_list = id.split(/\s+/);
  // 指定してあるIDからURLを生成
  let url = '/view/?id=' + id_list[1].slice(1);
  // URLにジャンプ
  window.location.href = url;
});