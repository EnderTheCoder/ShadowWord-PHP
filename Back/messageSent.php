<?php
/*
 * 1代表发送成功
 * -1代表token无效
 * -2代表发送失败
 * -3代表用户不存在
 * */
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./config/mysqlConfig.php";
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$token = new token();
$sql = new mysqlCore();
if (!$token->tokenCheck()) stdJqReturn(-1);
$message = addslashes(sprintf("%s", $_POST['message']));
$receiver = addslashes(sprintf("%s", $_POST['receiver']));
$message = substr($message, 0, 1000);
$receiver = substr($receiver, 0, 15);
$sql->messageSent($_SESSION['token']['username'], $receiver, $message);
stdJqReturn(1);
//if(messageSent($_SESSION['token']['username'], $receiver, $message)) stdJqReturn(1);
//else stdJqReturn(-2);