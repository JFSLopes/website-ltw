<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');
    session_start();

    $db = new DB();
    $dbh = $db->get_connection();

    if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['password'])) {
        $password_validation = check_password($_POST['password']);
        if($password_validation != 'valid password'){
            $message = '';
            if($password_validation == 'size') $message = 'Password size must be between 8-16.';
            if($password_validation == 'UpperCase and LowerCase') $message = 'Password must have upper and lowercase characters.';
            if($password_validation == 'special') $message = 'Password must have at least one special character';
            $_SESSION['message'] = $message;
            header('Location: register.php');
            exit();
        }
        $registration_result = try_register($dbh, $_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['password'], $_POST['phone']);

        if ($registration_result === 'Success') {
            // Registration was successful, redirect the user
            $user_id = get_user_id($dbh, $_POST['email']);
            $_SESSION['user_id'] = $user_id;
            $path = '../php/main.php';
            header('Location: ' . $path);
            exit();
        } else {
            // Registration failed, display appropriate error message
            $message = '';
            if ($registration_result === 'email_exists') {
                $message = 'Email already exists.';
            } else {
                $message = 'An unknown error occurred.';
            }
            $_SESSION['message'] = $message;
            header('Location: register.php');
            exit();
        }
    }
    $_SESSION['message'] = 'An unknown error occurred.';
    header('Location: register.php');
    exit();
?>

<?php
    function try_register($dbh, $fname, $lname, $email, $password, $phone){
        try {
            $options = ['cost' => 12];
            // Hash the password 
            $hashed_password = password_hash($password, PASSWORD_DEFAULT, $options);

            $stmt = $dbh->prepare('INSERT INTO User (firstName, lastName, email, password, phoneNumber) VALUES (?, ?, ?, ?, ?);');
            $success = $stmt->execute(array($fname, $lname, $email, $hashed_password, $phone));
    
            if ($success) {
                return 'Success'; // Registration was successful
            } else {
                return 'unknown_error'; // Other error occurred
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return 'email_exists'; // Email already exists
            } else {
                return 'unknown_error'; // Other error occurred
            }
        }
    } 
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