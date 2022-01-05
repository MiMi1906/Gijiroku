<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

$tpl = new Template();

if (!empty($_COOKIE['email'])) {
  $_POST['email'] = $_COOKIE['email'];
  $_POST['password'] = $_COOKIE['password'];
  $_POST['save'] = 'on';
}

// データを受け取った時
if (!empty($_POST)) {
  if ($_POST['email'] !== '' && $_POST['password'] !== '') {
    $sql = 'SELECT * FROM members WHERE email = :email AND password = :password';
    $login = $db->prepare($sql);
    $login->bindValue(':email', $_POST['email']);
    $login->bindValue(':password', sha1($_POST['password']));
    $login->execute();

    $member = $login->fetch();

    if ($member) {
      // ログイン成功
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();

      if (!empty($_POST['save']) && $_POST['save'] == 'on') {
        // ログイン情報を記録する
        setcookie('email', $_POST['email'], time() + 60 * 60 * 24 * 14, "/");
        setcookie('password', $_POST['password'], time() + 60 * 60 * 24 * 14, "/");
        setcookie('save', 'on', time() + 60 * 60 * 24 * 14, "/");
      }

      header('Location: /');
      exit();
    } else {
      $error['login'] = 'failed';
    }
  } else {
    $error['login'] = 'blank';
  }
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
    <div class="content login_form">
      <div class="logo">Gijiroku</div>
      <form action="" method="post">
        <?php if (!empty($error['login']) && $error['login'] == 'blank') : ?>
          <div class="error">メールアドレスとパスワードを入力してください</div>
        <?php endif; ?>
        <?php if (!empty($error['login']) && $error['login'] == 'failed') : ?>
          <div class="error">メールアドレスかパスワードが間違っています</div>
        <?php endif; ?>
        <div class="label">メールアドレス</div>
        <input type="email" name="email" placeholder="メールアドレス" class="login_form_input" id="" value="<?php if (!empty($_POST['email'])) echo h($_POST['email']) ?>"><br>
        <div class="label">パスワード</div>
        <input type="password" name="password" placeholder="パスワード" id="" class="login_form_input"><br>
        <input type="hidden" name="save" id="" value="on">
        <input type="submit" class="submit_btn" value="ログインする">
      </form>
      <div class="join">
        アカウントをお持ちでない方は<a href="/join/">こちら</a>
      </div>
    </div>
  </div>
</body>

</html>