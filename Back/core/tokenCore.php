<?php

class token
{
    private function createToken()
    {
        $randLength = 20;
        $chars = 'abcdefghijklmnopqrstuvwxyzQWERTYUIOPASDFGHJKLZXCVBNM';
        $len = strlen($chars);
        $randStr = '';
        for ($i = 0; $i < $randLength; $i++) {
            $randStr .= $chars[rand(0, $len - 1)];
        }
        $token = $randStr . time();
        return $token;
    }

    public function tokenSpawn($username)
    {
        $conn = mysqliConnect();
//        $string = '';
//        $arr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
//        for ($i = 0; $i < 20; $i++) {
//            $string .= $arr[rand(0, count($arr) - 1)];
//        }
        $string = $this->createToken();
        $token = array(
            'username' => $username,
            'tokenValue' => $string,
            'timeNow' => time(),
            'exp' => 3600,
        );
        $_SESSION['token'] = $token;
        tokenSave($conn, $token['username'], $token['tokenValue'], $token['timeNow'], $token['exp']);
    }

    public function tokenCheck()
    {
        $username = addslashes(sprintf("%s", $_SESSION['token']['username']));
        $username = substr($username, 0, 15);
        $conn = mysqliConnect();
        $token = tokenGet($conn, $username);
        if ($token['exp'] + $token['timeNow'] < time() ||
            $token['tokenValue'] != $_SESSION['token']['tokenValue']) return false;
        else {
            $newTokenValue = $this->createToken();
            tokenUpdate($conn, $newTokenValue, $username);
            $_SESSION['token']['tokenValue'] = $newTokenValue;
            return true;
        }
    }

    public function temTokenCheck($conn, $lvl)
    {
        $username = addslashes(sprintf("%s", $_SESSION['token']['username']));
        $username = substr($username, 0, 15);
        $token = tokenGet($conn, $username);
        if (
            $token['exp'] + $token['timeNow'] < time() ||
            $token['tokenValue'] != $_SESSION['token']['tokenValue']
        ) return false;
        else {
            if ($lvl) {
                $newTokenValue = $this->createToken();
                tokenUpdate($conn, $newTokenValue, $username);
                $_SESSION['token']['tokenValue'] = $newTokenValue;
            }
            return true;
        }
    }

    public function temTokenOverTimeCheck($conn, $username)
    {
        $token = tokenGet($conn, $username);
        return $token['exp'] + $token['timeNow'] >= time();
    }
}