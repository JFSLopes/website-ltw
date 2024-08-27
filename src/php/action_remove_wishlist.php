<?php

function removeProduct($dbh,$product_id,$user_id){
    $stmt = $dbh->prepare('DELETE FROM Wishlist  WHERE product = ? AND user = ?;');
    return $stmt->execute(array($product_id,$user_id));
}
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/product_info/product_info.php');
    session_start();

    $db = new DB();
    $dbh = $db->get_connection();
    $user_id = $_SESSION['user_id'];

    if(isset($_POST['product_id'])){
        $product = new Product($dbh,$_POST['product_id']);
        if($product->isValid()){
            removeProduct($dbh,(int)$_POST['product_id'],(int)$user_id);
            $response = array('success' => true, 'message' => 'Removed from wishlist');
        }
        else{
            $response = array('success' => false, 'message' => 'Product not valid');

        }
    }
    else{
        $response = array('success' => false, 'message' => 'Product id not defined');
    }
    echo json_encode($response);
    exit();
?>
