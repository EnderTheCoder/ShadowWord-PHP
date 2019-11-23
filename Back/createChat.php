<?php
require "superHeader.php";
if (!$token->tokenCheck()) stdJqReturn(-1);
$username = addslashes(sprintf("%s", $_POST['username']));
$username = substr($username, 0, 15);
$user = $sql->getUserInfByUsername($username);
if ($user['lvl'] < 2) stdJqReturn(-2);
$sql->createChat($_SESSION['token']['username'], $username);
stdJqReturn(1);