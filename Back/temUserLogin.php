<?php
require "superHeader.php";
$username = addslashes(sprintf("%s", $_GET['username']));
$username = substr($username, 0, 15);
$user = $sql->getUserInfByUsername($username);
if (!$user['username']) stdJqReturn(-1);
if ($user['lvl'] > 1) stdJqReturn(-2);
if (!$token->temTokenOverTimeCheck($username)) stdJqReturn(-3);
if ($user['password']) {
    if ($_SESSION['captcha'] != $_POST['captcha']) stdJqReturn(-4);
    if ($user['password'] != $_POST['password']) stdJqReturn(-5);
}
$sql->createChat($_SESSION['token']['username'], $username);
stdJqReturn(1);
