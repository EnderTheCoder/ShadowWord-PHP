<?php
require "superHeader.php";
if (!$token->tokenCheck()) stdJqReturn(-1);
$result = $sql->searchFriends($_POST['input'], $_SESSION['token']['username']);
for ($i = 1; $i <= $result['rows']; $i++)
    if ($sql->chatExistenceCheck($_SESSION['token']['username'], $result[$i]['username']))
        $result[$result['rows']] = null;
stdJqReturn($result);