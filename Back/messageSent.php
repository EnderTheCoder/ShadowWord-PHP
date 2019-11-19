<?php
/*
 * 1代表发送成功
 * -1代表token无效
 * -2代表发送失败
 * -3代表用户不存在
 * */
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
$token = new token();
if(!$token->tokenCheck()) stdJqReturn(-1);
$message = addslashes(sprintf("%s", $_POST['message']));
$receiver = addslashes(sprintf("%s", $_POST['receiver']));
$message = substr($message, 0, 100);
$receiver = substr($receiver, 0, 15);
$conn = mysqliConnect();
if(messageSent($conn, $_SESSION['token']['username'], $receiver, $message)) stdJqSqlReturn($conn, 1);
else stdJqSqlReturn($conn, -2);
