<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$token = new token();
if(!$token->tokenCheck()) stdJqReturn(-1);
$conn = mysqliConnect();
stdJqSqlReturn($conn, listCheck($conn, $_SESSION['token']['username']));