<?php
require_once(__DIR__ . '/user_info/user_info.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/templates/common.tpl.php');

session_start();

$db = new DB();
$dbh = $db->get_connection();

$logged_user_id = $_SESSION['user_id'];

if (isset($_POST['other_user_id'], $_POST['message-content'])) {
    try {
        $other_user_id = $_POST['other_user_id'];
        $content = $_POST['message-content'];
        $date = date('Y-m-d H:i:s');

        $logged_user = new User($dbh, $logged_user_id);
        $other_user = new User($dbh, $other_user_id);

        if ($logged_user->is_user_valid() && $other_user->is_user_valid()) {
            $stmt = $dbh->prepare('INSERT INTO Message (message, date, userOrig, userDest) VALUES (?, ?, ?, ?);');
            if ($stmt->execute(array($content, $date, $logged_user_id, $other_user_id))) {
                header("Location: message.php?other_user_id=" . $other_user_id);
                exit();
            } else {
                throw new Exception('Failed to insert message into database');
            }
        } else {
            throw new Exception('Invalid user ID');
        }
    } catch (Exception $e) {
        $_SESSION['message'] = 'Something unexpected happened.';
        header("Location: message.php?other_user_id=" . $other_user_id);
        exit();
    }
}
?>
