<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

loginCheck();

// データベース接続
$db = dbConnect();

$tpl = new Template();

if (empty($_GET['member_id']) || empty($_GET['flag'])) {
  header('Location: /');
  exit();
}

$key_str;

if ($_GET['flag'] == 'follows') {
  $sql = 'SELECT * FROM follow WHERE member_id = :id';
  $key_str = 'follow_id';
} else if ($_GET['flag'] == 'followers') {
  $sql = 'SELECT * FROM follow WHERE follow_id = :id';
  $key_str = 'member_id';
} else if (
  $_GET['flag'] == 'supports'
) {
  $sql = 'SELECT * FROM support WHERE member_id = :id';
  $key_str = 'support_id';
} else if (
  $_GET['flag'] == 'supporters'
) {
  $sql = 'SELECT * FROM support WHERE support_id = :id';
  $key_str = 'member_id';
}
$members = $db->prepare($sql);
$members->bindValue(':id', $_GET['member_id']);
$members->execute();
$member = $members->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" rel="stylesheet">

  <link rel="stylesheet" href="/css/remodal.css">
  <link rel="stylesheet" href="/css/remodal-default-theme.css">

  <link rel="stylesheet" href="/css/general.css">

  <title>メンバー / Gijiroku</title>
</head>

<body>

  <?php
  $tpl->setValue_tpl_header('メンバー');
  $tpl->show(TPL_HEADER_BAR);
  ?>

  <main class="main" id="main">
    <div class="content">
      <div class="message_list" id="message_list">
        <?php
        foreach ($member as $m) {
          $sql = 'SELECT * FROM members WHERE id = :id';
          $stmt = $db->prepare($sql);
          $stmt->bindValue(':id', $m[$key_str]);
          $stmt->execute();
          $list = $stmt->fetch();
          print('<div class="member_list">');
          print('<a href="/profile/?id=' . $list['id'] . '">');
          print('<span class="image">');
          print('<img src="' . $list['image'] . '" alt="">');
          print('</span>');
          print('<span class="text">');
          print('<span class="heading">');
          print($list['name']);
          print('</span>');
          print('<span class="exp">');
          print($list['bio']);
          print('</span>');
          print('</a>');
          print('</div>');
        }
        ?>
      </div>
    </div>
  </main>

  <?php
  $tpl->show(TPL_FOOTER_BAR);
  ?>

  <!-- script -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <script src="/script/nice.js"></script>
  <script src="/script/show_thread.js"></script>

  <!-- remodal -->
  <script src="/script/remodal.min.js"></script>

</body>

</html>