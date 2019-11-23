<?php
//-1代表未登录或者登录失效，返回用户名代表登录成功
require "superHeader.php";
if($token->tokenCheck()) stdJqReturn($_SESSION['token']['username']);
else stdJqReturn(-1);