<?php
require_once('templates/common.tpl.php');
require_once('templates/categories.tpl.php');
require_once('database/connection.php');
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
draw_main_page($dbh, $user_id);
draw_footer();

?>

<?php
    function draw_main_page($dbh, $user_id){ ?>

        <section id="filter">

            <form id="search-form" action="#" method="GET">
                <input autocomplete="off" type="search" id="search-input" name="search" placeholder="Search by product...">
                <button type="submit" id="search-button"><i class="fas fa-search"></i></button>
            </form>

            <div id="suggestions">

            </div>
            
            <input type="checkbox" id="checkbox-filter">
            <label for="checkbox-filter" id="toggle-filter"></label>

            <form id="filter-form" action="#" method="GET">
                <div class="filter-group">
                    <h3>Category</h3>
                    <ul>
                        <li>
                            <label><input type="radio" id="none" name="category" value="none" checked>No filter</label>
                        </li>
                        <li>
                            <label><input type="radio" id="decoration" name="category" value="decoration">Decoration</label>
                        </li>
                        <li>
                            <label><input type="radio" id="furniture" name="category" value="furniture">Furniture</label>
                        </li>
                    </ul>
                </div>
        
                <div id="furniture-options" class="filter-group hidden">
                    <h3>Furniture</h3>
                    <?php draw_furniture($dbh, true); ?>
                </div>

                <div id="decoration-options" class="filter-group hidden">
                    <h3>Decoration</h3>
                    <?php draw_decoration($dbh, true); ?>
                </div>

                <div class="filter-group">
                    <h3>Order by price</h3>
                    <label><input type="radio" id="order-low-to-high" name="price-order" value="low-to-high" checked>Low to High</label>
                    <label><input type="radio" id="order-high-to-low" name="price-order" value="high-to-low">High to Low</label>
                </div>
        
                <div class="filter-group">
                    <h3>Price Range</h3>
                    <input type="range" id="price-range" name="price-range" min="0" max="5000" step="50">
                    <span id="price-display">$0 - $5000</span>
                </div>
        
                <div class="filter-group">
                    <h3>Condition</h3>
                    <?php draw_condition($dbh, true); ?>
                </div>
        
                <div class="filter-group">
                    <h3>Size</h3>
                    <?php draw_size($dbh, true); ?>
                </div>
                
                <button type="submit">Apply Filters</button>
            </form>
            
        </section>
        <div id="products-wrapper">

            <section id="products">

                <div id="popular">
                    <p>Popular Products</p>
                </div>  

                <?php
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                    draw_products($dbh, $user_id, $searchTerm, $page);
                ?>
            </section>
    </div>
    <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
    ?>
    <?php } ?>