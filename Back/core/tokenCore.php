<?php
class token
{
    public function tokenSpawn($username)
    {
        $conn = mysqliConnect();
        $string = '';
        $arr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        for ($i = 0; $i < 20; $i++) {
            $string .= $arr[rand(0, count($arr) - 1)];
        }
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
            tokenUpdate($conn, $username);
            return true;
        }
    }
}