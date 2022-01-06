<?php
// func.php

define('TPL_MESSAGE', 1);
define('TPL_HEADER_BAR', 2);
define('TPL_FOOTER_BAR', 3);

define('MSG_TYPE_USER', 0);
define('MSG_TYPE_RECORD', 1);

// dbconnect func
// データベース接続 (SQLite3)
function dbConnect()
{
  $dbPath = $_SERVER['DOCUMENT_ROOT'] . '/database.db';
  try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo 'データベース接続エラー : ' . $e->getMessage();
  }
  return $db;
}

function loginCheck()
{
  if (empty($_SESSION['id']) || $_SESSION['time'] + 3600 < time()) {
    logout();
    header('Location: /login/');
    exit();
  } else {
    // ログインしている
    $_SESSION['time'] = time();
    return true;
  }
}

function logout()
{
  session_start();

  setcookie('email', '', time() - 3600, '/');
  setcookie('password', '', time() - 3600, '/');
  setcookie('save', '', time() - 3600, '/');
  setcookie(session_name(), '', time() - 3600, '/');

  $_SESSION = array();

  session_destroy();
}

// h func
// HTML特殊文字をそのまま表示
function h($string)
{
  if (!empty($string)) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', true);
  }
}

// fuzzyTime func
// 「何秒前」のように時刻を表示
function fuzzyTime($time_db)
{
  $unix   = strtotime($time_db);
  $now    = time();
  $diff_sec   = $now - $unix;

  if ($diff_sec < 60) {
    $time   = $diff_sec;
    $unit   = "秒前";
  } elseif ($diff_sec < 3600) {
    $time   = $diff_sec / 60;
    $unit   = "分前";
  } elseif ($diff_sec < 86400) {
    $time   = $diff_sec / 3600;
    $unit   = "時間前";
  } elseif ($diff_sec < 2764800) {
    $time   = $diff_sec / 86400;
    $unit   = "日前";
  } else {
    if (date("Y") != date("Y", $unix)) {
      $time   = date("Y年n月j日", $unix);
    } else {
      $time   = date("n月j日", $unix);
    }

    return $time;
  }

  return (int)$time . $unit;
}

// makeLink func
// URLにリンクを設置する
function url($string)
{
  return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2" target="_blank" rel="noopener noreferrer"><span>\1\2</span></a>', $string);
}

function user($string)
{
  return mb_ereg_replace("(?<=^|(?<=[^a-zA-Z0-9-_\.]))@(.[^<br>\s\t]+)", '<span>@\1</span>', $string);
}

function tag($string)
{
  return mb_ereg_replace("(?<=^|(?<=[^a-zA-Z0-9-_\.]))#(.[^<br>\s\t]+)", '<span>#\1</span>', $string);
}

function coloring($string)
{
  $string = user($string);
  $string = tag($string);
  $string = url($string);
  return $string;
}

class Template
{
  function setValue_tpl_message($postData)
  {
    $db = dbConnect();
    $this->id = $postData['id'];
    $this->name = $postData['name'];
    $this->image = $postData['image'];
    $this->message = $postData['message'];
    $this->member_id = $postData['member_id'];
    $this->reply_post_id = $postData['reply_post_id'];
    $this->nice_num = $postData['nice_num'];
    $this->thread_id = $postData['thread_id'];
    $this->type = $postData['type'];
    $this->created = $postData['created'];
    if ($this->nice_num == 0) {
      $this->nice_num = '';
    }
    $sql = 'SELECT * FROM nice WHERE like_post_id = :like_post_id AND member_id = :member_id';
    $likes = $db->prepare($sql);
    $likes->bindValue('like_post_id', $postData['id']);
    $likes->bindValue('member_id', $_SESSION['id']);
    $likes->execute();
    $like = $likes->fetch();
    $like_str = '';
    if ($like) {
      $like_str = '<i class="fas fa-heart"></i><span class="nice_cnt">' . $this->nice_num . '</span>';
    } else {
      $like_str = '<i class="far fa-heart"></i><span class="nice_cnt">' . $this->nice_num . '</span>';
    }
    $this->like_str = $like_str;
  }

  function setValue_tpl_header($heading)
  {
    $this->heading = $heading;
  }

  function show($tplType)
  {
    $v = $this;
    switch ($tplType) {
      case TPL_MESSAGE:
        $tplfile = 'message.tpl.php';
        ob_start();
        include($_SERVER['DOCUMENT_ROOT'] . "/tpl/{$tplfile}");
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
      case TPL_HEADER_BAR:
        $tplfile = 'header_bar.tpl.php';
        break;
      case TPL_FOOTER_BAR:
        $tplfile = 'footer_bar.tpl.php';
        break;
      default:
        break;
    }
    include($_SERVER['DOCUMENT_ROOT'] . "/tpl/{$tplfile}");
  }
}
