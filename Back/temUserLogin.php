<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./config/mysqlConfig.php";
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$token = new token();
$sql = new mysqlCore();
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
