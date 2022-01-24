<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

loginCheck();

// データベース接続
$db = dbConnect();

$tpl = new Template();

$sql = 'SELECT * FROM members WHERE id = :id';
$members = $db->prepare($sql);
$members->bindValue(':id', $_SESSION['id']);
$members->execute();
$member = $members->fetch();
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

  <title>議事録一覧 / Gijiroku</title>
</head>

<body>

  <?php
  $tpl->setValue_tpl_header('議事録一覧');
  $tpl->show(TPL_HEADER_BAR);
  ?>

  <main class="main" id="main">
    <form action="" autocomplete="off"><input type="hidden" id="count" name="" value="0"></form>
    <div class="content">
      <div class="send">
        <div class="submit">
          <a href="/#send_window">
            <button type="submit">
              <i class="fas fa-comment-alt"></i>
            </button>
          </a>
        </div>
      </div>
      <div class="message_list" id="message_list">
        <!-- メッセージをここに追加 -->
      </div>
    </div>
  </main>

  <?php
  $tpl->show(TPL_FOOTER_BAR);
  ?>

  <!-- script -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <script src="/script/ajax_add_content.js"></script>
  <script src="/script/nice.js"></script>
  <script src="/script/show_thread.js"></script>

  <!-- remodal -->
  <script src="/script/remodal.min.js"></script>

</body>

</html>