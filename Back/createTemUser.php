<?php
/*
 * token不合法返回-1
 * 传入post数据userType代表临时用户的权限，必须使用0或1，如果此数据不合法将返回-2
 * 用户名为空返回-3
 * 用户名已被注册返回-4
 * */
require "superHeader.php";
if (!$token->tokenCheck()) stdJqReturn(-1);
if ($_POST['userType'] != 1 && $_POST['userType'] != 0) stdJqReturn(-2);
$exp = intval($_POST['time']);
$user = $sql->getUserInfByUsername($_SESSION['token']['username']);
$temUsers = $sql->getTemUsersByUsername($_SESSION['token']['username']);
if($user['lvl'] < 2) stdJqReturn(-4);
if($user['lvl'] == 2 && $temUsers > 10) stdJqReturn(-5);
if($user['lvl'] == 3 && $temUsers > 100) stdJqReturn(-5);
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$user = $sql->getUserInfByUsername($username);
if ($username == $user['username']) stdJqReturn(-4);
if(strlen($username) == 0) stdJqReturn(-3);
$password = addslashes(sprintf("%s", $_POST['password']));
$password = substr($password, 0, 40);
if ($sql->createTemUser($username, $_POST['userType'], $password, $_SESSION['token']['username'])) stdJqReturn(1);
else stdJqReturn(-3);