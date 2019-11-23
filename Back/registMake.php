<?php
/*接口使用指南
 * 返回-1代表输入存在空
 * 返回-2代表验证码错误
 * 返回-3代表用户名已被注册
 * 返回1代表登陆成功
 * */
require "superHeader.php";
$captcha = $_SESSION['captcha'];
$_SESSION['captcha'] = rand();
if (!emptyCheck($_POST['username']) ||
    !emptyCheck($_POST['password']) ||
    !emptyCheck($_POST['email']) ||
    !emptyCheck($_POST['captcha'])
) stdJqReturn(-1);
if ($_POST['captcha'] != $captcha) stdJqReturn(-2);
$username = addslashes(sprintf("%s", $_POST['username']));
$password = addslashes(sprintf("%s", $_POST['password']));
$email = addslashes(sprintf("%s", $_POST['email']));
$username = substr($username, 0, 15);
$password = substr($password, 0, 40);
$email = substr($email, 0, 50);
$user = $sql->getUserInfByUsername($username);
if($username == $user['username']) stdJqReturn(-3);
$regDate = date("Y/m/d");
$regIP = $_SERVER['REMOTE_ADDR'];
$sql->registCheck($username, $password, $regDate, $email, $regIP);
stdJqReturn(1);
/*
 * 权限分为6级
 * 0级代表短暂临时会话用户
 * 1级代表长期临时会话用户
 * 2级代表普通用户
 * 3级代表付费用户
 * 4级代表管理员用户
 * 5级代表最高权限用户
 *
 * */