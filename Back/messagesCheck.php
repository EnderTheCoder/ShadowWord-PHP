<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
$token = new token();
if(!$token->tokenCheck()) stdJqReturn(-1);
$conn = mysqliConnect();
$user_2 = addslashes(sprintf("%s", $_POST['username']));
$user_2 = substr($user_2, 0, 15);
stdJqSqlReturn($conn, messagesCheck($conn, $_SESSION['token']['username'], $user_2));