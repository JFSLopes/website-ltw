<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/review_info/reviews.php');
    require_once(__DIR__ . '/user_info/user_info.php');
    session_start();
    try{
        if(!isset($_POST['user_evaluated'],$_POST["user-review"],$_POST["value-review"],$_SESSION["user_id"])){
            $_SESSION['message'] = "Missing required parameters";
            throw new Exception("Missing requires parameters");   
        }
        $user_evaluated = $_POST['user_evaluated'];
        $value_review = $_POST['value-review'];
        $user_id = $_SESSION['user_id'];
        $user_review = $_POST['user-review'];


        if (!is_numeric($value_review) || $value_review < 1 || $value_review > 5) {
            $_SESSION['message'] = "Invalid review value. Please enter a number between 1 and 5.";
            throw new Exception("Invalid review value. Please enter a number between 1 and 5.");
        }

        $db = new DB();
        $dbh = $db->get_connection();
        $user_being_rated = new User($dbh,$user_evaluated);

        if(!$user_being_rated->is_user_valid()){
            $_SESSION['message'] = "Rating an invalid user";
            throw new Exception("Rating an invalid user.");
        }
        
        if($user_being_rated->getID() == $user_id){
            $_SESSION['message'] = "User cannot rate himself";
            throw new Exception("User cannot rate himself");
            
        }
        if(user_already_reviewed_user($dbh,$user_evaluated,$user_id)){
            $stmt = $dbh->prepare('UPDATE Review_User 
                                    SET evaluation = ?, comment = ?,date = ? 
                                    WHERE user_evaluated = ? AND user_evaluating = ?;');
            $stmt->execute(array($value_review,$user_review,date('Y-m-d H:i:s'),$user_evaluated,$user_id));
            $_SESSION['message'] = "Review posted successfully";
        }else{
            $stmt = $dbh->prepare('INSERT INTO Review_User (evaluation, comment, date, user_evaluated,user_evaluating) VALUES (?,?,?,?,?)');
            $stmt->execute(array($value_review,$user_review,date('Y-m-d H:i:s'),$user_evaluated,$user_id));
            $_SESSION['message'] = "Review updated successfully";
        }
        header("Location: profile.php?user_id=" . $user_evaluated);
        exit();
    } catch(Exception $e){
        if(!isset($_SESSION['message'])){
            $_SESSION['message'] = 'Something unexpected happened.';
        }
        header("Location: profile.php?user_id=" . $user_evaluated);
        exit();
    }
?>