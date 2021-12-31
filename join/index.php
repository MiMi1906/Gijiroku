<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

if (!empty($_POST)) {
  // エラー項目の確認
  if ($_POST['name'] == '') {
    $error['name'] = 'blank';
  }
  if ($_POST['email'] == '') {
    $error['email'] = 'blank';
  } else {
  }
  if (strlen($_POST['password']) < 4) {
    $error['password'] = 'length';
  }
  if ($_POST['password'] == '') {
    $error['password'] = 'blank';
  }

  $fileName = $_FILES['image']['name'];
  if (!empty($fileName)) {
    $ext = substr($fileName, -3);
    if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
      $error['image'] = 'type';
    }
  }
  if (empty($error)) {
    $sql = 'SELECT COUNT(*) FROM members WHERE email = :email';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', $_POST['email']);
    $stmt->execute();
    $record = $stmt->fetchColumn();
    if ($record > 0) {
      $error['email'] = 'duplicate';
    }
  }

  if (empty($error)) {
    // 画像をアップロードする
    $image = date('YmdHis') . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], '../resource/image/icon/' . $image);
    $_SESSION['join'] = $_POST;
    $_SESSION['join']['image'] = $image;
    header('Location: /join/check/');
    exit();
  }
}

if (!empty($_GET) && $_GET['action'] == 'rewrite') {
  $_POST = $_SESSION['join'];
  $error['rewrite'] = true;
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
  <p>次のフォームに必要事項をご記入ください。</p>
  <form action="" method="post" enctype="multipart/form-data">
    <label for="">名前</label><br>
    <input type="text" name="name" id="" value="<?php if (!empty($_POST['name'])) echo h($_POST['name']); ?>"><br>
    <?php if (!empty($error['name']) && $error['name'] == 'blank') : ?>
      *ニックネームを入力してください<br>
    <?php endif; ?>
    <label for="">メールアドレス</label><br>
    <input type="email" name="email" id="" value="<?php if (!empty($_POST['email'])) echo h($_POST['email']); ?>"><br>
    <?php if (!empty($error['email']) && $error['email'] == 'blank') : ?>
      *メールアドレスを入力してください<br>
    <?php endif; ?>
    <?php if (!empty($error['email']) && $error['email'] == 'duplicate') : ?>
      *登録されたメールアドレスはすでに登録されています<br>
    <?php endif; ?>
    <label for="">パスワード</label><br>
    <input type="password" name="password" id=""><br>
    <?php if (!empty($error['password']) && $error['password'] == 'blank') : ?>
      *パスワードを入力してください<br>
    <?php endif; ?>
    <?php if (!empty($error['password']) && $error['password'] == 'length') : ?>
      *パスワードは4文字以上で入力してください<br>
    <?php endif; ?>
    <label for="">アイコン用写真</label><br>
    <?php if (!empty($error['image']) && $error['image'] == 'type') : ?>
      *写真などは「.gif」「.jpg」「.png」の画像を指定してください<br>
    <?php endif; ?>
    <?php if (!empty($error)) : ?>
      *恐れ入りますが、画像を改めて指定してください<br>
    <?php endif; ?>
    <input type="file" name="image" id=""><br>
    <input type="submit" value="入力内容を確認する">
  </form>
</body>

</html>