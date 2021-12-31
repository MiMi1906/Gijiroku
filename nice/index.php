<?php
header('Content-type: text/plain; charset= UTF-8');

$id = filter_input(INPUT_POST, 'id');

require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

$db = dbConnect();

if (!empty($id)) {
  $sql = "SELECT COUNT( * ) AS cnt FROM nice WHERE member_id = :member_id AND like_post_id = :like_post_id";
  $check = $db->prepare($sql);
  $check->bindValue(':member_id', $_SESSION['id']);
  $check->bindValue(':like_post_id', $id);
  $check->execute();
  $like_cnt = $check->fetchColumn();
  $like_obj = [];
  $like_obj['like_cnt'] = 0;

  if ($like_cnt == 0) {
    $sql = "INSERT INTO nice(member_id, like_post_id, created) VALUES(:member_id, :like_post_id, :created)";
    $like = $db->prepare($sql);
    $like->bindValue(':member_id', $_SESSION['id']);
    $like->bindValue(':like_post_id', $id);
    $like->bindValue(':created', date('Y/m/d H:i:s'));
    $like->execute();
    $like_obj['inc_dec'] = 'inc';
  } else {
    $sql = "DELETE FROM nice WHERE member_id = :member_id AND like_post_id = :like_post_id";
    $check = $db->prepare($sql);
    $check->bindValue(':member_id', $_SESSION['id']);
    $check->bindValue(':like_post_id', $id);
    $check->execute();
    $like_obj['inc_dec'] = 'dec';
  }
  $sql = 'SELECT COUNT( like_post_id ) AS cnt FROM nice WHERE like_post_id = :like_post_id';
  $likes = $db->prepare($sql);
  $likes->bindValue(':like_post_id', $id);
  $likes->execute();
  $like_cnt = $likes->fetchColumn();
  $like_obj['like_cnt'] = $like_cnt;
  echo json_encode($like_obj);
} else {
  echo 'Ajaxエラー: 接続エラー';
}

exit();
