<?php
/*
 * token不合法返回-1
 * 传入post数据userType代表临时用户的权限，必须使用0或1，如果此数据不合法将返回-2
 *
 * */
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./config/mysqlConfig.php";
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$sql = new mysqlCore();
$token = new token();
if (!$token->tokenCheck()) stdJqReturn(-1);
if ($_POST['userType'] != 1 && $_POST['userType'] != 0) stdJqReturn(-2);
$exp = intval($_POST['time']);
$lvl = $sql->getUserLvlByUsername($_SESSION['token']['username']);
$temUsers = $sql->getTemUsersByUsername($_SESSION['token']['username']);
if($lvl < 2) stdJqReturn(-4);
if($lvl == 2 && $temUsers > 10) stdJqReturn(-5);
if($lvl == 3 && $temUsers > 100) stdJqReturn(-5);
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$password = addslashes(sprintf("%s", $_POST['password']));
$password = substr($password, 0, 40);
if ($sql->createTemUser($username, $_POST['userType'], $password, $_SESSION['token']['username'])) stdJqReturn(1);
else stdJqReturn(-3);