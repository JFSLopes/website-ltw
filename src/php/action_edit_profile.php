<?php
require_once(__DIR__ . '/database/connection.php');

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($user_id) {
        $db = new DB();
        $dbh = $db->get_connection();
        
        if ($_FILES["profile_image"]["error"] == UPLOAD_ERR_OK) {
            // Check file type
            $allowed_types = ['image/png', 'image/jpeg', 'image/jpg']; // Allowed image types
            $file_type = $_FILES["profile_image"]["type"];
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['message'] = 'Only PNG, JPG, and JPEG files are allowed.';
            } else {
                // Get file extension
                $extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);

                // Generate new file name
                $new_filename = "user_" . $user_id . "." . $extension;
                $upload_dir = __DIR__ . "/../../images/user/";
                $target_file = $upload_dir . $new_filename;

                // Delete existing image, if any
                $existing_image = glob($upload_dir . "user_" . $user_id . ".*");
                if ($existing_image) {
                    unlink($existing_image[0]);
                }

                // Move uploaded file to target directory
                if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                    $_SESSION['message'] = 'Error uploading files.';
                }
                else{
                    $image_path = "../../images/user/" . $new_filename;
                    $stmt = $dbh->prepare('UPDATE User SET profilePic = ? WHERE id = ?');
                    $stmt->execute([$image_path, $user_id]);
                }
            }
        }

        $new_first_name = $_POST['first_name'];
        $new_last_name = $_POST['last_name'];
        $new_personal_info = $_POST['personal_info'];
        $new_phone_number = $_POST['phone_number'];
        $new_email = $_POST['new_email'];
        $new_password = $_POST['new_password'];
        
        if (!empty($new_password)) {
            $password_validation = check_password($new_password);
            if($password_validation != 'valid password'){
                $message = '';
                if($password_validation == 'size') $message = 'password size must be between 8-16.';
                if($password_validation == 'UpperCase and LowerCase') $message = 'password must have Upper and LowerCase characters.';
                if($password_validation == 'special') $message = 'password must have at least one special character';
                $_SESSION['message'] = $message;
            }else{
                $stmt = $dbh->prepare('UPDATE User SET firstName = ?, lastName = ?, personalInfo = ?, phoneNumber = ?, email = ?, password = ? WHERE id = ?');
                $stmt->execute([$new_first_name, $new_last_name, $new_personal_info, $new_phone_number, $new_email, sha1($new_password), $user_id]);
            }
        } else {
            $stmt = $dbh->prepare('UPDATE User SET firstName = ?, lastName = ?, personalInfo = ?, phoneNumber = ?, email = ? WHERE id = ?');
            $stmt->execute([$new_first_name, $new_last_name, $new_personal_info, $new_phone_number, $new_email, $user_id]);
        }
        if (!isset($_SESSION['message'])){
            $_SESSION['message'] = 'Profile edit successfully.';
        }
    } else {
        $_SESSION['message'] = 'Something unexpected happened.';
    }
}
header("Location: profile.php?user_id=" . $user_id);
exit();
?>

<?php
    function check_password($password){
        if((strlen($password) < 8) || (strlen($password) > 16)){
            return 'size';
        }
        else if(!preg_match("/[A-Z]/",$password) || !preg_match("/[a-z]/",$password)){
            return 'UpperCase and LowerCase';
        }
        else if(!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)){
            return 'special';
        }
        return "valid password";
    }
?>