<?php 
require_once(__DIR__ . '/templates/common.tpl.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/user_info/user_info.php');

session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$db = new DB();
$dbh = $db->get_connection();
$user_id = $_SESSION['user_id'];

draw_header("Edit Profile Page", $dbh, $user_id);
draw_change_profile_page($dbh, $user_id);
draw_footer();
?>

<?php function draw_change_profile_page($dbh, $user_id) { 
    $user = new User($dbh, $user_id);
    ?>

    <h2>Edit your profile</h2>

    <form action="action_edit_profile.php" id="edit-profile-form" method="POST" enctype="multipart/form-data">
        <div class="edit_profile_container">
            <section id="profile_details">

                <div id="change-details">

                    <label for="change-image">Upload your new image:</label> 
                    <input type="file" id="change-image" name="profile_image"><br><br>

                    <label for="change-first-name">First Name:</label>
                    <input type="text" id="change-first-name" name="first_name" required placeholder="Update your first name" value="<?= $user->getFirstName() ?>"><br><br>
            
                    <label for="change-last-name">Last Name:</label>
                    <input type="text" id="change-last-name" name="last_name" required placeholder="Update your last name" value="<?= $user->getLastName() ?>"><br><br>

                    <label for="change-personal-info">Description:</label>
                    <input type="text" id="change-personal-info" name="personal_info" required placeholder="Update your profile description" value="<?= $user->getPersonalInfo() ?>"><br><br>

                    <label for="change-phone">Phone number:</label>
                    <input type="text" id="change-phone" name="phone_number" required placeholder="Update your phone number" value="<?= $user->getPhone() ?>"><br><br>

                    <label for="change-email">Email:</label>
                    <input type="text" id="change-email" name="new_email" required placeholder="Update your email" value="<?= $user->getEmail() ?>"><br><br>

                    <label for="change-password">Password:</label>
                    <input type="password" id="change-password" name="new_password" placeholder="Enter a new password"><br><br>

                </div>

                <button type="submit" id="edit-profile-button">Submit changes</button>

            </section>
        </div>
    </form>

<?php } ?> 