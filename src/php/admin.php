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

draw_header("Admin Page", $dbh, $user_id);
draw_admin_page($dbh);
draw_footer();

?>

<?php function draw_admin_page($dbh) { ?>

    <section id="admin-functionalities">
            <h2>Admin Functionalities</h2>
            <ul>
                <li>Elevate a user to admin status</li>
                <li>Introduce new item categories, sizes, conditions, and other pertinent entities</li>
                <li>Oversee and ensure the smooth operation of the entire system</li>
            </ul>
        </section>

        <section id="category-manegemnet">
            <h3>Category Management</h3>
                <select id="edit-operation" name="operation">
                    <option disabled selected value> -- Select an option -- </option>
                    <option value="delete">Delete category</option>
                    <option value="create">Create category</option>
                </select>

                 <!-- Delete Category Form -->
                <form id="delete-category-form" action="action_edit_categories.php" method="POST" class="hidden">
                    <input type="hidden" name="operation" value="delete">
                    <ul>
                        <li>
                            <label><input type="radio" id="decoration" name="category" value="decoration"> Decoration</label>
                        </li>
                        <li>
                            <label><input type="radio" id="furniture" name="category" value="furniture"> Furniture</label>
                        </li>
                        <li>
                            <label><input type="radio" id="size" name="category" value="size"> Size</label>
                        </li>
                        <li>
                            <label><input type="radio" id="condition" name="category" value="condition"> Condition</label>
                        </li>
                    </ul>

                    <div id="admin-decoration" class="hidden">
                        <?php draw_category_select($dbh, 'Decoration', 'Decoration') ?>
                    </div>
                    <div id="admin-furniture" class="hidden">
                        <?php draw_category_select($dbh, 'Furniture', 'Furniture'); ?>
                    </div>
                    <div id="admin-size" class="hidden">
                        <?php draw_category_select($dbh, 'Size', 'Size'); ?>
                    </div>
                    <div id="admin-condition" class="hidden">
                        <?php draw_category_select($dbh, 'Condition', 'Condition'); ?>
                    </div>

                    <button type="submit" id="delete-button">Delete</button>
                </form>

                 <!-- Create Category Form -->
                <form id="create-category-form" action="action_edit_categories.php" method="POST" class="hidden">
                    <input type="hidden" name="operation" value="create">
                    <label for="category-type">Category Type:</label>
                    <select id="category-type" name="category-type" required>
                        <option disabled selected value=''> -- Select an option -- </option>
                        <option value="Condition">Condition</option>
                        <option value="Size">Size</option>
                        <option value="Furniture">Furniture Type</option>
                        <option value="Decoration">Decoration Type</option>
                        
                    </select>
                    <label for="category-name">Category Name:</label>
                    <input type="text" id="category-name" name="category-name" required>
                    <button type="submit">Create Category</button>
                </form>
    </section>
    <?php
        if (isset($_SESSION['message'])) {
            popup($_SESSION['message']);
            unset($_SESSION['message']);
        }
    ?>

<?php } ?>

<?php
function draw_category_select($dbh, $tableName, $label) {
    $stmt = $dbh->prepare("SELECT * FROM $tableName");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $selectId = 'category-select-' . $tableName;

    echo "<label for='$selectId'>$label:</label>";
    echo "<select id='$selectId' name='category-select'>";
    echo "<option disabled selected value=''> -- Select an option -- </option>";
    foreach ($categories as $category) {
        echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
    }
    echo "</select>";
}
?>