<?php
/*接口使用指南
 * 返回-1代表输入存在空
 * 返回-2代表验证码错误
 * 返回-3代表账号或密码错误
 * 返回-4代表未知数据库错误
 * 返回1代表登陆成功
 * */
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./config/mysqlConfig.php";
require "./core/customFunctions.php";
require "./core/mysqlCore.php";
require "./core/tokenCore.php";
session_start();
$captcha = $_SESSION['captcha'];
$_SESSION['captcha'] = rand();
$sql = new mysqlCore();
if (!emptyCheck($_POST['username']) ||
    !emptyCheck($_POST['password']) ||
    !emptyCheck($_POST['captcha'])) stdJqReturn(-1);
if ($_POST['captcha'] != $captcha) stdJqReturn(-2);
$username = addslashes(sprintf("%s", $_POST['username']));
$password = addslashes(sprintf("%s", $_POST['password']));
$username = substr($username, 0, 15);
$password = substr($password, 0, 40);
$lastLoginDate = date("Y/m/d");
$lastLoginIP = $_SERVER['REMOTE_ADDR'];
$passwordGet = $sql->getPasswordByUsername($username);
if (!$passwordGet || $passwordGet != $password)
    stdJqReturn(-3);
else {
    if ($sql->updateLoginInf($username, $lastLoginIP, $lastLoginDate)) {
        $token = new token();
        $token->tokenSpawn($username);
        stdJqReturn(1);
    } else stdJqReturn(-4);
}