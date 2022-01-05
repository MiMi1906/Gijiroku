// ajax_add_content.js
// jQuery 必須
// 要素追加用
// 20件ずつ追加

const _sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));


var nowURL = location.pathname;
var nowParameter;
var ajaxURL = '';
var thread_id;
var emph_id;
var member_id;

if (nowURL == '/') {
  ajaxURL = '/getPosts/root.php';
} else if (nowURL == '/thread/') {
  nowParameter = location.search;
  nowParameter = nowParameter.slice(1);
  nowParameter = nowParameter.split(/\&/);
  thread_id = nowParameter[0].replace(/[^0-9]/g, '');
  emph_id = nowParameter[1].replace(/[^0-9]/g, '');
  ajaxURL = '/getPosts/thread.php';
} else if (nowURL == '/profile/') {
  nowParameter = location.search;
  member_id = nowParameter.replace(/[^0-9]/g, '');
  ajaxURL = '/getPosts/profile.php';
}

// 最初に呼び出し
ajax_add_content();

document.getElementById('main').onscroll = event => {
  if (isFullScrolled(event)) {
    // 要素を追加
    ajax_add_content();
    _sleep(100);
  }
}

// 要素最下部までスクロールしたかをチェック
function isFullScrolled(event) {
  const positionWithAdjustmentValue = event.target.clientHeight + event.target.scrollTop - 0;
  // スクロールしていたら true を返す
  return positionWithAdjustmentValue >= event.target.scrollHeight;
}

// Ajaxコンテンツ追加処理
function ajax_add_content() {
  // 追加コンテンツ
  var add_content = "";
  // コンテンツ件数
  // 初期値 0
  var count = Number($("#count").val());
  // Ajax処理
  $.post({
    type: "post",
    datatype: "json",
    url: ajaxURL,
    data: {
      'count': count,
      'thread_id': thread_id,
      'emph_id': emph_id,
      'member_id': member_id
    }
  }).done(function (data) {
    // コンテンツ生成
    add_content = data;
    // コンテンツ追加
    $("#message_list").append(add_content);
    // 取得件数を加算してセット
    // 加算値 20
    count += 20;
    $("#count").val(count);
  }).fail(function (e) {
    console.log(e);
  })
}