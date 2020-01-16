<?php
/*
 * 1代表发送成功
 * -1代表token无效
 * -2代表发送失败
 * -3代表用户不存在
 * */
require "superHeader.php";
if (!$token->tokenCheck()) stdJqReturn(-1);
$message = addslashes(sprintf("%s", $_POST['message']));
$receiver = addslashes(sprintf("%s", $_POST['receiver']));
$message = substr($message, 0, 1000);
$receiver = substr($receiver, 0, 15);
if (strlen($message) > 10) $summary = substr($message, 0, 9) . '...';
else $summary = $message;
if(!$sql->chatExistenceCheck($_SESSION['token']['username'], $receiver)) stdJqReturn(-3);
$sql->messageSend($_SESSION['token']['username'], $receiver, $message);
$sql->chatUpdate($_SESSION['token']['username'], $receiver, $summary);
stdJqReturn(1);
//if(messageSent($_SESSION['token']['username'], $receiver, $message)) stdJqReturn(1);
//else stdJqReturn(-2);