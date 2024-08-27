<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/selling_info/selling_info.php');
    require_once(__DIR__ . '/user_info/user_info.php');

    session_start();

    $db = new DB();
    $dbh = $db->get_connection();

    try {
        // Begin the transaction
        $dbh->beginTransaction();

        if(isset($_POST['remove_product'])) {
            $product_id = $_POST['remove_product'];
            
            if(is_numeric($product_id) && $product_id > 0) {
                $user_id = $_SESSION['user_id'];
                $user = new User($dbh, $user_id);
                
                if($user->is_user_admin() || $user_id == get_user_id_selling($dbh, $product_id)){
                    /// Remove from the selling
                    $stmt = $dbh->prepare("DELETE FROM Selling WHERE product = ?");
                    $stmt->execute([$product_id]);

                    /// Remove from the wislist
                    $stmt = $dbh->prepare("DELETE FROM Cart WHERE product = ?");
                    $stmt->execute([$product_id]);

                    /// Set quantity to 0
                    $stmt = $dbh->prepare("UPDATE Product SET quantity = 0 WHERE id = ?");
                    $stmt->execute([$product_id]);
                    
                    $dbh->commit();

                    $_SESSION['message'] = 'Product was removed successfully.';
                } else {
                    $_SESSION['message'] = 'You are not authorized to remove products that are not your own.';
                }
            } else {
                $_SESSION['message'] = 'Product is invalid.';
            }
        } else {
            $_SESSION['message'] = 'No product has given.';
        }
        header("Location: main.php");
        exit();
    } catch (Exception $e) {
        $dbh->rollback();
        $_SESSION['message'] = 'Something unexpected happened.';
        header("Location: main.php");
        exit();
    }
?>