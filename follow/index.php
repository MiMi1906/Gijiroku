<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

if (empty($_POST['follow_id'])) {
  header('Location: /');
  exit();
}

// データベース接続
$db = dbConnect();

if (empty($_POST['delete_id'])) {
  $sql = 'INSERT INTO follow(member_id, follow_id) VALUES(:member_id, :follow_id)';
} else {
  $sql = 'DELETE FROM follow WHERE member_id = :member_id AND follow_id = :follow_id';
}
$stmt = $db->prepare($sql);
$stmt->bindValue(':member_id', $_SESSION['id']);
$stmt->bindValue(':follow_id', $_POST['follow_id']);
$stmt->execute();

header('Location: /profile/?id=' . $_POST['follow_id']);
exit();
