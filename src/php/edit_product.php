<?php
    require_once(__DIR__ . '/templates/common.tpl.php');
    require_once(__DIR__ . '/product_info/product_info.php');
    require_once(__DIR__ . '/selling_info/selling_info.php');
    session_start();
    if(!isset($_SESSION['user_id'])){
        header('Location: login.php');
        exit();
    }

    $DB = new DB();
    $dbh = $DB->get_connection();
    $user_id = $_SESSION['user_id'];

    draw_header("Edit Product", $dbh, $user_id);

    try {
        if (isset($_POST['edit_product'])) {
            $product_id = $_POST['edit_product'];
            $product = new Product($dbh, $product_id);
            if ($product->isValid()) {
                /// User is the owner
                if ($user_id == get_user_id_selling($dbh, $product_id)){
                    ?>
                        <form action="action_edit_product.php" method="POST" id="edit-form" enctype="multipart/form-data"> <!-- Submit to the same page -->
                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" value="<?= $product->getName() ?>">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description"><?= $product->getDescription() ?></textarea>
                            <label for="price">Price:</label>
                            <input type="number" id="price" name="price" value="<?= $product->getPrice() ?>">
                            <label for="product_pic">Product Picture (Edit will replace all old photos):</label>
                            <input type="file" multiple id="product_pic" name="product-images[]" accept="image/png, image/jpeg, image/jpg">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="<?= $product->getQuantity() ?>">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" value="<?= $product->getAddress() ?>">
                            <label for="address">Zipcode:</label>
                            <input type="text" id="zipcode" name="zipcode" value="<?= $product->getZipcode() ?>">
                            <input type="submit" value="Update Product">
                        </form>
                    <?php
                }
                else{
                    throw new Exception("User is not the owner.");
                }
            } else {
                throw new Exception("Invalid product.");
            }
        } else {
            throw new Exception("Product ID not provided.");
        }
    } catch (Exception $e) {
        header("Location: error.php?error=" . urlencode($e->getMessage()));
        exit();
    }

    draw_footer();
?>
