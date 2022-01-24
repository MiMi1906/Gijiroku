<?php
header("Content-type: application/json; charset=UTF-8");

require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

$count = $_POST["count"];

session_start();

// データベース接続
$db = dbConnect();

$tpl = new Template();

$sql = 'SELECT m.name, m.image, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.type = 1 AND p.reply_post_id = 0 ORDER BY p.id DESC LIMIT :end_num OFFSET :start_num';
$stmt = $db->prepare($sql);
$stmt->bindValue(':start_num', $count);
$stmt->bindValue(':end_num', $count + 20);
$stmt->execute();

$posts = $stmt;

$obj = [];

foreach ($posts as $post) {
  $post['message'] = mb_strimwidth($post['message'], 0, 200, '…', 'UTF-8');
  $tpl->setValue_tpl_message($post);
  $obj[] = $tpl->show(TPL_MESSAGE);
}

echo json_encode($obj);

exit;
