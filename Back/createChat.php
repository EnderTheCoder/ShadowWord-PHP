<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$token = new token();
$conn = mysqliConnect();
if (!$token->tokenCheck()) stdJqReturn(-1);
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$user = getUserInfByUsername($conn, $username);
if ($user['lvl'] < 2) stdJqSqlReturn($conn, -2);
createChat($conn, $_SESSION['token']['username'], $username);
stdJqSqlReturn($conn, 1);