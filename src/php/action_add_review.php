<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/review_info/reviews.php');
    require_once(__DIR__ . '/product_info/product_info.php');
    require_once(__DIR__ . '/selling_info/selling_info.php');
    session_start();

    try {
        if (!isset($_POST['user-review'], $_POST['value-review'], $_SESSION['user_id'], $_POST['product_id'])) {
            $_SESSION['message'] = "Missing required parameters";
            throw new Exception("Missing required parameters");
        }

        $user_review = $_POST['user-review'];
        $value_review = $_POST['value-review'];
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];

        if (!is_numeric($value_review) || $value_review < 1 || $value_review > 5) {
            $_SESSION['message'] = "Invalid review value. Please enter a number between 1 and 5.";
            throw new Exception("Invalid review value. Please enter a number between 1 and 5.");
        }

        $db = new DB();
        $dbh = $db->get_connection();

        $product = new Product($dbh, $product_id);
        if(!$product->isValid()){ // Product id is invalid
            $_SESSION['message'] = "Invalid product id.";
            throw new Exception("Invalid product id.");
        }

        if (user_already_reviewed($dbh, $user_id, $product_id)){
            $_SESSION['message'] = "User alredy reviewed.";
            throw new Exception("User alredy reviewed.");
        }
        /// User cannot comment his products
        if (get_user_id_selling($dbh, $product_id) == $user_id){
            $_SESSION['message'] = "Seller cannot comment his products.";
            throw new Exception("Seller cannot comment his products.");
        }

        $stmt = $dbh->prepare('INSERT INTO Review (evaluation, comment, date, user, product) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(array($value_review, $user_review, date('Y-m-d H:i:s'), $user_id, $product_id));

        header("Location: product.php?id=" . $product_id);
        exit();
    } catch (Exception $e) {
        if(!isset($_SESSION['message'])){
            $_SESSION['message'] = 'Something unexpected happened.';
        }
        header("Location: product.php?id=" . $product_id);
        exit();
    }
?>
