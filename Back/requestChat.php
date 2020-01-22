<?php
require "superHeader.php";
if (!emptyCheck($_POST['username'])) stdJqReturn(-4);
if (!$token->tokenCheck()) stdJqReturn(-1);
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$requestMessage = substr($_POST['requestMessage'], 0, 300);
$user = $sql->getUserInfByUsername($username);
if ($user['username'] == null || $user['lvl'] < 2) stdJqReturn(-2);
if ($sql->chatExistenceCheck($_SESSION['token']['username'], $user['username'])) stdJqReturn(-3);
$requests = $sql->getRequestsList($username);
for ($i = 0; $i < $requests['rows']; $i++)
    if ($requests[$i]['sender'] == $_SESSION['token']['username'])
        stdJqReturn(-5);
$sql->chatUpdate($username, "验证消息", "用户".$_SESSION['token']['username']."申请加为好友");
$sql->requestChat($_SESSION['token']['username'], $user['username'], $requestMessage);
stdJqReturn(1);