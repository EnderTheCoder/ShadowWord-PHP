<?php
require "superHeader.php";
$result = array(
    'code' => false,
    'message' => '链接无效！将跳转到主页',
    'link' => URL,
);
$key = $sql->getEmailKey($_POST['key']);
if($key['keyValue'] != $_POST['key']) stdJqReturn($result);
switch ($key['action'])
{
    case 'register':
        $sql->enableUser($key['username']);
        $result['code'] = "jump";
        $result['message'] = "新用户" . $key['username'] . "您好：你的邮箱验证已经完成，点击确定后将跳转至登录页. Have a nice day!";
        $result['link'] = $result['link'] . "Fore/LoginPage.html";
        break;
    case 'resetPassword':
        echo 1;
        break;
    case 'changePassword':
        echo 2;
        break;
}
stdJqReturn($result);