<?php
require "superHeader.php";
if(!$token->tokenCheck()) stdJqReturn(-1);
stdJqReturn($sql->listCheck($_SESSION['token']['username']));