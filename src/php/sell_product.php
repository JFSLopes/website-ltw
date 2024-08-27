<?php
require_once(__DIR__ . '/templates/common.tpl.php');
require_once(__DIR__ . '/templates/categories.tpl.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/display_message.php');

session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$db = new DB();
$dbh = $db->get_connection();
$user_id = $_SESSION['user_id'];

draw_header("Main Page", $dbh, $user_id);
draw_sell_product_page($dbh);
draw_footer();

?>

<?php
    function draw_sell_product_page($dbh){?>

        <h2>Sell your product here</h2>

        <form id="sell-product-form" action="action_sell_product.php" method="POST" enctype="multipart/form-data">
            
            <h3>Product Informations</h3>

            <div id="sell-options">

                <div id="radio-options">
                    <label><input type="radio" name="product-type" value="decoration" id="decoration"> Decoration</label>
                    <br>
                    <label><input type="radio" name="product-type" value="furniture" id="furniture"> Furniture</label>

                    <div id="furniture-options" class="hidden">
                        <h4>Furniture Categories</h4>
                        <?php draw_furniture($dbh, false) ?>
                    </div>
                    <div id="decoration-options" class="hidden">
                        <h4>Decoration Categories</h4>
                        <?php draw_decoration($dbh, false) ?>
                    </div>
                </div>

                <label for="product-name">Product Name:</label> 
                <input type="text" id="product-name" name="product-name" placeholder="Enter your product name" required>

                <label for="product-description">Product Description:</label> 
                <textarea name="product-description" id="product-description" placeholder="Describe briefly your product" required></textarea>

                <label for="price">Price:</label> 
                <input type="number" id="price" name="product-price" min="0" max="5000" step="0.01" placeholder="Enter the price" required>

                <label for="product-pictures">Product Pictures (up to 5 images):</label>
                <input type="file" id="product-pictures" name="product-images[]" multiple accept="image/png, image/jpeg, image/jpg">

                
                <label id="condition-sell">Condition:<br>
                    <?php draw_condition($dbh, false) ?>
                </label><br>

                <label id="size-sell">Size:<br>
                    <?php draw_size($dbh, false) ?>
                </label><br>

                <label id="quantity">Quantity:
                    <input type="number" min="1" id="increase-quantity" name="quantity" value="1" required>
                </label>

            </div>

            <h3>Seller Informations</h3>

            <div id="sell-details">

                <label for="address">Address:</label> 
                <input type="text" id="address" name="product-address" placeholder="Enter the address" required>

                <label for="zipcode">Zipcode:</label>
                <input type="text" id="zipcode" name="product-zipcode" placeholder="Enter the zipcode" required>

            </div>

            <button type="submit" id="sell-product-button">Submit Product</button>

        </form>
        <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
        ?>

    <?php }
?>