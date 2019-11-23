<?php
require "superHeader.php";
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$user = $sql->getUserInfByUsername($username);
if (!$token->tokenCheck()) stdJqReturn(-1);
if ($_SESSION['token']['username'] != $user['master']) stdJqReturn(-2);
$sql->userDestory($username);
stdJqReturn(1);