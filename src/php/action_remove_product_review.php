<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');

    session_start();
    $db = new DB();
    $dbh = $db->get_connection();

    try{
        $dbh->beginTransaction();
        if(isset($_POST['product_id']) && isset($_POST['user_id'])){
            $product_id = $_POST['product_id'];
            $user_review = $_POST['user_id'];

            if(is_numeric($product_id) && $product_id > 0){
                $user_id = $_SESSION['user_id'];
                $user = new User($dbh,$user_id);

                if($user->is_user_admin() || $user_id == $user_review){
                    $stmt = $dbh->prepare("DELETE FROM Review WHERE user = ? AND product = ?;");
                    $stmt->execute(array($user_review,$product_id));

                    $dbh->commit();
                    $_SESSION['message'] = "Review was eliminated successfully.";
                }else{
                    $_SESSION['message'] = 'You are not authorized to remove reviews that are not your own.';
                }
            }else {
                $_SESSION['message'] = 'Product is invalid.';
            }
        }else{
            $_SESSION['message'] = 'Impossible to indentify the review.';
        }
        header("Location: product.php?id=" . $product_id);
        exit();
    } catch (Exception $e) {
        $dbh->rollback();
        $_SESSION['message'] = 'Something unexpected happened.';
        header("Location: main.php");
        exit();
    }
?>


 