<?php
/*
 * -1：身份验证失败
 * 返回二维json数组代表未读消息，外面一层从0到n-1条记录；里面一层中user_2代表来自user_2的消息，unread代表来自该用户的未读消息
 * */
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
$token = new token();
if(!$token->tokenCheck()) stdJqReturn(-1);
$conn = mysqliConnect();
stdJqSqlReturn($conn, unreadCheck($conn, $_SESSION['token']['username']));