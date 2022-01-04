<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

// データベース接続
$db = dbConnect();

$tpl = new Template();

if (empty($_REQUEST['id'])) {
  header('Location: /');
  exit();
}

$sql = 'SELECT m.name, m.image, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.member_id = :member_id ORDER BY p.created DESC';
$posts = $db->prepare($sql);
$posts->bindValue(':member_id', $_REQUEST['id']);
$posts->execute();

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
  $tpl->show(TPL_HEADER_BAR);
  ?>
  <main class="main" id="main">
    <form action="" autocomplete="off"><input type="hidden" id="count" name="" value="0"></form>
    <?php
    $sql = 'SELECT * FROM members WHERE id = :id';
    $profile = $db->prepare($sql);
    $profile->bindValue(':id', $_REQUEST['id']);
    $profile->execute();
    foreach ($profile as $m) {
      $member = $m;
    }
    ?>
    <div class="profile">
      <div class="profile_content">
        <div class="profile_heading">
          <div class="profile_image">
            <img src="/resource/image/icon/<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>">
          </div>
          <div class="profile_name">
            <?php echo $member['name']; ?>
          </div>
        </div>
        <div class="profile_exp">
          Lorem ipsum dolor sit amet consectetur adipisicing elit.<br>
          Animi porro odit totam quaerat. Recusandae sapiente maxime voluptatibus, <br>
          nostrum aperiam sed error totam, tenetur adipisci illo voluptatum.<br>
          Beatae, omnis quo! Deleniti?<br>
        </div>
        <div class="profile_follow_group">
          <div class="profile_follow_list">
            <span class="follow_num">123</span>フォロー
          </div>
          <div class="profile_follow_list">
            <span class="follow_num">123</span>サポート
          </div>
          <div class="profile_follow_list">
            <span class="follow_num">123</span>フォロワー
          </div>
          <div class="follow_btn">
            <input type="button" value="フォロー">
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="message_list" id="message_list">
        <!-- メッセージをここに追加 -->
      </div>
    </div>
  </main>
  <?php
  $tpl->show(TPL_FOOTER_BAR);
  ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="/script/ajax_add_content.js">
  </script>
  <script src="/script/nice.js"></script>
  <script src="/script/show_thread.js"></script>
</body>

</html>