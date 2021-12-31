<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

if (!empty($_SESSION['id'])) {
  $id = $_REQUEST['id'];

  // 投稿を検査する
  $sql = 'SELECT * FROM posts WHERE id = :id';
  $messages = $db->prepare($sql);
  $messages->bindValue(':id', $id);
  $messages->execute();
  $message = $messages->fetch();

  if ($message['member_id'] === $_SESSION['id']) {
    // 削除する
    $sql = 'DELETE FROM posts WHERE id = :id';
    $del = $db->prepare($sql);
    $del->bindValue(':id', $id);
    $del->execute();
  }
}

header('Location: /');
exit();
