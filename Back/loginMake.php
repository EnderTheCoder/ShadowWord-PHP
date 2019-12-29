<?php
/*接口使用指南
 * 返回-1代表输入存在空
 * 返回-2代表验证码错误
 * 返回-3代表账号或密码错误
 * 返回-4代表未知数据库错误
 * 返回-5代表临时用户入口错误
 * 返回-6代表用户未完成邮箱验证
 * 返回-7代表用户已被封禁
 * 返回1代表登陆成功
 * */
require "superHeader.php";
$captcha = $_SESSION['captcha'];
$_SESSION['captcha'] = rand();
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
$user = $sql->getUserInfByUsername($username);
if ($user['state'] == 3) stdJqReturn(-7);
if ($user['state'] == 2) stdJqReturn(-6);
if ($user['lvl'] < 2) stdJqReturn(-5);
if ($user['username'] != $username|| $user['password'] != $password)
    stdJqReturn(-3);
else {
    if ($sql->updateLoginInf($username, $lastLoginIP, $lastLoginDate)) {
        $token->tokenSpawn($username);
        stdJqReturn(1);
    } else stdJqReturn(-4);
}