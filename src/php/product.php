<?php

    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/templates/common.tpl.php');
    require_once(__DIR__ . '/selling_info/selling_info.php');
    require_once(__DIR__ . '/sold_info/sold_info.php');
    require_once(__DIR__ . '/product_info/product_info.php');
    require_once(__DIR__ . '/display_message.php');
    require_once(__DIR__ . '/location/get_location_info.php');

    session_start();
    if(!isset($_SESSION['user_id'])){
        header('Location: login.php');
        exit();
    }

    $DB = new DB();
    $dbh = $DB->get_connection();
    $user_id = $_SESSION['user_id'];

    draw_header("Product", $dbh, $user_id);

    // Product ID is set
    if(isset($_GET['id'])) {
        $product_id = $_GET['id'];
        $user_id = $_SESSION['user_id'];

        /// Product ID valid
        $product = new Product($dbh, $product_id);
        if ($product->isValid()){
            if (get_user_id_selling($dbh, $product_id) != $user_id){ /// Only update clicks if is other user clicking it
                $stmt = $dbh->prepare('UPDATE Product SET clicks = clicks + 1 WHERE id = ?;');
                $stmt->execute([$product_id]);
            }

            if (product_being_sell($dbh, $product_id)){
                $user_id_selling = get_user_id_selling($dbh, $product_id);
                draw_product_page($dbh, $product_id, $user_id_selling, $user_id);
            }
            else{
                $user_id_selling = get_user_id_sold($dbh, $product_id);
                draw_product_page($dbh, $product_id, $user_id_selling, $user_id);
            }
        } else {
            echo "<p>The product does not exist.</p>";
        }
    } else {
        echo "<p>The product does not exist.</p>";
    }
    draw_footer();
?>

<?php
    function draw_product_page($dbh, $product_id, $user_id_selling, $user_id){ ?>
        <div id="product-page">
            <?php draw_product_details($dbh, $product_id, $user_id_selling, $user_id); ?>
            <?php draw_product_add_review($dbh, $product_id); ?>
            <?php draw_product_reviews($dbh, $product_id, $user_id); ?>
        </div>
        <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
        ?>
    <?php }
?>

<?php
    function draw_product_details($dbh, $product_id, $user_id_selling, $user_id){
        require_once(__DIR__ . '/user_info/user_info.php');

        $product = new Product($dbh, $product_id);
        $user_selling = new User($dbh, $user_id_selling);
        $user = new User($dbh, $user_id);

        $address = $product->getAddress() . ", " . $product->getZipcode();
        $coordinates = get_coordinates_locationiq($address);
        if ($coordinates) {
            $lat = $coordinates['lat'];
            $lon = $coordinates['lng'];
        } else {
            // Default coordinates
            $lat = 0;
            $lon = 0;
        }

        ?>
        <section id="product-details">
            <?php if(isset($_SESSION['user_id']) && ($user->is_user_admin() || $user_id == $user_id_selling) && $product->getQuantity() != 0 && product_being_sell($dbh, $product_id)){ ?>
                <form id="remove-product-form" action="action_remove_product.php" method="POST">
                    <input type="hidden" name="remove_product" value="<?= $product_id ?>">
                    <button class="admin-only remove-product-btn" type="submit" id="remove">Remove Product</button>
                </form>
            <?php } ?>
            <?php if(isset($_SESSION['user_id']) && ($user_id == $user_id_selling) && ($product->getQuantity() != 0) && product_being_sell($dbh, $product_id)){ ?>
                <form id="edit-product-form" action="edit_product.php" method="POST">
                    <input type="hidden" name="edit_product" value="<?= $product_id ?>">
                    <button class="admin-only remove-product-btn" type="submit" id="edit">Edit Product</button>
                </form>
            <?php } ?>
            <h3 id="product-title"> <?= $product->getName() ?></h3>
            <p id="date">Published: <?= $product->getPublishDate() ?> </p>


            <?php $productPics = $product->getProductPics(); ?>
            <div id="product-images-container">
                <?php if (!empty($productPics)): ?>
                    <div id="images">
                        <?php foreach ($productPics as $index => $pic): ?>
                            <img class="product-image <?= $index === 0 ? '' : 'hidden' ?>" src="<?= $pic ?>" alt="Product Image">
                        <?php endforeach; ?>
                    </div>
                    <div id="button-change">
                        <?php if (count($productPics) > 1): ?>
                            <button id="change-image-btn" ><i style="font-size:24px" class="fa">&#xf152;</i> </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div id="images">
                        <img id="product-image" src="../../images/product/default.png" alt="Default Product Image">
                </div>
                <?php endif; ?>
            </div>


            <p id="product-categories">
            <?php 
                $categories = $product->getCategories();
                $numCategories = count($categories);
                foreach ($categories as $index => $category) {
                    echo $category;
                    if ($index < $numCategories - 1) {
                        echo ', ';
                    }
                }
            ?>
            </p>
            <p id="product-description"> <?= $product->getDescription() ?></p>
            <p class="price-display">Price: <?= $product->getPrice() ?>€</p>
            <p class="quantity-display">Quantity: <?= $product->getQuantity() ?></p>
            
            <div class="map-container">
                <h3><?= $address ?></h3>
                <iframe
                    src="https://www.google.com/maps/embed/v1/view?key=MOCKED_API_KEY&center=<?= $lat ?>,<?= $lon ?>&zoom=15">
                </iframe>
            </div>
            <?php if($user_id != $user_id_selling){?>
                <form id="cart-form" action="action_add_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                    <button type="submit">Add to Cart</button>
                </form>

                <form id="wishlist-form" action="action_add_wishlist.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                    <button type="submit" id="wishlistbtn"> Add to Wishlist</button>
                </form>
                
            <?php }?>
            <section id="seller-details">
                <h3>Seller Information</h3>
                <a href="profile.php?user_id=<?= $user_id_selling ?>"><img src="<?= $user_selling->getPic() ?>" alt="Seller Profile Picture"></a>
                <a href="profile.php?user_id=<?= $user_id_selling ?>"><p> <?= $user_selling->getFirstName() . " " . $user_selling->getLastName() ?> </p></a>
            </section>
        </section>
    <?php }
?>

<?php
    function draw_product_add_review($dbh, $product_id){ ?>
        <section id="add-review">
            <h3>Add a Review</h3>
            <form id="review-form" action="action_add_review.php" method="POST">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <label>Your Review:<textarea id="user-review" name="user-review" required></textarea></label>
                <label>Review: <input type="number" min="1" max="5" id="value-review" name="value-review" required></label>
                <button type="submit">Submit Review</button>
            </form>
        </section>
    <?php }
?>
<?php
    function draw_product_reviews($dbh, $product_id, $user_id){
        require_once(__DIR__ . '/review_info/reviews.php');
        require_once(__DIR__ . '/user_info/user_info.php');
        $reviews = get_reviews_by_product($dbh, $product_id);
        $sum = 0;
        $num = 0;
        foreach ($reviews as $review){
            $sum += intval($review['evaluation']);
            $num++;
        }
        if ($num != 0){ ?>
            <section id="product-reviews">
                <h3>Reviews</h3>
                <p>Average Rating: <?= round($sum/$num , 1) ?></p>
                <?php foreach ($reviews as $review){
                    $user = new User($dbh, $review['user']);
                    ?>
                    <div class="review">
                        <div class="user-info">
                            <a href="profile.php?user_id=<?= $user->getID()?>"><img src="<?= $user->getPic() ?>" alt="User profile picture"></a>
                            <a href="profile.php?user_id=<?= $user->getID()?>"><p><?= $user->getFirstName() . " " . $user->getLastName() ?></p></a>
                        </div>
                        <p class="review-rating">Rating: <?= $review['evaluation'] ?>/5</p>
                        <p class="review-description"><?= $review['comment'] ?></p>
                        <p class="review-date">Date: <?= $review['date'] ?></p>
                        <form id="remove_review_form" action="action_remove_product_review.php" method = "POST">
                            <input type="hidden" name = "product_id" value="<?= $product_id?>">
                            <input type="hidden" name = "user_id" value="<?= $user->getID() ?>">
                            <button class="admin-only remove-product-review-btn" type="submit" id="remove">Remove Review</button>
                        </form>
                    </div>
                <?php } ?>
        </section>
        <?php } ?>
    <?php }
?>
