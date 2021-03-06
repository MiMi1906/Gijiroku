<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

session_start();

loginCheck();

// データベース接続
$db = dbConnect();

$tpl = new Template();

if (empty($_REQUEST['id'])) {
  header('Location: /');
  exit();
}
$sql = 'SELECT * FROM members WHERE id = :id';
$profile = $db->prepare($sql);
$profile->bindValue(':id', $_REQUEST['id']);
$profile->execute();
foreach ($profile as $m) {
  $member = $m;
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
  <title><?php echo $member['name']; ?> / Gijiroku</title>
</head>

<body>

  <?php $tpl->setValue_tpl_header($member['name']);
  $tpl->show(TPL_HEADER_BAR); ?>
  <main class="main" id="main">
    <form action="" autocomplete="off"><input type="hidden" id="count" name="" value="0"></form>

    <div class="profile">
      <div class="profile_content">
        <div class="profile_heading">
          <div class="profile_image">
            <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>">
          </div>
          <div class="profile_name">
            <div class="profile_name_text">
              <?php echo $member['name']; ?>
            </div>
          </div>
        </div>
        <div class="profile_exp">
          <?php echo nl2br(h($member['bio'])); ?>
        </div>
        <div class="profile_follow_group">
          <?php if ($member['flag'] == 1) : ?>
            <div class="profile_follow_list">
              <?php
              $sql = 'SELECT COUNT(*) AS follow_cnt FROM follow WHERE member_id = :member_id';
              $stmt = $db->prepare($sql);
              $stmt->bindValue(':member_id', $member['id']);
              $stmt->execute();
              $follow = $stmt->fetchColumn();
              ?>
              <a href="/member/?member_id=<?php echo $member['id']; ?>&flag=follows" class="follows"><span class="follow_num"><?php echo $follow; ?></span> フォロー</a>
            </div>
            <div class="profile_follow_list">
              <?php
              $sql = 'SELECT COUNT(*) AS support_cnt FROM support WHERE member_id = :member_id';
              $stmt = $db->prepare($sql);
              $stmt->bindValue(':member_id', $member['id']);
              $stmt->execute();
              $support = $stmt->fetchColumn();
              ?>
              <a href="/member/?member_id=<?php echo $member['id']; ?>&flag=supports" class="follows"><span class="follow_num"><?php echo $support; ?></span> サポート</a>
            </div>
            <div class="profile_follow_list">
              <?php
              $sql = 'SELECT COUNT(*) AS follow_cnt FROM follow WHERE follow_id = :follow_id';
              $stmt = $db->prepare($sql);
              $stmt->bindValue(':follow_id', $member['id']);
              $stmt->execute();
              $follower = $stmt->fetchColumn();
              ?>
              <a href="/member/?member_id=<?php echo $member['id']; ?>&flag=followers" class="follows"><span class="follow_num"><?php echo $follower; ?></span> フォロワー</a>
            </div>
          <?php else : ?>
            <div class="profile_follow_list">
              <?php
              $sql = 'SELECT COUNT(*) AS support_cnt FROM support WHERE support_id = :support_id';
              $stmt = $db->prepare($sql);
              $stmt->bindValue(':support_id', $member['id']);
              $stmt->execute();
              $supporter = $stmt->fetchColumn();
              ?>
              <a href="/member/?member_id=<?php echo $member['id']; ?>&flag=supporters" class="follows"><span class="follow_num"><?php echo $supporter; ?></span> サポーター</a>
            </div>
          <?php endif; ?>
          <?php
          $sql = 'SELECT COUNT(*) AS follow_cnt FROM follow WHERE member_id = :member_id AND follow_id = :follow_id';
          $stmt = $db->prepare($sql);
          $stmt->bindValue(':member_id', $_SESSION['id']);
          $stmt->bindValue(':follow_id', $member['id']);
          $stmt->execute();
          $follow = $stmt->fetchColumn();

          $sql = 'SELECT COUNT(*) AS support_cnt FROM support WHERE member_id = :member_id AND support_id = :support_id';
          $stmt = $db->prepare($sql);
          $stmt->bindValue(':member_id', $_SESSION['id']);
          $stmt->bindValue(':support_id', $member['id']);
          $stmt->execute();
          $support = $stmt->fetchColumn();

          if ($_REQUEST['id'] != $_SESSION['id']) : ?>
            <?php if ($member['flag'] == 0) : ?>
              <?php if ($support == 0) : ?>
                <div class="follow_btn">
                  <form action="/follow/support/" method="post">
                    <input type="hidden" name="support_id" value="<?php echo $_REQUEST['id']; ?>">
                    <input type="submit" value="サポート">
                  </form>
                </div>
              <?php else : ?>
                <div class="follow_btn">
                  <form action="/follow/support/" method="post">
                    <input type="hidden" name="support_id" value="<?php echo $_REQUEST['id']; ?>">
                    <input type="hidden" name="delete_id" value="<?php echo $_REQUEST['id']; ?>">
                    <input type="submit" value="サポート解除">
                  </form>
                </div>
              <?php endif; ?>
            <?php else : ?>
              <?php if ($follow == 0) : ?>
                <div class="follow_btn">
                  <form action="/follow/" method="post">
                    <input type="hidden" name="follow_id" value="<?php echo $_REQUEST['id']; ?>">
                    <input type="submit" value="フォロー">
                  </form>
                </div>
              <?php else : ?>
                <div class="follow_btn">
                  <form action="/follow/" method="post">
                    <input type="hidden" name="follow_id" value="<?php echo $_REQUEST['id']; ?>">
                    <input type="hidden" name="delete_id" value="<?php echo $_REQUEST['id']; ?>">
                    <input type="submit" value="フォロー解除">
                  </form>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          <?php else : ?>
            <div class="follow_btn">
              <form action="/edit/" method="post">
                <input type="submit" value="編集">
              </form>
            </div>
          <?php endif; ?>
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