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
if (!$token->tokenCheck()) stdJqReturn(-1);
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$user = $sql->getUserInfByUsername($username);
if ($user['lvl'] < 2) stdJqReturn(-2);
$sql->createChat($_SESSION['token']['username'], $username);
stdJqReturn(1);