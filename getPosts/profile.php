<?php
header("Content-type: application/json; charset=UTF-8");

require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

$count = $_POST["count"];
$member_id = $_POST["member_id"];

session_start();

// データベース接続
$db = dbConnect();

$tpl = new Template();

$sql = 'SELECT m.name, m.image, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.member_id = :member_id ORDER BY p.id DESC LIMIT :start_num, :end_num';
$stmt = $db->prepare($sql);
$stmt->bindValue(':member_id', $member_id);
$stmt->bindValue(':start_num', $count);
$stmt->bindValue(':end_num', $count + 20);
$stmt->execute();

$posts = $stmt;

$obj = [];

foreach ($posts as $post) {
  $tpl->setValue_tpl_message($post);
  $obj[] = $tpl->show(TPL_MESSAGE);
}

echo json_encode($obj);
exit;
