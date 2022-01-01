$('.like').on('click', function () {
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

$('.link').on('click', function () {
  let id = $(this).attr("id");
  let url = '/thread/?id=' + id.slice(1)
  window.location.href = url;
});
