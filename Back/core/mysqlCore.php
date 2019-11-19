<?php
require "./config/mysqlConfig.php";
function mysqliConnect()
{
    $conn = new mysqli(MYSQL_HOST, MYSQL_USER_NAME, MYSQL_PASSWORD);
    if (!$conn) {
        return false;
    }
    mysqli_query($conn, "set names utf8");
    mysqli_select_db($conn, MYSQL_DB_NAME);
    return $conn;
}

function registCheck($conn, $username, $password, $regDate, $email)
{
    $stmt = $conn->prepare("INSERT INTO userInf (username, password, regDate, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $username, $password, $regDate, $email);
    return $stmt->execute();
}

function getPasswordByUsername($conn, $username)
{
    $stmt = $conn->prepare("SELECT password FROM userInf where username = ?");
    $stmt->bind_param('s', $username);
    $password = '';
    $stmt->bind_result($password);
    $stmt->execute();
    $stmt->fetch();
    return $password;
}

function updateLoginInf($conn, $username, $lastLoginIP, $lastLoginDate)
{
    $stmt = $conn->prepare("UPDATE userInf SET lastLoginIP = ?, lastLoginDate = ? WHERE username = ?");
    $stmt->bind_param('sss', $lastLoginIP, $lastLoginDate, $username);
    return $stmt->execute();
}

function tokenSave($conn, $username, $tokenValue, $timeNow, $exp)
{
        $stmt = $conn->prepare("UPDATE token SET tokenValue = ?, timeNow = ?, exp = ? WHERE username = ?");
        $stmt->bind_param('ssss', $tokenValue, $timeNow, $exp, $username);
        return $stmt->execute();
//        $stmt = $conn->prepare("INSERT INTO token (username, timeNow, tokenValue, exp) VALUES (?, ?, ?, ?)");
//        $stmt->bind_param('ssss', $username, $timeNow, $tokenValue, $exp);
//        echo 'c';
//        return $stmt->execute();
}

function tokenGet($conn, $username)
{
    $stmt = $conn->prepare("SELECT timeNow, exp, tokenValue FROM token where username = ?");
    $stmt->bind_param('s', $username);
    $result = array(
        'tokenValue' => '',
        'timeNow' => '',
        'exp' => '',
    );
    $stmt->bind_result($result['timeNow'], $result['exp'], $result['tokenValue']);
    $stmt->execute();
    $stmt->fetch();
    return $result;
}

function tokenUpdate($conn, $username)
{
//    $stmt = $conn->prepare("DELETE FROM TABLE token WHERE username = ?");
//    $stmt->bind_param('s', $username);
//    $stmt->execute();
    $stmt = $conn->prepare("UPDATE token SET timeNow = ?, exp = 3600 where username = ?");
    $stmt->bind_param('ss', time(), $username);
    return $stmt->execute();
}

function messageSent($conn, $sender, $receiver, $message)
{
    $stmt = $conn->prepare("INSERT INTO messages (sender, recevier, message, sentTime) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sss', $sender, $receiver, $message, time());
    return $stmt->execute();
}