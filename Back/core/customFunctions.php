<?php
function emptyCheck($str) {
    return (!empty($str) && isset($str));
}

function stdJqReturn($res) {
    $callback = $_GET['callback'];
    echo $callback.'('.json_encode($res).')';
    exit;
}
