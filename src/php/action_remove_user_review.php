<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');

    session_start();
    $db = new DB();
    $dbh = $db->get_connection();

    try{
        $dbh->beginTransaction();
        if(isset($_POST['user_rated']) && isset($_POST['user_rating'])){
            $user_rated = new User($dbh,$_POST['user_rated']);
            $user_rating = new User($dbh,$_POST['user_rating']);

            if($user_rated->is_user_valid() && $user_rating->is_user_valid()){
                $user_id = $_SESSION['user_id'];
                $user = new User($dbh,$user_id);

                if($user->is_user_admin() || $user_id == $user_rating){
                    $stmt = $dbh->prepare("DELETE FROM Review_User WHERE user_evaluated = ? AND user_evaluating = ?;");
                    $stmt->execute(array($user_rated->getID(),$user_rating->getID()));

                    $dbh->commit();
                    $_SESSION['message'] = "Review was eliminated successfully.";
                }else{
                    $_SESSION['message'] = 'You are not authorized to remove reviews that are not your own.';
                }
            }else {
                $_SESSION['message'] = 'One or both users envolved on review are invalid.';
            }
        }else{
            $_SESSION['message'] = 'Impossible to indentify the review.';
        }
        header("Location: profile.php?user_id=" . $user_rated->getID());
        exit();
    } catch (Exception $e) {
        $dbh->rollback();
        $_SESSION['message'] = 'Something unexpected happened.';
        header("Location: main.php");
        exit();
    }
?>


 