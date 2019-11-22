<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$token = new token();
$conn = mysqliConnect();
$username = addslashes(sprintf("%s", $_GET['username']));
$username = substr($username, 0, 15);
$user = getUserInfByUsername($conn, $username);
if (!$user['username']) stdJqSqlReturn($conn, -1);
if ($user['lvl'] > 1) stdJqSqlReturn($conn, -2);
if (!$token->temTokenOverTimeCheck($conn, $username)) stdJqSqlReturn($conn, -3);
if ($user['password']) {
    if ($_SESSION['captcha'] != $_POST['captcha']) stdJqSqlReturn($conn, -4);
    if ($user['password'] != $_POST['password']) stdJqSqlReturn($conn, -5);
}
createChat($conn, $_SESSION['token']['username'], $username);
stdJqSqlReturn($conn, 1);
