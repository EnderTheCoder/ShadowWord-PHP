<?php
require "superHeader.php";
if(!$token->tokenCheck()) stdJqReturn(-1);
if(!$sql->chatExistenceCheck($_SESSION['token']['username'], $_POST['username'])) stdJqReturn(-2);
stdJqReturn($sql->messagesCheck($_SESSION['token']['username'], $_POST['username'], $_POST['LastMessageID']));