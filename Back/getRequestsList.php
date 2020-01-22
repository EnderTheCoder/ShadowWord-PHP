<?php
require "superHeader.php";
if (!$token->tokenCheck()) stdJqReturn(-1);
$result = $sql->getRequestsList($_SESSION['token']['username']);
stdJqReturn($result);
