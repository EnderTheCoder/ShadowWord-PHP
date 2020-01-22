<?php
function emptyCheck($str)
{
    return (!empty($str) && isset($str));
}

function stdJqReturn($res)
{
    if (emptyCheck($_GET['callback'])) $callback = $_GET['callback'];
    else $callback = $_POST['callback'];
    echo $callback . '(' . json_encode($res) . ')';
    exit;
}

function sendMailTo($mailTo, $mailSub, $mailBody)
{
    $mailSub = "=?UTF-8?B?" . base64_encode($mailSub) . "?="; //防止乱码
    //$mailBody = "=?UTF-8?B?".base64_encode($mailBody)."?="; //防止乱码
    $mailType = "HTML";
    $smtp = new smtp(SMTP_SERVER_ADDR, SMTP_SERVER_PORT, true, SMTP_USER, SMTP_PASS);
    $smtp->debug = FALSE; //是否显示发送的调试信息
    return $smtp->sendmail($mailTo, SMTP_USER_EMAIL, $mailSub, $mailBody, $mailType);
}

function keySpawn()
{
    $randLength = 20;
    $chars = 'abcdefghijklmnopqrstuvwxyzQWERTYUIOPASDFGHJKLZXCVBNM';
    $len = strlen($chars);
    $randStr = '';
    for ($i = 0; $i < $randLength; $i++) {
        $randStr .= $chars[rand(0, $len - 1)];
    }
    $Key = $randStr . time();
    $Key = base64_encode($Key);
    return $Key;
}

function sqlInjectionFilter($str, $length)
{
    $str = addslashes(sprintf("%s", $str));
    $str = substr($str,0, $length);
    return $str;
}

