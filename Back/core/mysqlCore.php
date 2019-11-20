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

function unreadUpdate($conn, $user_1, $user_2)
{
    $stmt = $conn->prepare("UPDATE user_chats SET unread = 0 WHERE user_1 = ? AND user_2 = ?");
    $stmt->bind_param('ss', $user_1, $user_2);
    return $stmt->execute();
}

function unreadCheck($conn, $username)
{
    $unread = '';
    $user_2 = '';
    $rows = 0;
    $result = array();
    $stmt = $conn->prepare("SELECT unread, user_2 FROM user_chats WHERE user_1 = ? AND unread != 0");
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) return false;
    $stmt->bind_result($unread, $user_2);
    $stmt->store_result();
    while ($stmt->fetch()) {
        $result[$rows] = array(
            'user_2' => $user_2,
            'unread' => $unread,
        );
        $rows++;
    }
    return $result;
}

function messagesCheck($conn, $receiver, $sender)
{
    $message = '';
    $sentTime = '';
    $statues = '';
    $rows = 0;
    $result = array();
    $stmt = $conn->prepare("SELECT message, sentTime, statues FROM messages WHERE receiver = ? AND sender = ?");
    $stmt->bind_param('ss', $receiver, $sender);
    if (!$stmt->execute()) return false;
    $stmt->bind_result($message, $sentTime, $statues);
    $stmt->store_result();
    while ($stmt->fetch()) {
        if(!$statues) continue;
        $result[$rows] = array(
            'message' => $message,
            'sentTime' => $sentTime,
        );
        $rows++;
    }
    return $result;
}

function listCheck($conn, $username)
{
    $unread = '';
    $user_2 = '';
    $rows = 0;
    $result = array();
    $stmt = $conn->prepare("SELECT unread, user_2 FROM user_chats WHERE user_1 = ?");
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) return false;
    $stmt->bind_result($unread, $user_2);
    $stmt->store_result();
    while ($stmt->fetch()) {
        $result[$rows] = array(
            'user_2' => $user_2,
            'unread' => $unread,
        );
        $rows++;
    }
    return $result;
}