<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$token = new token();
$conn = mysqliConnect();
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$user = getUserInfByUsername($conn, $username);
if (!$token->tokenCheck()) stdJqReturn(-1);
if ($_SESSION['token']['username'] != $user['master']) stdJqSqlReturn($conn, -2);
userDestory($conn, $username);
stdJqSqlReturn($conn, 1);