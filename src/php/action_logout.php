
<?php

    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');

    session_start();

    session_destroy();
    header("Location: login.php");
    exit();

?>