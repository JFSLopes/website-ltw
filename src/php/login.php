<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');
    require_once(__DIR__ . '/templates/common.tpl.php');
    require_once(__DIR__ . '/display_message.php');

$db = new DB();
$dbh = $db->get_connection();

session_start();

draw_login_page($dbh);


?>

<?php function draw_login_page($dbh) { ?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/authentication/login.css">
    <link rel="stylesheet" href="../css/display_messages.css">
</head>

<header>
        <h1 id="login_h1">Second Chance Market</h1>
</header>

<body id="login_body">
    <form action="action_login.php" method="post" id="login_form">
        <label id="login_email">Email: <input type="text" placeholder="Enter your email" name="email" required></label>
        <label id="login_password">Password: <input type="password" placeholder="Enter your password" name="password" required></label>
        <button type="submit" id="login_button">Log in</button>
    
        <div id="reg">
            <a href="register.php">Register</a>
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