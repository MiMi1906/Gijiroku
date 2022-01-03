<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

$tpl = new Template();

if (empty($_REQUEST['thread_id'])) {
  header('Location: /');
  exit();
}

$sql = 'SELECT m.name, m.image, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.thread_id = :thread_id ORDER BY p.created ASC';
$posts = $db->prepare($sql);
$posts->bindValue(':thread_id', $_REQUEST['thread_id']);
$posts->execute();

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" rel="stylesheet">

  <link rel="stylesheet" href="/css/general.style.css">
  <title>議事録アプリ</title>
</head>

<body>
  <?php
  $tpl->show(TPL_HEADER_BAR);
  ?>
  <main class="main">
    <div class="content">
      <?php if (!empty($posts)) {
        foreach ($posts as $post) {
          $tpl = new Template();
          $tpl->setValue_tpl_message($post);
          $tpl->show(TPL_MESSAGE);
        }
      } else {
        print('<p>この投稿は削除されたか、URLが間違っています</p>');
      }
      ?>
    </div>
  </main>
  <?php
  $tpl->show(TPL_FOOTER_BAR);
  ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="/script/thread.script.js"></script>
</body>

</html>