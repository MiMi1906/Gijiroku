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
    $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :created)';
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
      $reply_thread_name = $_POST['reply_thread_name'];
    } else {
      $sql = 'SELECT MAX(thread_id) AS max FROM posts';
      $thread_max = $db->prepare($sql);
      $thread_max->execute();
      $thread_id = $thread_max->fetchColumn();
      $message->bindValue(':thread_id', $thread_id + 1);
      $message->bindValue(':reply_post_id', 0);
    }
    $message->bindValue(':member_id', $member['id']);
    $message->bindValue(':message', nl2br($reply_thread_name . h($_POST['message'])));
    $message->bindValue(':created', date('Y/m/d H:i:s'));
    $message->execute();

    header('Location: /');
    exit();
  }
}

// 投稿を取得する
$sql = 'SELECT m.name, m.image, p.* FROM members m, posts p WHERE m.id = p.member_id ORDER BY p.created DESC';
$posts = $db->query($sql);

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
        <form action="/post/" method="post">
          <input type="hidden" name="message" value="<?php if (!empty($message)) echo h($message); ?>">
          <input type="hidden" name="reply_post_id" value="<?php if (!empty($_REQUEST['res'])) echo h($_REQUEST['res']); ?>">
          <input type="hidden" name="reply_thread_name" value="<?php if (!empty($res_html)) echo h($res_html); ?>">
          <input type="hidden" name="quote" value="<?php if (!empty($_REQUEST['quote'])) echo h($_REQUEST['quote']); ?>">
          <div class="submit">
            <button type="submit">
              <i class="fas fa-comment-alt"></i>
            </button>
          </div>
        </form>
      </div>

      <div class="message_list">
        <?php
        foreach ($posts as $post) {
          $tpl->setValue_tpl_message($post);
          $tpl->show(TPL_MESSAGE);
        }
        ?>
      </div>
    </div>

  </main>

  <?php
  $tpl->show(TPL_FOOTER_BAR);
  ?>

  <!-- script -->
  <!-- emoji-button -->
  <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <!-- index.script -->
  <script src="/script/index.script.js"></script>
</body>

</html>