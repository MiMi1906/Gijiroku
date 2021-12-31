<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

if (empty($_SESSION['join'])) {
  header('Location: /join/');
  exit();
}

if (!empty($_POST)) {
  // 登録処理をする
  $sql = 'INSERT INTO members(name, email, password, image, created) VALUES(:name, :email, :password, :image, :created)';
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':name', $_SESSION['join']['name']);
  $stmt->bindValue(':email', $_SESSION['join']['email']);
  $stmt->bindValue(':password', sha1($_SESSION['join']['password']));
  $stmt->bindValue(':image', $_SESSION['join']['image']);
  $stmt->bindValue(':created', date('Y/m/d H:i:s'));
  $stmt->execute();

  unset($_SESSION['join']);

  header('Location: /join/complete/');
  exit();
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>議事録アプリ</title>
</head>

<body>
  <p>記載した内容を確認して、「登録する」ボタンをクリックしてください。</p>
  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="submit">
    <label for="">名前</label><br>
    <?php echo h($_SESSION['join']['name']); ?><br>
    <label for="">メールアドレス</label><br>
    <?php echo h($_SESSION['join']['email']); ?><br>
    <label for="">パスワード</label><br>
    【表示されません】<br>
    <label for="">アイコン用写真</label><br>
    <img src="/resource/image/icon/<?php echo h($_SESSION['join']['image']) ?>" width="100" height="100" alt=""><br>
    <a href="/join/?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する">
  </form>
</body>

</html>