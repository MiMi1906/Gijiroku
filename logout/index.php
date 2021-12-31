<?php
session_start();

setcookie('email', '', time() - 3600, '/');
setcookie('password', '', time() - 3600, '/');
setcookie('save', '', time() - 3600, '/');
setcookie(session_name(), '', time() - 3600, '/');

$_SESSION = array();

session_destroy();

header('Location: /login/');
exit();
