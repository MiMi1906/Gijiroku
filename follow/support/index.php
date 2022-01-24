<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

if (empty($_POST['support_id'])) {
  header('Location: /');
  exit();
}

// データベース接続
$db = dbConnect();

if (empty($_POST['delete_id'])) {
  $sql = 'INSERT INTO support(member_id, support_id) VALUES(:member_id, :support_id)';
} else {
  $sql = 'DELETE FROM support WHERE member_id = :member_id AND support_id = :support_id';
}
$stmt = $db->prepare($sql);
$stmt->bindValue(':member_id', $_SESSION['id']);
$stmt->bindValue(':support_id', $_POST['support_id']);
$stmt->execute();

header('Location: /profile/?id=' . $_POST['support_id']);
exit();
