<?php
/*
 * -1：身份验证失败
 * 消除未读消息
 * */
require "superHeader.php";
if(!$token->tokenCheck()) stdJqReturn(-1);
$sql->unreadUpdate($_SESSION['token']['username'], $_POST['username']);