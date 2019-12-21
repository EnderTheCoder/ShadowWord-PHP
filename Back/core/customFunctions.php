<?php
function emptyCheck($str) {
    return (!empty($str) && isset($str));
}

function stdJqReturn($res) {
    $callback = $_GET['callback'];
    echo $callback.'('.json_encode($res).')';
    exit;
}

function sendMailTo($mailTo, $mailSub, $mailBody)
{
    $mailSub = "=?UTF-8?B?" . base64_encode($mailSub) . "?="; //防止乱码
    //$mailBody = "=?UTF-8?B?".base64_encode($mailBody)."?="; //防止乱码
    $mailType = "HTML";
    $smtp = new smtp(SMTP_SERVER_ADDR, SMTP_SERVER_PORT, true, SMTP_USER, SMTP_PASS);
    $smtp->debug = FALSE; //是否显示发送的调试信息
    $smtp->sendmail($mailTo, SMTP_USER_EMAIL, $mailSub, $mailBody, $mailType);
}