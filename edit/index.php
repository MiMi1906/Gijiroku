<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

loginCheck();

$tpl = new Template();

// データベース接続
$db = dbConnect();

$sql = 'SELECT * FROM members WHERE id = :id';
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $_SESSION['id']);
$stmt->execute();
$profile = $stmt->fetch();

if (!empty($_POST)) {
  // エラー項目の確認
  if ($_POST['name'] == '') {
    $error['name'] = 'blank';
  }
  if ($_POST['email'] == '') {
    $error['email'] = 'blank';
  }
  if (!empty($_FILES['image']['name'])) {
    $fileName = $_FILES['image']['name'];
    $ext = substr($fileName, -3);
    if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
      $error['image'] = 'type';
    }
  }
  if (strlen($_POST['bio']) > 160) {
    $error['bio'] = 'large';
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
      $record > 1
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
    if ($record > 1) {
      $error['email'] = 'duplicate';
    }
  }

  if (empty($error)) {
    // 画像をアップロードする
    if (!empty($_FILES['image']['name'])) {
      $image = date('YmdHis') . $_FILES['image']['name'];
      move_uploaded_file($_FILES['image']['tmp_name'], '../resource/image/icon/' . $image);
      $sql =  "UPDATE members SET name = :name, email = :email, image = :image, bio = :bio WHERE id = :id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(
        ':image',
        '/resource/image/icon/' . $image
      );
    } else {
      $sql = "UPDATE members SET name = :name, email = :email, bio = :bio WHERE id = :id";
      $stmt = $db->prepare($sql);
    }
    $stmt->bindValue(':name', $_POST['name']);
    $stmt->bindValue(':email', $_POST['email']);
    $stmt->bindValue(':bio', $_POST['bio']);
    $stmt->bindValue(':id', $_SESSION['id']);
    $stmt->execute();
    header('Location: /edit/');
    exit();
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
  <?php
  $tpl->setValue_tpl_header('プロフィールを編集');
  $tpl->show(TPL_HEADER_BAR);
  ?>
  <div class="main">
    <div class="content login_form">
      <form action="" method="post" enctype="multipart/form-data">
        <div class="label">ニックネーム</div>
        <input type="text" name="name" placeholder="Gijiroku" id="" class="login_form_input" value="<?php if (!empty($profile['name'])) echo h($profile['name']); ?>">
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
        <input type="email" name="email" placeholder="gijiroku@example.com" id="" class="login_form_input" value="<?php if (!empty($profile['email'])) echo h($profile['email']); ?>">
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
        <div class="label">アイコン画像</div>
        <div class="input_file">
          <div class="image">
            <img src="<?php if (!empty($profile['image'])) echo h($profile['image']) ?>"><br>
          </div>
        </div>
        <label class="file_input_btn">
          <input type="file" name="image" class="file_input" accept="image/*"><span class="file_name">アイコンを変更</span>
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
        <div class="label">プロフィール欄</div>
        <textarea name="bio" id="" class="bio" placeholder="紹介文"><?php if (!empty($profile['bio'])) {
                                                                    echo $profile['bio'];
                                                                  } ?></textarea>
        <?php if (!empty($error['bio']) && $error['bio'] == 'large') : ?>
          <div class="error">160字以内で入力してください</div>
        <?php endif; ?>
        <input type="submit" class="submit_btn" value="更新する">
      </form>
    </div>
  </div>
  <?php
  $tpl->show(TPL_FOOTER_BAR);
  ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="/script/file_input.js"></script>
</body>

</html>