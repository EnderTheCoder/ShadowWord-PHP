<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./config/mysqlConfig.php";
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$sql = new mysqlCore();
$token = new token();
if(!$token->tokenCheck()) stdJqReturn(-1);
$user_2 = addslashes(sprintf("%s", $_POST['username']));
$user_2 = substr($user_2, 0, 15);
stdJqReturn($sql->messagesCheck($_SESSION['token']['username'], $user_2));