<?php
//-1代表未登录或者登录失效，返回用户名代表登录成功
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
session_start();
include "./core/mysqlCore.php";
include "./core/customFunctions.php";
include "./core/tokenCore.php";
$token = new token();
if($token->tokenCheck()) stdJqReturn($_SESSION['token']['username']);
else stdJqReturn(-1);