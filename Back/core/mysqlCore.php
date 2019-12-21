<?php

class mysqlCore
{
    private function mysqliConnect()
    {
        $conn = new mysqli(MYSQL_HOST, MYSQL_USER_NAME, MYSQL_PASSWORD);
        if (!$conn) {
            return false;
        }
        mysqli_query($conn, "set names utf8");
        mysqli_select_db($conn, MYSQL_DB_NAME);
        return $conn;
    }

    public function registCheck($username, $password, $regDate, $email, $regIP)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("INSERT INTO userInf (username, password, regDate, email, regIP) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $username, $password, $regDate, $email, $regIP);
        $stmt->execute();
    }

    public function updateLoginInf($username, $lastLoginIP, $lastLoginDate)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("UPDATE userInf SET lastLoginIP = ?, lastLoginDate = ? WHERE username = ?");
        $stmt->bind_param('sss', $lastLoginIP, $lastLoginDate, $username);
        return $stmt->execute();
    }

    public function tokenSave($username, $tokenValue, $timeNow, $exp)
    {
        $conn = $this->mysqliConnect();
        $checkout = $this->tokenGet($username);
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

    public function tokenGet($username)
    {
        $conn = $this->mysqliConnect();
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

    public function tokenUpdate($tokenValue, $username)
    {
        $conn = $this->mysqliConnect();
//     $stmt = $conn->prepare("DELETE FROM TABLE token WHERE username = ?");
//     $stmt->bind_param('s', $username);
//     $stmt->execute();
        $stmt = $conn->prepare("UPDATE token SET tokenValue = ?, timeNow = ?, exp = 3600 WHERE username = ?");
        $stmt->bind_param('sss', $tokenValue, time(), $username);
        return $stmt->execute();
    }

    public function messageSent($sender, $receiver, $message)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("insert into messages(sender, receiver, message, sentTime) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $sender, $receiver, $message, time());
        return $stmt->execute();
    }

    public function unreadUpdate($user_1, $user_2)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("UPDATE user_chats SET unread = 0 WHERE user_1 = ? AND user_2 = ?");
        $stmt->bind_param('ss', $user_1, $user_2);
        return $stmt->execute();
    }

    public function unreadCheck($username)
    {
        $conn = $this->mysqliConnect();
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

    public function messagesCheck($receiver, $sender)
    {
        $conn = $this->mysqliConnect();
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

    public function listCheck($username)
    {
        $conn = $this->mysqliConnect();
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

    public function createTemUser($username, $lvl, $password, $master)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("UPDATE userInf SET temUsers = temUsers + 1 WHERE username = ?");
        $stmt->bind_param('s', $master);
        $stmt->execute();
        $stmt = $conn->prepare("INSERT INTO userInf (username, password, regDate, lvl, master) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $username, $password, date("Y/m/d"), $lvl, $master);
        return $stmt->execute();
    }

    public function getTemUsersByUsername($username)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("SELECT temUsers FROM userInf where username = ?");
        $stmt->bind_param('s', $username);
        $temUsers = '';
        $stmt->bind_result($temUsers);
        $stmt->execute();
        $stmt->fetch();
        return $temUsers;
    }

    public function getUserInfByUsername($username)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("SELECT id, username, lvl, views, email, regDate, regIP, lastloginIP, lastloginDate, info, password, temUsers, master FROM userInf where username = ?");
        $stmt->bind_param('s', $username);
        $result = array();
        $stmt->bind_result(
            $result['id'],
            $result['username'],
            $result['lvl'],
            $result['views'],
            $result['email'],
            $result['regDate'],
            $result['regIP'],
            $result['lastLoginIP'],
            $result['lastloginDate'],
            $result['info'],
            $result['password'],
            $result['temUsers'],
            $result['master']
        );
        $stmt->execute();
        $stmt->fetch();
        return $result;
    }

    public function createChat($user_1, $user_2)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("INSERT INTO user_chats(user_1, user_2) VALUES (?, ?)");
        $stmt->bind_param('ss', $user_1, $user_2);
        $stmt->execute();
        $stmt->bind_param('ss', $user_2, $user_1);
        $stmt->execute();
    }

    public function userDestory($username)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("DELETE FROM userInf WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
    }

    public function requestChat($user_1, $user_2)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("INSERT INTO requestsQuery(sender, receiver) VALUES (?, ?)");
        $stmt->bind_param('ss', $user_1, $user_2);
        $stmt->execute();
    }

    public function saveRegisterKey($backKey)
    {
        $conn = $this->mysqliConnect();

    }

}
//插入新消息会增加回话的未读消息数触发器
//create definer = Ender@`%` trigger trigger_message_update
//    after INSERT
//    on messages
//    for each row
//    UPDATE user_chats SET unread = unread + 1 where user_1 = NEW.receiver;