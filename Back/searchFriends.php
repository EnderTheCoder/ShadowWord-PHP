<?php
require "superHeader.php";
if (!$token->tokenCheck()) stdJqReturn(-1);
$result = $sql->searchFriends($_POST['input']);
for ($i = 0; $i < $result['rows']; $i++)
    if ($sql->chatExistenceCheck($_SESSION['token']['username'], $result[$result['rows']]['username']))
        $result[$result['rows']] = null;
stdJqReturn($result);