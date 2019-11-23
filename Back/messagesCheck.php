<?php
require "superHeader.php";
if(!$token->tokenCheck()) stdJqReturn(-1);
$user_2 = addslashes(sprintf("%s", $_POST['username']));
$user_2 = substr($user_2, 0, 15);
stdJqReturn($sql->messagesCheck($_SESSION['token']['username'], $user_2));