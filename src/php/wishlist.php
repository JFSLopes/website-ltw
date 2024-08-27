<?php
require_once(__DIR__ . '/templates/common.tpl.php');
require_once(__DIR__ . '/user_info/user_info.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/product_info/product_info.php');
require_once(__DIR__ . '/display_message.php');


session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$db = new DB();
$dbh = $db->get_connection();
$user_id = $_SESSION['user_id'];
$total = 0;
draw_header("Wishlist", $dbh, $user_id);
draw_wishlist($dbh,$user_id);
draw_footer();

?>


<?php function draw_wishlist($dbh, $user_id) { 
    $stmt = $dbh->prepare('SELECT * FROM Wishlist wi
    WHERE wi.user = ?');
    $stmt->execute(array($user_id));
    $wishlist = $stmt->fetchAll();
?>

    <section id="wishlist-products"> 
        <h2>Wishlist</h2>

        <div id="empty">
            <?php if (empty($wishlist)) { ?>
                <p>Your wishlist is empty. Add some products!</p>
            <?php } else { ?> 
        </div>

            <ul> 
                <?php foreach ($wishlist as $item) {
                    $product = new Product($dbh, $item['product']);
                    if (!$product->isValid()) continue;
                    $product_id = $product->getId();
                    $price = $product->getPrice();
                ?>   
                
                <li class="product-item-wishlist">
                    <a href="product.php?id=<?= $product->getId() ?>">
                        <img src= <?= $product->getProductPic() ?> alt="Product Image" class="product-image-wishlist">
                    </a>

                    <div id="product-details-wishlist">
                        <p class="product-name-wishlist"><?= $product->getName() ?> </p>
                        <p class="product-price-wishlist"><?= $product->getPrice() ?> €</p>

                        <div class="button-container">
                            <script src="../js/add_cart_from_wishlist.js"></script>
                            <button type="button" id="add-cart-wishlist" class="add-product-wishlist" data-product-id="<?=$product_id?>" onclick ="addCart(this)"> Add to Cart</button>
                            <script src="../js/remove_wishlist.js"></script>
                            <button type="button" id="delete-button-wishlist" class="delete-product-wishlist" data-product-id="<?=$product_id?>" onclick="removeProductWishlist(this)">Remove</button>
                        </div>
                    </div>

                </li>

                <?php } ?>

            </ul>

        <?php } ?>

    </section>
    <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
    ?>

<?php } ?>
