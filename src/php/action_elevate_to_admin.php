<?php 
require_once(__DIR__ . '/user_info/user_info.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/templates/common.tpl.php');

session_start();

$db = new DB();
$dbh = $db->get_connection();

if (isset($_POST['other_user_id'])){
    $user = new User($dbh, $_POST['other_user_id']);
    if ($user->is_user_valid()){
        $stmt = $dbh->prepare('UPDATE User SET isAdmin = 1 WHERE id = ?;');
        $stmt->execute(array($_POST['other_user_id']));
        $_SESSION['message'] = 'User is now admin.';
    } else {
        $_SESSION['message'] = 'The user does not exist.';
    }
} else {
    $_SESSION['message'] = 'Something unexpected happened.';
}

header("Location: profile.php?user_id=" . $_POST['other_user_id']);
exit();
?>