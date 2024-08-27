<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/product_info/product_info.php');

    session_start();

    function update_product_details($dbh, $product_id, $name, $description, $price, $quantity, $address, $zipcode) {
        $stmt = $dbh->prepare('UPDATE Product SET name = ?, description = ?, price = ?, quantity = ?, address = ?, zipcode = ? WHERE id = ?');
        return $stmt->execute([$name, $description, $price, $quantity, $address, $zipcode, $product_id]);
    }

    $DB = new DB();
    $dbh = $DB->get_connection();
    
    $dbh->beginTransaction();
    try {
        if (isset($_POST['name'], $_POST['description'], $_POST['price'], $_POST['quantity'], $_POST['product_id'], $_POST['address'], $_POST['zipcode'])){
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $product_id = $_POST['product_id'];
            $address = $_POST['address'];
            $zipcode = $_POST['zipcode'];

            if ($quantity <= 0){
                $_SESSION['message'] = 'Quantity cannot be set to 0 or less. Delete the product instead.';
                throw new Exception("Quantity cannot be set to 0 or less. Delete the product instead.");
            }
            
            /// Check if id is valid
            $product = new Product($dbh, $product_id);
            if (!$product->isValid()) {
                $_SESSION['message'] = 'Product is invalid.';
                throw new Exception("Invalid product ID.");
            }

            if (update_product_details($dbh, $product_id, $name, $description, $price, $quantity, $address, $zipcode)) {
                $dbh->commit();

                /// Override the photos if needed
                if (!empty($_FILES['product-images']['name'][0])){
                    // Delete existing product images directory if it exists
                    $productDir = __DIR__ . "/../../images/product/" . $product_id;

                    $files = glob($productDir . '/*');
                    foreach($files as $file){ /// Remove all files, aka images
                        if (is_file($file)){
                            unlink($file);
                        }
                    }
                
                    // Create the product directory
                    if (!mkdir($productDir, 0777, true) && !is_dir($productDir)) {
                        $_SESSION['message'] = 'Something unexpected happened';
                        throw new Exception("Failed to create product directory.");
                    }
                
                    // Validate and upload each new image
                    $imageCount = count($_FILES['product-images']['name']);
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
                                    throw new Exception("Error uploading files.");
                                }
                            } else {
                                $_SESSION['message'] = 'Error uploading files.';
                                throw new Exception("Error uploading files.");
                            }
                        } else {
                            $_SESSION['message'] = 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.';
                            throw new Exception("Invalid file type. Only JPG, JPEG, and PNG files are allowed.");
                        }
                    }
                }

                $_SESSION['message'] = 'Product edited successfully.';
                header("Location: product.php?id=" . $product_id);
                exit();
            } else {
                $_SESSION['message'] = 'Failed to update product details.';
                throw new Exception("Failed to update product details.");
            }
        } else {
            $_SESSION['message'] = 'Form data missing.';
            throw new Exception("Form data missing.");
        }
    } catch (Exception $e) {
        $dbh->rollback();
        
        if (!isset($_SESSION['message'])){
            $_SESSION['message'] = 'Something unexpected happened.';
        }

        header("Location: product.php?id=" . $product_id);
        exit();
    }
?>
