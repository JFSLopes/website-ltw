<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/product_info/product_info.php');
    require_once(__DIR__.'/selling_info/selling_info.php');
    session_start();

    try {
        if (!isset($_POST['product_id'])){ // Making sure a product id has been received
            $_SESSION['message'] = 'There is no product to be added.';
        }

        $product_id = $_POST['product_id'];
        $user_id = $_SESSION['user_id'];

        $db = new DB();
        $dbh = $db->get_connection();

        $product = new Product($dbh, $product_id);
        if(!$product->isValid()){ // Product id is invalid
            $_SESSION['message'] = 'Product is invalid.';
        }
       
        // Check if the user already has the product
        $stmt = $dbh->prepare('SELECT * FROM Wishlist WHERE user = ? AND product = ?');
        $stmt->execute(array($user_id, $product_id));
        $result = $stmt->fetch();
        if (empty($result)){ // User has not added the product yet
            $stmt = $dbh->prepare('INSERT INTO Wishlist (user, product) VALUES (?, ?);');
            $stmt->execute(array($user_id, $product_id));
        }

        $_SESSION['message'] = 'Product added to wishlist.';
        header("Location: product.php?id=" . $product_id);
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = 'Something unexpected happened.';
        header("Location: product.php?id=" . $product_id);
        exit();
    }
?>
