<?php 

require_once(__DIR__ . '/user_info/user_info.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/templates/common.tpl.php');
require_once(__DIR__ . '/display_message.php');

session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$db = new DB();
$dbh = $db->get_connection();
draw_header("Profile Page", $dbh, $_SESSION['user_id']);
if (isset($_GET['user_id'])){

    $user_being_watched = new User($dbh, $_GET['user_id']); /// User which profile is being seen
    $user_logged_in = new User($dbh, $_SESSION['user_id']); /// User logged in
    if ($user_being_watched->is_user_valid()){
        draw_profile_page($dbh, $user_being_watched, $user_logged_in);
        if($user_being_watched->getID() != $user_logged_in->getID()){
            draw_user_add_review($dbh,$user_being_watched->getID());
        }
        draw_user_reviews($dbh,$user_being_watched);
    }
    else{
        echo "<p> Not able to load user profile</p>";
    }
}
else{
    echo '<p> Not able to load user profile </p>';
}
draw_footer();
?>

<?php function draw_profile_page($dbh, $user_being_watched, $user_logged_in) {
    ?>
    <div class="container-profile">
        <section id="profile-details">
            <div class="profile-content">
                <div class="profile-image">
                    <img src="<?= $user_being_watched->getPic(); ?>" alt="User profile picture">
                </div>
                <div class="profile-details">
                    <p class="name"> 
                        <?= $user_being_watched->getFirstName() . " " . $user_being_watched->getLastName() ?>
                    </p>
                    <p id="personal-info"> <?= $user_being_watched->getPersonalInfo() ?></p>
                    <p id="phone-number">Phone Number: <?= $user_being_watched->getPhone() ?>
                    </p>
                </div>
            </div>
            <?php 
                if($user_being_watched->getID() == $user_logged_in->getID()){ ?>
                    <form action="change_profile.php" method="post" id="changebutton"> <!-- Should only be in the page if it is his profile -->
                        <button type="submit">Edit Profile</button>
                    </form>
                <?php } 
            ?>

            <?php 
                if($user_being_watched->getID() == $user_logged_in->getID()){ ?>
                    <form action="message.php" method="post" id="changebutton"> <!-- Should only be in the page if it is his profile -->
                        <button type="submit">See messages</button>
                    </form>
                <?php } 
            ?>

            <?php 
                if($user_being_watched->getID() != $user_logged_in->getID()){ ?>
                    <form action="message.php?other_user_id=<?= $user_being_watched->getID() ?>" method="POST" id="messagebutton"> <!-- Should only be in the page if not his profile -->
                        <button type="submit">Send Message</button>
                    </form>
                <?php } 
            ?>

            <?php 
                if($user_being_watched->getID() == $user_logged_in->getID()){ ?>
                    <form action="action_logout.php" method="post" id="logoutbutton"> <!-- Should only be in the page if it is his profile -->
                        <button type="submit">Log out</button>
                    </form>
                <?php } 
            ?>    
        </section>
        
        <?php if ($user_being_watched->getID() == $user_logged_in->getID()){ ?>
            <section id="bought-products" class="product-container">
                <h3>Bought Products</h3>
                <div class="product-list">
                    <?php bought_products($dbh, $user_logged_in->getID()) ?>
                </div>
            </section>

            <section id="selling-products" class="product-container">
                <h3>Selling Products</h3>
                <div class="product-list">
                    <?php selling_products($dbh, $user_logged_in->getID()) ?>
                </div>
            </section>

            <section id="sold-products" class="product-container">
                <h3>Sold Products</h3>
                <div class="product-list">
                    <?php sold_products($dbh, $user_logged_in->getID()) ?>
                </div>
            </section>
        <?php } ?>
        
        <?php 
            if($user_logged_in->is_user_admin() && !$user_being_watched->is_user_admin()){ ?>
                <section id="admin-actions" class="admin-only">
                    <form action="action_elevate_to_admin.php" method="POST">
                        <input type="hidden" name="other_user_id" value="<?= $user_being_watched->getID() ?>">
                        <button type="submit">Elevate to Admin</button>
                    </form>
                </section>
            <?php } 
        ?>  
    </div>
    <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
        ?>
    <?php } ?>




<?php
    function bought_products($dbh, $user_id){
        require_once(__DIR__ . '/product_info/product_info.php');

        $stmt = $dbh->prepare('SELECT product, quantity FROM Bought WHERE user = ?;');
        $stmt->execute(array($user_id));

        $results = $stmt->fetchAll();
        $has_product = false;
        foreach ($results as $result){
            $has_product = true;
            $product = new Product($dbh, $result['product']);
            ?>
                <div class="profile-product">
                    <a href="product.php?id=<?= $result['product'] ?>"><img src="<?= $product->getProductPic() ?>" alt="Product Image"></a>
                    <p><?= $product->getName() ?></p>
                    <p>Quantity: <?= $result['quantity'] ?></p>
                    <p><?= round($product->getPrice() * $result['quantity'], 2) ?> €</p>
                </div>
        <?php }
        if (!$has_product){
            echo '<p>No products to be shown.</p>';
        }
    }
?>

<?php
    function selling_products($dbh, $user_id){
        require_once(__DIR__ . '/product_info/product_info.php');

        $stmt = $dbh->prepare('SELECT product FROM Selling WHERE user = ?;');
        $stmt->execute(array($user_id));

        $results = $stmt->fetchAll();
        $has_product = false;
        foreach ($results as $result){
            $has_product = true;
            $product = new Product($dbh, $result['product']);
            ?>
                <div class="profile-product">
                    <a href="product.php?id=<?= $result['product'] ?>"><img src="<?= $product->getProductPic() ?>" alt="Product Image"></a>
                    <p><?= $product->getName() ?></p>
                    <p>Quantity: <?= $product->getQuantity() ?></p>
                    <p><?= $product->getPrice()?> €</p>
                    <i id="info-button" class="fas fa-info-circle product-history-icon" data-product-id="<?= $product->getID() ?>"></i>
                </div>
        <?php }
        if (!$has_product){
            echo '<p>No products to be shown.</p>';
        }
    }
?>

<?php
    function sold_products($dbh, $user_id){
        require_once(__DIR__ . '/product_info/product_info.php');

        $stmt = $dbh->prepare('SELECT product, quantity, user_bought FROM Sold WHERE user = ?;');
        $stmt->execute(array($user_id));

        $results = $stmt->fetchAll();
        $has_product = false;
        foreach ($results as $result){
            $has_product = true;
            $product = new Product($dbh, $result['product']);
            $sold_id;
            try{
                $stmt = $dbh->prepare("SELECT id FROM Sold WHERE user_bought = ? AND product = ?;");
                $stmt->execute([$result['user_bought'], $product->getID()]);
                $result1 = $stmt->fetch();
                $sold_id = $result1['id'];
            } catch (Exception $e){
                $_SESSION['message'] = "Something unexpected happended.";
                header("Location: profile.php?user_id=" . $user_id);
                exit();
            }

            ?>
                <div class="profile-product">
                    <a href="product.php?id=<?= $result['product'] ?>"><img src="<?= $product->getProductPic() ?>" alt="Product Image"></a>
                    <p><?= $product->getName() ?></p>
                    <p>Quantity: <?= $result['quantity'] ?></p>
                    <p><?= $product->getPrice()?> €</p>
                    <a href="shipping_form.php?id=<?= $sold_id ?>"> <i style='font-size:24px;color:black' class='fas'>&#xf0d1;</i> </a>
                </div>
        <?php }
        if (!$has_product){
            echo '<p>No products to be shown.</p>';
        }
    }
?>
<?php
    function draw_user_add_review($dbh, $user_rated){ ?>
        <section id="add-review-user">
            <h3>Add a Review</h3>
            <form id="review-form-user" action="action_add_review_user.php" method="POST">
                <input type="hidden" name="user_evaluated" value="<?= $user_rated ?>">
                <label>Your Review:<textarea id="user-review" name="user-review" required></textarea></label>
                <label>Review: <input type="number" min="1" max="5" id="value-review" name="value-review" required></label>
                <button type="submit">Submit Review</button>
            </form>
        </section>
<?php }
?>

<?php
    function draw_user_reviews($dbh, $user_evaluated){
        require_once(__DIR__ . '/user_info/user_info.php');
        require_once(__DIR__. '/review_info/reviews.php');
        $reviews = get_reviews_by_user($dbh,$user_evaluated->getID());
        $sum = 0;
        $num = 0;
        foreach ($reviews as $review){
            $sum += intval($review['evaluation']);
            $num++;
        }
        if ($num != 0){ ?>
            <section id="user-reviews">
                <h3>Reviews</h3>
                <p>Average Rating: <?= round($sum/$num , 1) ?></p>
                <?php foreach ($reviews as $review){
                    $user = new User($dbh, $review['user_evaluating']);
                    ?>
                    <div class="review">
                        <div class="user-info">
                            <a href="profile.php?user_id=<?= $user->getID()?>"><img src="<?= $user->getPic() ?>" alt="User profile picture"></a>
                            <a href="profile.php?user_id=<?= $user->getID()?>"><p><?= $user->getFirstName() . " " . $user->getLastName() ?></p></a>
                        </div>
                        <p class="review-rating">Rating: <?= $review['evaluation'] ?>/5</p>
                        <p class="review-description"><?= $review['comment'] ?></p>
                        <p class="review-date">Date: <?= $review['date'] ?></p>
                        <form id="remove_user_review_form" action="action_remove_user_review.php" method = "POST">
                            <input type="hidden" name = "user_rated" value="<?= $user_evaluated->getID()?>">
                            <input type="hidden" name = "user_rating" value="<?= $user->getID() ?>">
                            <button class="admin-only remove-user-review-btn" type="submit" id="remove">Remove Review</button>
                        </form>
                    </div>
                <?php } ?>
        </section>
        <?php } ?>
    <?php }
?>