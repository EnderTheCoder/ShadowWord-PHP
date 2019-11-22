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

function registCheck($conn, $username, $password, $regDate, $email, $regIP)
{
    $stmt = $conn->prepare("INSERT INTO userInf (username, password, regDate, email, regIP) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $username, $password, $regDate, $email, $regIP);
    return $stmt->execute();
}

function getPasswordByUsername($conn, $username)
{
    $stmt = $conn->prepare("SELECT lvl, password FROM userInf where username = ?");
    $stmt->bind_param('s', $username);
    $password = '';
    $lvl = '';
    $stmt->bind_result($lvl, $password);
    $stmt->execute();
    $stmt->fetch();
    if ($lvl < 2) return false;
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
    $checkout = tokenGet($conn, $username);
    if ($checkout['tokenValue']) {
        $stmt = $conn->prepare("UPDATE token SET tokenValue = ?, timeNow = ?, exp = ? WHERE username = ?");
        $stmt->bind_param('ssss', $tokenValue, $timeNow, $exp, $username);
        return $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO token (username, timeNow, tokenValue, exp) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $username, $timeNow, $tokenValue, $exp);
        return $stmt->execute();
    }
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

function tokenUpdate($conn, $tokenValue, $username)
{
//    $stmt = $conn->prepare("DELETE FROM TABLE token WHERE username = ?");
//    $stmt->bind_param('s', $username);
//    $stmt->execute();
    $stmt = $conn->prepare("UPDATE token SET tokenValue = ?, timeNow = ?, exp = 3600 WHERE username = ?");
    $stmt->bind_param('sss', $tokenValue, time(), $username);
    return $stmt->execute();
}

function messageSent($conn, $sender, $receiver, $message)
{
    $stmt = $conn->prepare("insert into messages(sender, receiver, message, sentTime) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $sender, $receiver, $message, time());
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
    $stmt = $conn->prepare("SELECT unread, user_2 FROM user_chats WHERE user_1 = ? AND unread > 0");
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
    $id = '';
    $message = '';
    $sentTime = '';
    $state = '';
    $rows = 0;
    $result = array();
    $stmt = $conn->prepare("SELECT id, sender, message, sentTime, state FROM messages WHERE (receiver = ? AND sender = ?) OR (receiver = ? AND sender = ?)");
    $stmt->bind_param('ssss', $receiver, $sender, $sender, $receiver);
    if (!$stmt->execute()) return false;
    $stmt->bind_result($id, $sender, $message, $sentTime, $state);
    $stmt->store_result();
    while ($stmt->fetch()) {
        if (!$state) continue;
        $result[$rows] = array(
            'id' => $id,
            'sender' => $sender,
            'message' => $message,
            'sentTime' => $sentTime,
            'state' => $state,
        );
        /*
         * id：消息编号
         * sender：发送者用户名
         * message：消息内容
         * sentTime：发送时间的unix时间戳
         * state：0代表被系统删除，除管理员不可见；
         * 1代表正常状态，即双方都可见（默认）；
         * 2代表只有sender能看见；
         * 3代表只有receiver能看见
         * */
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

function createTemUser($conn, $username, $lvl, $password, $master)
{
    $stmt = $conn->prepare("UPDATE userInf SET temUsers = temUsers + 1 WHERE username = ?");
    $stmt->bind_param('s', $master);
    $stmt->execute();
    $stmt = $conn->prepare("INSERT INTO userInf (username, password, regDate, lvl, master) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $username, $password, date("Y/m/d"), $lvl, $master);
    return $stmt->execute();
}

function getUserLvlByUsername($conn, $username)
{
    $stmt = $conn->prepare("SELECT lvl FROM userInf where username = ?");
    $stmt->bind_param('s', $username);
    $lvl = '';
    $stmt->bind_result($lvl);
    $stmt->execute();
    $stmt->fetch();
    return $lvl;
}

function getTemUsersByUsername($conn, $username)
{
    $stmt = $conn->prepare("SELECT temUsers FROM userInf where username = ?");
    $stmt->bind_param('s', $username);
    $temUsers = '';
    $stmt->bind_result($temUsers);
    $stmt->execute();
    $stmt->fetch();
    return $temUsers;
}
//插入新消息会增加回话的未读消息数触发器
//create definer = Ender@`%` trigger trigger_message_update
//    after INSERT
//    on messages
//    for each row
//    UPDATE user_chats SET unread = unread + 1 where user_1 = NEW.receiver;