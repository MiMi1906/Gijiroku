<?php
session_start();

setcookie('email', '', time() - 3600, '/');
setcookie('password', '', time() - 3600, '/');
setcookie('save', '', time() - 3600, '/');
setcookie(session_name(), '', time() - 3600, '/');

$_SESSION = array();

session_destroy();
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
      <div class="exp">ログアウトしました</div>
      <form action="/login/" method="post">
        <input type="submit" class="submit_btn" value="ログインする">
      </form>
    </div>
  </div>
</body>