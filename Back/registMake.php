<?php
/*接口使用指南
 * 返回-1代表输入存在空
 * 返回-2代表验证码错误
 * 返回-3代表未知数据库错误
 * 返回1代表登陆成功
 * */
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "./core/mysqlCore.php";
require "./core/customFunctions.php";
session_start();
$captcha = $_SESSION['captcha'];
$_SESSION['captcha'] = rand();
$conn = mysqliConnect();
if (!$conn) {
    die("mysql error!");
}
if (!emptyCheck($_POST['username']) ||
    !emptyCheck($_POST['password']) ||
    !emptyCheck($_POST['email']) ||
    !emptyCheck($_POST['captcha'])
) stdJqSqlReturn($conn, -1);
if ($_SESSION['captcha'] != $captcha) stdJqSqlReturn($conn, -2);
$username = addslashes(sprintf("%s", $_POST['username']));
$password = addslashes(sprintf("%s", $_POST['password']));
$email = addslashes(sprintf("%s", $_POST['email']));
$username = substr($username, 0, 15);
$password = substr($password, 0, 40);
$email = substr($email, 0, 30);
$regDate = date("Y/m/d");
$res = registCheck($conn, $username, $password, $regDate, $email);
if ($res) stdJqSqlReturn($conn, 1);
else stdJqSqlReturn($conn, -3);
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