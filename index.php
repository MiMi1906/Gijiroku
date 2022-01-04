<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

$tpl = new Template();

if (!empty($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  // ログインしている
  $_SESSION['time'] = time();

  $sql = 'SELECT * FROM members WHERE id = :id';
  $members = $db->prepare($sql);
  $members->bindValue(':id', $_SESSION['id']);
  $members->execute();
  $member = $members->fetch();
} else {
  // ログインしていない
  header('Location: /logout/');
  exit();
}

if (!empty($_POST)) {
  if ($_POST['message'] != '') {
    $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :created)';
    $message = $db->prepare($sql);
    $reply_thread_name = '';
    if (!empty($_POST['reply_post_id'])) {
      $sql = 'SELECT * FROM posts WHERE id = :id';
      $thread_reply = $db->prepare($sql);
      $thread_reply->bindValue(':id', $_POST['reply_post_id']);
      $thread_reply->execute();
      $thread_id = $thread_reply->fetch();
      $message->bindValue(':thread_id', $thread_id['thread_id']);
      $message->bindValue(':reply_post_id', $_POST['reply_post_id']);
      $message->bindValue(':quote_from_id', 0);
      $reply_thread_name = $_POST['reply_thread_name'];
    } else if (!empty($_POST['quote_post_id'])) {
      $quote_name = $_POST['quote_name'];
      $message->bindValue(':reply_post_id', 0);
      $sql = 'SELECT MAX(thread_id) AS max FROM posts';
      $message->bindValue(':quote_from_id', $_POST['quote_post_id']);
      $thread_max = $db->prepare($sql);
      $thread_max->execute();
      $thread_id = $thread_max->fetchColumn();
      $message->bindValue(':thread_id', $thread_id + 1);
    } else {
      $sql = 'SELECT MAX(thread_id) AS max FROM posts';
      $thread_max = $db->prepare($sql);
      $thread_max->execute();
      $thread_id = $thread_max->fetchColumn();
      $message->bindValue(':thread_id', $thread_id + 1);
      $message->bindValue(':quote_from_id', 0);
      $message->bindValue(':reply_post_id', 0);
    }
    $message->bindValue(':nice_num', 0);
    $message->bindValue(':member_id', $member['id']);
    $message->bindValue(':message', nl2br($reply_thread_name . h($_POST['message']) . str_replace(PHP_EOL, "", $quote_name)));
    $message->bindValue(':created', date('Y/m/d H:i:s'));

    $message->execute();

    header('Location: /');
    exit();
  }
}

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
  $message = '<a href="/profile/?id=' . $table['member_id'] . '">@' . $table['name'] . '</a> の投稿の引用<br>' . $table['message'];
  $quote_str = '<div class="name">' . $table['name'] . '</div>' . $table['message'];
  $quote_str = preg_replace(
    '|<a href="\/thread\/\?thread_id=(.*?)&emph_id=(.*?)#(.*?)"><object><div class="quote_exp">(.*?)</div></object></a>|',
    '<br>' . '<object><a href="/thread/?thread_id=\1&emph_id=\2#\3"><span>' .  $_SERVER['HTTP_HOST'] . '/thread/?thread_id=\1</span></a></object>',
    $quote_str
  );
  $message = preg_replace(
    '|<a href="\/thread\/\?thread_id=(.*?)&emph_id=(.*?)#(.*?)"><object><div class="quote_exp">(.*?)</div></object></a>|',
    '<br>' . '<object><a href="/thread/?thread_id=\1&emph_id=\2#\3"><span>' .  $_SERVER['HTTP_HOST'] . '/thread/?thread_id=\1</span></a></object>',
    $message
  );
  $quote_html = '<a href="/thread/?thread_id=' . $table['thread_id'] . '&emph_id=' . $table['id'] . '#' . $table['id'] . '"><object><div class="quote_exp">' . $quote_str . '</div></object></a>';
}
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

  <div class=" send_window remodal" data-remodal-id="send_window">
    <form action="/" method="post">
      <div class="res_detail">
        <?php if (!empty($message)) echo $message; ?>
      </div>
      <textarea name="message" id="" class="message" placeholder="意見を投稿しよう"></textarea>
      <input type="hidden" name="reply_post_id" value="<?php if (!empty($_REQUEST['res'])) echo h($_REQUEST['res']); ?>">
      <input type="hidden" name="reply_thread_name" value="<?php if (!empty($res_html)) echo h($res_html); ?>">
      <input type="hidden" name="quote_post_id" value="<?php if (!empty($_REQUEST['quote'])) echo h($_REQUEST['quote']); ?>">
      <input type="hidden" name="quote_name" value="<?php if (!empty($quote_html)) echo h($quote_html); ?>">
      <div class="submit">
        <input type="submit" value="投稿する">
      </div>
    </form>
  </div>

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