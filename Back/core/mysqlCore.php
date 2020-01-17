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

    public function registerCheck($username, $password, $regDate, $email, $regIP, $state)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("INSERT INTO userInf (username, password, regDate, email, regIP, state) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $username, $password, $regDate, $email, $regIP, $state);
        $stmt->execute();
        /*
         * 用户状态state
         * 1:正常用户
         * 2:注册中未验证用户
         * 3:封禁但未注销用户
         * */
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

    public function chatExistenceCheck($sender, $receiver)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("SELECT user_2 FROM user_chats WHERE user_1 = ? AND user_2 = ?");
        $stmt->bind_param('ss', $sender, $receiver);
        $result = '';
        $stmt->bind_result($result);
        $stmt->execute();
        $stmt->fetch();
        return boolval($result != '');
    }

    public function messageSend($sender, $receiver, $message)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("INSERT INTO messages(sender, receiver, message, sentTime) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $sender, $receiver, $message, time());
        return $stmt->execute();
    }

    public function chatUpdate($user_1, $user_2, $summary)//user_1 send to user_2
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("UPDATE user_chats SET latestMessage = ? WHERE (user_1 = ? AND user_2 = ?) OR (user_1 = ? AND user_2 = ?)");
        $stmt->bind_param('sssss', $summary, $user_1, $user_2, $user_2, $user_1);
        $stmt->execute();
//        $conn = $this->mysqliConnect();
//        $stmt = $conn->prepare("UPDATE user_chats SET unread = unread + 1 WHERE user_1 = ? AND user_2 = ?");
//        $stmt->bind_param('ss', $user_2, $user_1);
//        $stmt->execute();
//        上方代码已经使用MySQL触发器代替
    }

    public function unreadUpdate($user_1, $user_2)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("UPDATE user_chats SET unread = 0 WHERE user_1 = ? AND user_2 = ?");
        $stmt->bind_param('ss', $user_1, $user_2);
        $stmt->execute();
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

    public function messagesCheck($receiver, $sender, $lastMessageID)
    {
        $conn = $this->mysqliConnect();
        $id = '';
        $message = '';
        $sendTime = '';
        $state = '';
        $result = array();
        $result['rows'] = 0;
        $stmt = $conn->prepare("SELECT id, sender, message, sentTime, state FROM messages 
        WHERE ((receiver = ? AND sender = ?) OR (receiver = ? AND sender = ?)) AND (id > ?)
        ORDER BY id ASC LIMIT 100");
        $stmt->bind_param('sssss', $receiver, $sender, $sender, $receiver, $lastMessageID);
        if (!$stmt->execute()) return false;
        $stmt->bind_result($id, $sender, $message, $sendTime, $state);
        $stmt->store_result();
        while ($stmt->fetch()) {
            $result['rows']++;
//            if (!$state) continue;
            $result[$result['rows']] = array(
                'id' => $id,
                'sender' => $sender,
                'message' => $message,
                'sendTime' => $sendTime,
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
        }
        return $result;
    }

    public function listCheck($username)
    {
        $result = array(
            'rows' => 0,
            'unread' => 0,
            'messages' => array(),
        );
        $conn = $this->mysqliConnect();
        $unread = 0;
        $user_2 = '';
        $latestMessage = '';
        $stmt = $conn->prepare("SELECT unread, user_2, latestMessage FROM user_chats WHERE user_1 = ?");
        $stmt->bind_param('s', $username);
        if (!$stmt->execute()) return false;
        $stmt->bind_result($unread, $user_2, $latestMessage);
        $stmt->store_result();
        while ($stmt->fetch()) {
            $result['rows']++;
            $result['unread'] += $unread;
            $result['messages'][$result['rows']] = array(
                'user_2' => $user_2,
                'unread' => $unread,
                'latestMessage' => $latestMessage,
            );
        }
        return $result;
    }

    public function createTemUser($username, $lvl, $password, $master)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("UPDATE userInf SET temUsers = temUsers + 1 WHERE username = ?");
        $stmt->bind_param('s', $master);
        $stmt->execute();
        $stmt = $conn->prepare("INSERT INTO userInf (username, password, regDate, lvl, master, state) VALUES (?, ?, ?, ?, ?, 1)");
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
        $stmt = $conn->prepare("SELECT id, username, lvl, views, email, regDate, regIP, lastloginIP, lastloginDate, info, password, temUsers, master, state FROM userInf where username = ?");
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
            $result['lastLoginDate'],
            $result['info'],
            $result['password'],
            $result['temUsers'],
            $result['master'],
            $result['state']
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

    public function userDestroy($username)
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

    public function saveEmailKey($key, $action, $username)
    {
        /*
         * 邮件返回动作类型action
         * register：注册验证码返回
         * resetPassword：重置密码返回
         * changePassword：修改密码返回
         * */
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("INSERT INTO emailKey(keyValue, `action`, username) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $key, $action, $username);
        $stmt->execute();
    }

    public function getEmailKey($key)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("SELECT `keyValue`, `username`, `action` FROM `emailKey` WHERE `keyValue` = ?");
        $stmt->bind_param('s', $key);
        $result = array();
        $stmt->bind_result(
            $result['keyValue'],
            $result['username'],
            $result['action']
            );
        $stmt->execute();
        $stmt->fetch();
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("DELETE FROM `emailKey` WHERE `keyValue` = ?");
        $stmt->bind_param('s', $key);
        $stmt->execute();
        return $result;
    }

    public function enableUser($username)
    {
        $conn = $this->mysqliConnect();
        $stmt = $conn->prepare("UPDATE `userInf` SET `state` = 1 WHERE `username` = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
    }
}
//插入新消息会增加回话的未读消息数触发器
//create definer = Ender@`%` trigger trigger_message_update
//    after INSERT
//    on messages
//    for each row
//    UPDATE user_chats SET unread = unread + 1 where user_1 = NEW.receiver;