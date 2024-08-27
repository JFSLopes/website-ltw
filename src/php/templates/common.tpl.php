<?php
    require_once('user_info/user_info.php');
    require_once('database/connection.php');
    
    function draw_header($title, $dbh, $user_id){
        $user = new User($dbh, $user_id);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $title ?></title>
            <link rel="stylesheet" href="../css/main_page/top_bar.css">
            <link rel="stylesheet" href="../css/main_page/filter.css">
            <link rel="stylesheet" href="../css/main_page/products.css">
            <link rel="stylesheet" href="../css/product_page/product.css">
            <link rel="stylesheet" href="../css/product_page/review.css">
            <link rel="stylesheet" href="../css/product_page/product_info.css">
            <link rel="stylesheet" href="../css/product_page/comments.css">
            <link rel="stylesheet" href="../css/cart/cart.css">
            <link rel="stylesheet" href="../css/sell/sell_product.css">
            <link rel="stylesheet" href="../css/admin/admin_page.css">
            <link rel="stylesheet" href="../css/profile/profile_page.css">
            <link rel="stylesheet" href="../css/footer.css">
            <link rel="stylesheet" href="../css/profile/change_profile.css">
            <link rel="stylesheet" href="../css/profile/send_message.css">
            <link rel="stylesheet" href="../css/sell/sell_product.css">
            <link rel="stylesheet" href="../css/messages/messages.css">
            <link rel="stylesheet" href="../css/profile/products_section.css">
            <link rel="stylesheet" href="../css/wishlist/wishlist.css">
            <link rel="stylesheet" href="../css/display_messages.css">
            <link rel="stylesheet" href="../css/cart/checkout.css">
            <link rel="stylesheet" href="../css/shipping-form.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <script src="../js/show_hidden_filter.js" defer></script>
            <script src="../js/search_bar.js" defer></script>
            <script src="../js/show_messages.js" defer></script>
            <script src="../js/change_image.js" defer></script>
            <script src="../js/edit_categories.js" defer></script>
            <script src="../js/filter.js" defer></script>
            <script src="../js/product_history.js" defer></script>
            <script src="../js/toggle_menu.js" defer></script>
            <script src='https://kit.fontawesome.com/a6ef6b97ef.js' crossorigin='anonymous' defer></script>
 
        </head>
        <body>
            <header id="page-header">
                <h1><a href="main.php">Second Chance Market</a></h1>
                <nav id="menu">
                    <ul>
                        <?php 
                            if (isset($_SESSION['user_id']) && $user->is_user_admin()){ ?>
                                <li><a href="admin.php">Admin</a></li> 
                        <?php }    
                        ?>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="wishlist.php">Wishlist</a></li>
                        <li><a href="profile.php?user_id=<?= $user_id ?>">Profile</a></li>
                        <li><a href="sell_product.php">Sell product</a></li>
                    </ul>
                </nav>
                <button id="menu-toggle" class="fa-solid fa-chevron-down"></button>
            </header>
<?php } ?>

<?php
    function draw_footer(){ ?>
        <footer>
            <div class="footer-container">
                <div id="website">
                    <p>Second Chance Market</p>
                </div>
                <div id="copyright">
                    <p>© 2024 Second Chance Market. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
    </html>
    <?php } ?>