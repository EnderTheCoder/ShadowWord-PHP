<?php
require "superHeader.php";
if (!$token->tokenCheck()) stdJqReturn(-1);
if (!emptyCheck($_POST['username'])) stdJqReturn(-2);
$result = $sql->getUserInfByUsername($_POST['username']);
unset($result['id']);
unset($result['views']);
unset($result['regDate']);
unset($result['regIP']);
unset($result['lastLoginIP']);
unset($result['lastLoginDate']);
unset($result['info']);
unset($result['password']);
unset($result['temUsers']);
unset($result['master']);
stdJqReturn($result);