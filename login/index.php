<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

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
  <title>議事録アプリ</title>
</head>

<body>
  <p>メールアドレスとパスワードを入力してログインしてください。</p>
  <p>ユーザー登録がまだの方はこちらからどう。</p>
  <p>&raquo;<a href="/join/">ユーザー登録をする</a></p>
  <form action="" method="post">
    <?php if (!empty($error['login']) && $error['login'] == 'blank') : ?>
      <p>* メールアドレスとパスワードを入力してください</p>
    <?php endif; ?>
    <?php if (!empty($error['login']) && $error['login'] == 'failed') : ?>
      <p>* メールアドレスかパスワードが間違っています</p>
    <?php endif; ?>
    <label for="">メールアドレス</label><br>
    <input type="email" name="email" id="" value="<?php if (!empty($_POST['email'])) echo h($_POST['email']) ?>"><br>
    <label for="">パスワード</label><br>
    <input type="password" name="password" id=""><br>
    <label for="">ログイン情報の記録</label><br>
    <input type="checkbox" name="save" id="" value="on"> 次回からは自動的にログインする<br>
    <input type="submit" value="ログインする">
  </form>
</body>

</html>