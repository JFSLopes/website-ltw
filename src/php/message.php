<?php

require_once(__DIR__ . '/user_info/user_info.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/templates/common.tpl.php');
require_once(__DIR__ . '/display_message.php');


session_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$db = new DB();
$dbh = $db->get_connection();

$user_id = $_SESSION['user_id'];
$open_message_user_id = '';
if (isset($_GET['other_user_id'])){
    $open_message_user_id = $_GET['other_user_id'];
    $user = new User($dbh, $open_message_user_id);
    if (!$user->is_user_valid()){
        /// Deal with the error.
    }
}

draw_header("Messages", $dbh, $user_id);
draw_messages($dbh, $user_id, $open_message_user_id);
draw_footer();

?>

<?php
function draw_messages($dbh, $user_id, $open_message_user_id){
    /// Pair like (1,2) and (2,1), only one must be selected (userOrig > userDest)
    $stmt = $dbh->prepare('SELECT DISTINCT userOrig, userDest FROM Message WHERE (userOrig = ? OR userDest = ?) ORDER BY date ASC;');
    $stmt->execute([$user_id, $user_id]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ids = [];
    foreach ($conversations as $conversation){
        $other_user_id = $conversation['userOrig'] == $user_id ? $conversation['userDest'] : $conversation['userOrig'];
        if (!in_array($other_user_id, $ids)){
            array_push($ids, $other_user_id);
        }
    }
    ?>
    <div id="messages-section">
        <section id="users-message" class="message-frame">
            <h3>Chats</h3>
            <?php foreach ($ids as $id){

                $other_user = new User($dbh, $id)
                ?>
                <div class="user-info user-info-frame" data-user-id="<?= $id ?>">
                    <img src="<?= $other_user->getPic() ?>" alt="User picture">
                    <p class="user-name"><?= $other_user->getFirstName() . ' ' . $other_user->getLastName() ?></p>
                </div>
            <?php } ?>
        </section>

        <section id="direct-messages" class="message-frame">
            <h3>Direct Messages</h3>
            <button id="change-user"> <i class="fa fa-users" aria-hidden="true"></i></button>
            <div class="messages-list">
                <?php
                    if (!empty($open_message_user_id)){
                        require_once(__DIR__ . "/action_fetch_messages.php");
                    }
                ?>
                <!-- Placeholder for displaying messages -->
            </div>
        </section>
    </div>
    <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
    ?>
<?php } ?>

