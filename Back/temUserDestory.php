<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./config/mysqlConfig.php";
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
require "./core/customFunctions.php";
session_start();
$token = new token();
$sql = new mysqlCore();
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$user = $sql->getUserInfByUsername($username);
if (!$token->tokenCheck()) stdJqReturn(-1);
if ($_SESSION['token']['username'] != $user['master']) stdJqReturn(-2);
$sql->userDestory($username);
stdJqReturn(1);