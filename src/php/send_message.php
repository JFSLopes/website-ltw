<?php 
require_once(__DIR__ . '/templates/common.tpl.php');
require_once(__DIR__ . '/database/connection.php');

session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$destinatary_id = $_GET['destinatary_id'];

$db = new DB();
$dbh = $db->get_connection();
$user_id = $_SESSION['user_id'];

$stmt = $dbh->prepare("SELECT id, firstName, lastName FROM User WHERE id = :destinatary_id");
$stmt->bindParam(':destinatary_id', $destinatary_id);
$stmt->execute();
$destinatary = $stmt->fetch(PDO::FETCH_ASSOC);

draw_header("Send Message Page", $dbh, $user_id);
draw_send_message($dbh);
draw_footer();
?>

<?php function draw_send_message($dbh) { ?>

    <div class="send-message"> 
        <h2> Send message to: <?php echo $destinatary['firstName'] . ' ' . $destinatary['lastName']; ?> </h2>
        <img src="<?php echo $destinatary['pic']; ?>" alt="Destinatary profile pic">
        <form action="action_send_message.php" method="post">
            <input type="hidden" name="destinatary_id" value="<?php echo $destinatary['id']; ?>">
            <label for="message" id="message">Message: </label>
            <textarea id="message-box" name="message" placeholder="Write your message here" rows="4" cols="50"></textarea>
            <button type="submit" id="send-button">Send Message</button>
        </form>
    </div>

<?php } ?>