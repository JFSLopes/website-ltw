<?php
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/product_categories/categories_info.php');

session_start();

function addProductToSelling($dbh, $productId, $userId) {
    $stmt = $dbh->prepare('INSERT INTO Selling (user, product) VALUES (?, ?)');
    $stmt->execute(array($userId, $productId));
}

$db = new DB();
$dbh = $db->get_connection();

$images = [];

try {
    // Begin the transaction
    $dbh->beginTransaction();

    if(isset($_POST['product-type'], $_POST['product-name'], $_POST['product-description'], $_POST['product-price'], $_POST['condition'], $_POST['size'], $_POST['quantity'], $_POST['product-address'], $_POST['product-zipcode'])) {
        // All variables are set, proceed with further actions
        $type = $_POST['product-type'];
        $name = $_POST['product-name'];
        $description = $_POST['product-description'];

        $price = $_POST['product-price'];
        $price = str_replace(',', '.', $price);
        if (!is_numeric($price) || $price <= 0 || $price >= 5000) {
            throw new Exception("Invalid price input!");
        }
        $price = number_format($price, 2);

        $condition = getAttributeId($dbh, "Condition", $_POST['condition']);
        $size = getAttributeId($dbh, "Size", $_POST['size']);
        $date = date('Y-m-d H:i:s');
        $quantity = $_POST['quantity'];
        $address = $_POST['product-address'];
        $zipcode = $_POST['product-zipcode'];

        $category;
        if ($type === "decoration" && isset($_POST['decoration'])){
            $category = getAttributeId($dbh, "Decoration", $_POST['decoration']);
        }
        else if ($type === "furniture" && isset($_POST['furniture'])){
            $category = getAttributeId($dbh, "Furniture", $_POST['furniture']);
        }
        else {
            $_SESSION['message'] = 'Invalid product type';
            throw new Exception("Invalid product type");
        }
        /// Make sure the id's are valid
        if ($size == null || $condition == null || $category == null){
            $_SESSION['message'] = 'Invalid product data';
            throw new Exception("Invalid product data");
        }
        if($quantity <= 0){
            $_SESSION['message'] = 'Invalid quantity.';
            throw new Exception("Invalid quantity");
        }

        /// Add the product
        $stmt = $dbh->prepare('INSERT INTO Product (name, description, publishDate, price, quantity, address, zipcode)
                            VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array($name, $description, $date, $price, $quantity, $address, $zipcode));

        if ($stmt->rowCount() <= 0){
            $_SESSION['message'] = 'Adding Product Failed.';
            throw new Exception("Adding Product Failed.");
        }
        /// Add to the selling table
        $product_id = $dbh->lastInsertId();
        $user_id = $_SESSION['user_id'];
        addProductToSelling($dbh, $product_id, $user_id);

        /// Add the product Categories
        if (!addProductAttribute($dbh, $product_id, $size, "Product_Size")){
            $_SESSION['message'] = 'Adding the product categories failed.';
            throw new Exception("Adding the product categories failed");
        }
        if (!addProductAttribute($dbh, $product_id, $condition, "Product_Condition")){
            $_SESSION['message'] = 'Adding the product categories failed.';
            throw new Exception("Adding the product categories failed");
        }
        if ($type === "decoration" && !addProductAttribute($dbh, $product_id, $category, "Product_Decoration")){
            $_SESSION['message'] = 'Adding the product categories failed.';
            throw new Exception("Adding the product categories failed");
        }
        if ($type === "furniture" && !addProductAttribute($dbh, $product_id, $category, "Product_Furniture")){
            $_SESSION['message'] = 'Adding the product categories failed.';
            throw new Exception("Adding the product categories failed");
        }

        // Commit transaction
        $dbh->commit();

        // Check if any images were uploaded
        if (!empty($_FILES['product-images']['name'][0])) {
            $imageCount = count($_FILES['product-images']['name']);
            
            // Validate each image
            for ($i = 0; $i < $imageCount; $i++) {
                $fileName = $_FILES['product-images']['name'][$i];
                $fileTmpName = $_FILES['product-images']['tmp_name'][$i];
                $fileSize = $_FILES['product-images']['size'][$i];
                $fileError = $_FILES['product-images']['error'][$i];
                $fileType = $_FILES['product-images']['type'][$i];

                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png'];

                if (in_array($fileExt, $allowedExtensions)) {
                    if ($fileError === 0) {
                        $newFileName = "product_" . uniqid('', true) . "." . $fileExt;
                        $uploadDir = __DIR__ . "/../../images/product/";

                        // Create the product directory
                        $productDir = $uploadDir . $product_id . "/";
                        if (!is_dir($productDir)) {
                            mkdir($productDir, 0777, true);
                        }

                        $targetFile = $productDir . $newFileName;
                        if (move_uploaded_file($fileTmpName, $targetFile)) {
                            $images[] = $targetFile;
                        } else {
                            $_SESSION['message'] = 'Error uploading files.';
                        }
                    } else {
                        $_SESSION['message'] = 'Error uploading files.';
                    }
                } else {
                    $_SESSION['message'] = 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.';
                }
            }
        }
    } else {
        $_SESSION['message'] = 'Missing required parameters.';
    }

    if (!isset($_SESSION['message'])){
        $_SESSION['message'] = 'Product is now for sale.';
    }

    header("Location: sell_product.php");
    exit();
} catch (Exception $e) {
    $dbh->rollback();
    
    if (!isset($_SESSION['message'])){
        $_SESSION['message'] = 'Something unexpected happened.';
    }
    header("Location: sell_product.php");
    exit();
}
