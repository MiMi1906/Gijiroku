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
  $sql = 'INSERT INTO members(name, email, password, image, created, flag) VALUES(:name, :email, :password, :image, :created, :flag)';
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':name', $_SESSION['join']['name']);
  $stmt->bindValue(':email', $_SESSION['join']['email']);
  $stmt->bindValue(':password', sha1($_SESSION['join']['password']));
  $stmt->bindValue(':image', '/resource/image/icon/' . $_SESSION['join']['image']);
  $stmt->bindValue(':created', date('Y/m/d H:i:s'));
  $stmt->bindValue(':created', 1);
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
  <link href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/general.css">
  <title>議事録アプリ</title>
</head>

<body>
  <div class="login_form_background">
    <div class="content login_form join_form">
      <div class="logo">Gijiroku</div>
      <div class="exp">登録内容を確認</div>
      <form action="" method="post" enctype="multipart/form-data">

        <div class="label">ニックネーム</div>
        <input readonly type="text" name="name" placeholder="ニックネーム" id="" class="login_form_input" value="<?php echo h($_SESSION['join']['name']); ?>">
        <div class="label">メールアドレス</div>
        <input readonly type="email" name="email" placeholder="メールアドレス" id="" class="login_form_input" value="<?php echo h($_SESSION['join']['email']); ?>">
        <div class="label">パスワード</div>
        <input readonly type="text" name="password" placeholder="パスワード" id="" class="login_form_input" value="表示されません"><br>
        <div class="label">アイコン画像</div>
        <div class="input_file">
          <div class="image">
            <img src="/resource/image/icon/<?php echo h($_SESSION['join']['image']) ?>"><br>
          </div>
        </div>
        <div class="join">登録内容を修正したい場合は<a href="/join/?action=rewrite">こちら</a></div>
        <input type="submit" class="submit_btn" value="登録する">
      </form>
    </div>
  </div>
</body>

</html>