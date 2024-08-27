<?php
require (__DIR__ . '/product_info/product_info.php');
require (__DIR__ . '/database/connection.php');
require (__DIR__ . '/selling_info/selling_info.php');

// Retrieve the JSON data
$jsonData = file_get_contents('php://input');

// Decode the JSON data into array
$productIds = json_decode($jsonData);

// Check if productIds is an array
if (!is_array($productIds)) {
    exit('Invalid product IDs data');
}

session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$db = new DB();
$dbh = $db->get_connection();

// Display the products
echo '<ul>';
foreach ($productIds as $productId) {
    $product = new Product($dbh, $productId);
    if (!$product->isValid() || $product->getQuantity() == 0){
        continue;
    }
    if ($user_id != get_user_id_selling($dbh, $productId)){
        echo '<li class="product">';
        echo '<img src="' . $product->getProductPic() . '" alt="' . $product->getName() . '">';
        echo '<h3>' . $product->getName() . '</h3>';
        echo '<p class="price">Price: ' . $product->getPrice() . '€</p>';
        echo '<p class="more-info"><a href="product.php?id=' . $product->getID() . '">More info...</a></p>';
        echo '</li>';
    }
}
echo '</ul>';
?>
