// nice.js
// jQuery 必須
// いいね機能用

// likeクラスがクリックされたとき発動
$(document).on('click', '.like', function () {
  // idを取得
  let id = $(this).attr("id").slice(1);
  // Ajax処理
  $.ajax({
    url: '/nice/',
    type: 'POST',
    dataType: 'json',
    data: {
      'id': id
    }
  })
    .done((data) => {
      var html;
      // カウントなどにより、表示を変える
      if (data.like_cnt == 0) {
        html = '<object><i class="far fa-heart"></i></object>';
      } else if (data.inc_dec == 'inc') {
        html = '<object><i class="fas fa-heart"></i>' + data.like_cnt + '</object>';
      } else {
        html = '<object><i class="far fa-heart"></i>' + data.like_cnt + '</object>';
      }
      $('#n' + id).html(html);
    })
    .fail((_data) => {
    })
    .always((_data) => {

    });
});
