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
if($user['username'] == '') stdJqSqlReturn($conn, -2);
requestChat($conn, $_SESSION['token']['username'], $user['username']);
stdJqSqlReturn($conn, 1);