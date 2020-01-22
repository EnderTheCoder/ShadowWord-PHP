<?php
require "superHeader.php";
if (!$token->tokenCheck()) stdJqReturn(-1);
$list = $sql->getRequestsList($_SESSION['token']['username']);
for ($i = 0; $i < $list['rows']; $i++) {
    if ($list[$i]['sender'] == $_POST['username'] && $list[$i]['state'] == 1) stdJqReturn(1);
    else break;
}
$sql->updateRequestState($_POST['username'], $_SESSION['token']['username'], 1);
$sql->createChat($_POST['username'], $_SESSION['token']['username'], 'common', true);
stdJqReturn(1);