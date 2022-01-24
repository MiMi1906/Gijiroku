<?php
setlocale(LC_ALL, 'ja_JP.UTF-8');

require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

$db = dbConnect();


$hFile = fopen('../data_list.csv', "r");
while ($d = fgetcsv($hFile)) {
  $name = $d[0];
  if ($d[7] == 'empty') {
    $image = '/resource/image/icon/default.png';
  } else {
    $image = $d[7];
  }
  $bio = '';
  $bio .= $d[1] . "\n";
  $bio .= $d[2] . "\n";
  $bio .= $d[3] . ' ';
  $bio .= $d[4] . "\n";
  $bio .= $d[5];
  $sql = 'INSERT INTO members(name, image, bio, created, flag) VALUES(:name, :image, :bio, :created, :flag)';
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':name', $name);
  $stmt->bindValue(':image', $image);
  $stmt->bindValue(':bio', $bio);
  $stmt->bindValue(':flag', 0);
  $stmt->bindValue(':created', date('Y/m/d H:i:s'));
  $stmt->execute();
}

fclose($hFile);

echo 'success';
exit();
