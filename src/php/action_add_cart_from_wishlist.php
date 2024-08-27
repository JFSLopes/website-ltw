<?php
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/product_info/product_info.php');
session_start();
function addCart($user_id,$product_id,$dbh){
    $stmt = $dbh->prepare('INSERT INTO Cart (user,product) VALUES (?,?);');
    return $stmt->execute(array($user_id,$product_id));
}

$db = new DB();
$dbh = $db->get_connection();
$user_id = $_SESSION['user_id'];

if(isset($_POST['product_id'])){
    $product = New Product($dbh,$_POST['product_id']);
    if($product->isValid()){
        $stmt = $dbh->prepare('SELECT * FROM Cart WHERE user = ? AND product = ?');
        $stmt->execute(array($user_id, $_POST['product_id']));
        $result = $stmt->fetch();
        if(empty($result)){
        addCart($user_id,$_POST['product_id'],$dbh);
        }
        $_SESSION['message'] = 'Product added to the cart.';
        $response = array('success' => true, 'message' => 'Added to cart');
    }
    else{
        $_SESSION['message'] = 'Product is invalid.';
        $response = array('success' => false, 'message' => 'Product not valid');
    }
}
else{
    $_SESSION['message'] = 'Product is invalid.';
    $response = array('success' => false, 'message' => 'Product id not defined');
}
echo json_encode($response);
exit();

