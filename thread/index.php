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
  <main class="main" id="main">
    <form action="" autocomplete="off"><input type="hidden" id="count" name="" value="0"></form>
    <div class="content">
      <div class="message_list" id="message_list">
        <!-- メッセージをここに追加 -->
      </div>
    </div>
  </main>
  <?php
  $tpl->show(TPL_FOOTER_BAR);
  ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="/script/ajax_add_content.js"></script>
  <script src="/script/nice.js"></script>
</body>

</html>