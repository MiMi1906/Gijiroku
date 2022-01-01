<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

$tpl = new Template();

// 返信
if (!empty($_REQUEST['res'])) {
  $sql = 'SELECT m.name, m.image, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.id = :id ORDER BY p.created DESC';
  $response = $db->prepare($sql);
  $response->bindValue(':id', $_REQUEST['res']);
  $response->execute();

  $table = $response->fetch();
  $message = '<a href="/profile/?id=' . $table['member_id'] . '">@' . $table['name'] . '</a> のスレッドへの返信';
  $res_html = '<div class="thread_exp">' . $message . '</div>';
}


// 引用
if (!empty($_REQUEST['quote'])) {
  $sql = 'SELECT m.name, m.image, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.id = :id ORDER BY p.created DESC';
  $response = $db->prepare($sql);
  $response->bindValue(':id', $_REQUEST['quote']);
  $response->execute();

  $table = $response->fetch();
  $message = $table['message'] . "\n";
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

  <main class="main">
    <div class="content">
      <div class="send">
        <form action="/" method="post">
          <div class="res_detail">
            <?php if (!empty($message)) echo $message ?>
          </div>
          <textarea name="message" id="" class="message" placeholder="意見を投稿しよう"></textarea>
          <input type="hidden" name="reply_post_id" value="<?php if (!empty($_REQUEST['res'])) echo h($_REQUEST['res']); ?>">
          <input type="hidden" name="reply_thread_name" value="<?php if (!empty($res_html)) echo h($res_html); ?>">
          <input type="hidden" name="quote" value="<?php if (!empty($_REQUEST['quote'])) echo h($_REQUEST['quote']); ?>">
          <div class="submit">
            <input type="submit" value="投稿する">
          </div>
        </form>
      </div>
    </div>
  </main>

  <?php
  $tpl->show(TPL_FOOTER_BAR);
  ?>

  <!-- script -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <!-- index.script -->
  <script src="/script/index.script.js"></script>
</body>

</html>