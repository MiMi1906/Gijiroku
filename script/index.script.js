ajax_add_content();

$(document).on('click', '.nice', function () {
  let id = $(this).attr("id");
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
      if (data.like_cnt == 0) {
        html = '<object><i class="far fa-heart"></i></object>';
      } else if (data.inc_dec == 'inc') {
        html = '<object><i class="fas fa-heart"></i>' + data.like_cnt + '</object>';
      } else {
        html = '<object><i class="far fa-heart"></i>' + data.like_cnt + '</object>';
      }
      $('#' + id).html(html);
    })
    .fail((_data) => {
    })
    .always((_data) => {

    });
});

document.getElementById('main').onscroll = event => {
  if (isFullScrolled(event)) {
    ajax_add_content();
  }
}

function isFullScrolled(event) {
  const positionWithAdjustmentValue = event.target.clientHeight + event.target.scrollTop;
  return positionWithAdjustmentValue >= event.target.scrollHeight;
}

// ajaxコンテンツ追加処理
function ajax_add_content() {
  // 追加コンテンツ
  var add_content = "";
  // コンテンツ件数
  var count = Number($("#count").val());
  // ajax処理
  $.post({
    type: "post",
    datatype: "json",
    url: "/getPosts/",
    data: { count: count }
  }).done(function (data) {
    // コンテンツ生成
    add_content = data;
    // コンテンツ追加
    $("#message_list").append(add_content);
    // 取得件数を加算してセット
    count += 20;
    $("#count").val(count);
  }).fail(function (e) {
    console.log(e);
  })
}

$(document).on('click', '.link', function () {
  let id = $(this).attr("id");
  let id_list = id.split(/\s+/);
  let url = '/thread/?thread_id=' + id_list[0].slice(1) + '&emph_id=' + id_list[1].slice(1) + '#' + id_list[1].slice(1);
  console.log(url);
  window.location.href = url;
});