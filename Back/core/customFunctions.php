<?php
function emptyCheck($str) {
    return (!empty($str) && isset($str));
}

function stdJqSqlReturn($conn, $res) {
    mysqli_close($conn);
    $callback = $_GET['callback'];
    echo $callback.'('.json_encode($res).')';
    exit;
}

function stdJqReturn($res) {
    $callback = $_GET['callback'];
    echo $callback.'('.json_encode($res).')';
    exit;
}
