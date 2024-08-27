<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');

    session_start();

    $db = new DB();
    $dbh = $db->get_connection();

    if (isset($_POST['email']) && isset($_POST['password'])) {
        if (try_login($dbh, $_POST['email'], $_POST['password'])) {
            $user_id = get_user_id($dbh, $_POST['email']);
            $_SESSION['user_id'] = $user_id;
            header('Location: ../php/main.php');
            exit();
        } else {
            $_SESSION['message'] = "Invalid email or password.";
            header('Location: ../php/login.php');
            exit();
        }
    }
?>

<?php
    function try_login($dbh, $email, $password){
        try {
            $stmt = $dbh->prepare('SELECT id, password FROM User WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user && password_verify($password, $user['password'])) {
                return $user['id'];
            } else {
                return false; // Invalid credentials
            }
        } catch (PDOException $e) {
            return false; // Error occurred
        }
    }
?>

