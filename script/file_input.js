$('.file_input').on('change', function () {
  var file = $(this).prop('files')[0];
  $('.file_name').text(file.name);
  $('.file_input_btn').css({
    'background-color': 'var(--sub-color)', 'border-color': 'var(--main-color)', 'color': 'var(--main-color)'
  });
  $('.file_input_alert').hide();
});