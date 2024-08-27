<?php
// fetch_messages.php

require_once(__DIR__ . '/user_info/user_info.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/templates/common.tpl.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new DB();
$dbh = $db->get_connection();

if (isset($_GET['other_user_id'])) {
    $other_user_id = filter_var($_GET['other_user_id'], FILTER_SANITIZE_NUMBER_INT);
    $logged_user_id = $_SESSION['user_id'];

    // Get all messages
    $stmt = $dbh->prepare('SELECT * FROM Message WHERE (userOrig = ? AND userDest = ?) OR (userOrig = ? AND userDest = ?) ORDER BY date ASC');
    $stmt->execute([$other_user_id, $logged_user_id, $logged_user_id, $other_user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $prevDay = '';
    foreach ($messages as $message) {
        $messageContent = htmlentities($message['message']);
        $messageDay = date('d/m/Y', strtotime($message['date']));
        $messageHour = date('H:i:s', strtotime($message['date']));

        // Check if the message is from the same day, otherwise display new day
        if ($prevDay != $messageDay){
            $prevDay = $messageDay;
            echo '<div class="date-message-field">';
            echo '<p class="message-date">' . $messageDay . '</p>';
            echo '</div>';
        }

        // There are 2 types of inputs, receiving, sending messages
        if ($message['userOrig'] != $other_user_id) { // Message sent by the current user
            // making sure that each grid line was only a message, it fill the other with an empty div
            echo '<div class="receiving-message">';
            echo '</div>';

            echo '<div class="message sending-message">';
        } else { // Message received by the current user
            // making sure that each grid line was only a message, it fill the other with an empty div
            echo '<div class="sending-message">';
            echo '</div>';

            
            echo '<div class="message receiving-message">';
        }

        echo '<p class="message-hour">' . $messageHour . '</p>';
        echo '<p class="message-content">' . $messageContent . '</p>';
        echo '</div>';
    }
    /// Draw input message form
    echo '<div id="new-message">
            <form action="action_send_message.php" method="POST">
                <label><input type="text" placeholder="New message..." name="message-content" required></label>
                <input type="hidden" name="other_user_id" value="' . $other_user_id . '"> <!-- Value will be defined with js -->
                <button type="submit">Send</button>
            </form>
        </div>';
} else {
    echo 'Error: User ID parameter is missing';
}
?>
