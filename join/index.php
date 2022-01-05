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

  if (strpos($_POST['name'], ' ')) {
    $error['name'] = 'discrimination';
  }

  if (empty($error['name'])) {
    $sql = 'SELECT COUNT(*) FROM members WHERE name = :name';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $_POST['name']);
    $stmt->execute();
    $record = $stmt->fetchColumn();
    if (
      $record > 0
    ) {
      $error['name'] = 'duplicate';
    }
  }

  if (empty($error['email'])) {
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
  <link href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/general.css">
  <title>議事録アプリ</title>
</head>

<body>
  <div class="login_form_background">
    <div class="content login_form join_form">
      <div class="logo">Gijiroku</div>
      <div class="exp">メンバー登録</div>
      <form action="" method="post" enctype="multipart/form-data">
        <div class="label">ニックネーム</div>
        <input type="text" name="name" placeholder="Gijiroku" id="" class="login_form_input" value="<?php if (!empty($_POST['name'])) echo h($_POST['name']); ?>">
        <?php if (!empty($error['name']) && $error['name'] == 'blank') : ?>
          <div class="error">
            ニックネームを入力してください
          </div>
        <?php endif; ?>
        <?php if (!empty($error['name']) && $error['name'] == 'duplicate') : ?>
          <div class="error">
            このニックネームはすでに登録されています
          </div>
        <?php endif; ?>
        <?php if (!empty($error['name']) && $error['name'] == 'discrimination') : ?>
          <div class="error">
            ニックネームにスペースは含めません
          </div>
        <?php endif; ?>
        <div class="label">メールアドレス</div>
        <input type="email" name="email" placeholder="gijiroku@example.com" id="" class="login_form_input" value=" <?php if (!empty($_POST['email'])) echo h($_POST['email']); ?>">
        <?php if (!empty($error['email']) && $error['email'] == 'blank') : ?>
          <div class="error">
            メールアドレスを入力してください
          </div>
        <?php endif; ?>
        <?php if (!empty($error['email']) && $error['email'] == 'duplicate') : ?>
          <div class="error">
            このメールアドレスはすでに登録されています
          </div>
        <?php endif; ?>
        <div class="label">パスワード</div>
        <input type="password" name="password" placeholder="Password" id="" class="login_form_input">
        <?php if (!empty($error['password']) && $error['password'] == 'blank') : ?>
          <div class="error">
            パスワードを入力してください
          </div>
        <?php endif; ?>
        <?php if (!empty($error['password']) && $error['password'] == 'length') : ?>
          <div class="error">
            パスワードは4文字以上で入力してください
          </div>
        <?php endif; ?>
        <div class="label">アイコン画像</div>
        <label class="file_input_btn">
          <input type="file" name="image" class="file_input" accept="image/*"><span class="file_name">ファイルを選択</span>
        </label>
        <div class="file_input_alert">
          <?php if (!empty($error['image']) && $error['image'] == 'type') : ?>
            <div class="error">.gif, .jpg, .png の画像を指定してください</div>
          <?php elseif (!empty($error) && empty($error['image'])) : ?>
            <div class="error">もう一度選択してください</div>
          <?php else : ?>
            選択されていません
          <?php endif; ?>
        </div>
        <input type="submit" class="submit_btn" value="入力内容を確認">
      </form>
      <div class="login">
        アカウントをお持ちの方は<a href="/login/">ログイン</a>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="/script/file_input.js"></script>

</body>

</html>