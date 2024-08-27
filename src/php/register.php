<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');
    require_once(__DIR__ . '/templates/common.tpl.php');
    require_once(__DIR__ . '/display_message.php');

$db = new DB();
$dbh = $db->get_connection();

session_start();

draw_register_page($dbh);
?>


<?php function draw_register_page($dbh) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/authentication/register.css">
    <link rel="stylesheet" href="../css/display_messages.css">
</head>

    <header>
        <h1 id ="register_title">Second Chance Market</h1>
    </header>

    <body id = "reg_body">
        <form id = "register_form" action="action_register.php"  method="post">
            <label id = "reg_fname">First name: <input type="text" name = "fname" placeholder="Enter your first name" required ></label>
            <label id = "reg_sname"  >Last name: <input type="text" name = "lname" placeholder="Enter your last name" required></label>
            <label id = "reg_phone_number" >Phone number: <input type="text"  name = "phone" placeholder="Enter your phone number" required></label>
            <label id = "reg_email">Email: <input type="text" name = "email"  placeholder="Enter your email" required></label>
            <label id = "reg_pass"  >Password: <input type="password" name = "password" placeholder="Create a password" required></label>
            <button id = "reg_button" type="submit">Register</button>
        
            <div id="log">
                <a href="login.php">Already have an account?</a>
            </div>
        </form>
        <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
        ?>
    </body>
</html>

<?php } ?>