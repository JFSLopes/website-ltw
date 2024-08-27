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
draw_header("Cart", $dbh, $user_id);
draw_cart($dbh,$user_id,$total);
draw_footer();

?>

<?php
function draw_cart($dbh,$user_id,$total){
    $stmt = $dbh->prepare('SELECT * FROM Cart w
                            WHERE w.user = ?');
    $stmt->execute(array($user_id));
    $cart = $stmt->fetchAll();
?>
    <section id="cart-products">
        <h2>Cart</h2>
        <form action="action_buy.php" id="buy-product" method = "POST">
            <table>
                <thead>
                    <tr>
                        <th>Quantity</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th></th> <!-- Column for delete button -->
                    </tr>
                </thead>
                <?php foreach ($cart as $row) {
                    $product = new Product($dbh, $row['product']);
                    if(!$product->isValid()) continue;
                    $total += $product->getPrice();
                    $product_id = $product->getId();
                    $price = $product->getPrice();
                    ?>
                <tbody>
                    <tr class="cart-product" id="<?= $product_id ?>">
                        <td>
                            <script src="../js/update_total_cart.js"></script>
                            <input type="number" min="1" id="quantity_<?=$product_id?>" name="quantity_<?=$product_id?>" value="1" max = "<?= $product->getQuantity() ?>" required onchange = "updateTotal(<?= $product_id ?>, <?= $price ?>)">
                        </td>
                        <td>
                            <a href="product.php?id=<?=$product_id?>"><?= $product->getName() ?></a>
                            <img src= <?= $product->getProductPic() ?> alt="Product Image" class="product-image">
                        </td>
                        <td> <?= $product->getPrice() ?> </td>
                        <td id = "total_<?= $product_id ?>" > <?= $product->getPrice() ?>  € </td>
                        <td>
                            <script src="../js/remove_cart.js"></script>
                            <button type="button" id = "delete-button" class="delete-product" data-product-id = "<?=$product_id?>" onclick = "removeProductCart(this)">Delete</button>
                        </td>
                    </tr>
                </tbody>
                <?php } ?>
                <tfoot>
                    <tr>
                        <td colspan="5" id = "final_price">
                            Total : <?= $total ?> €
                        </td>
                    </tr>
                </tfoot>
            </table>
            <input type="submit" value="Buy" id="buy-button">
        </form>
    </section>
    <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
    ?>

<?php
}
?>

