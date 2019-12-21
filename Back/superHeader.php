<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
require "config/mysqlConfig.php";
require "config/siteConfig.php";
require "config/smtpConfig.php";
require "core/mysqlCore.php";
require "core/tokenCore.php";
require "core/customFunctions.php";
require "core/smtpEmailCore.php";
session_start();
$token = new token();
$sql = new mysqlCore();