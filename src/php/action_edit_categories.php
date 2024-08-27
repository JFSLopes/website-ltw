<?php
session_start();

require_once(__DIR__ . '/database/connection.php');

$db = new DB();
$dbh = $db->get_connection();
$user_id = $_SESSION['user_id'];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];

        if ($operation === 'delete') {
            // Check if the category ID is provided
            if (isset($_POST['category-select'], $_POST['category'])) {
                $category_id = $_POST['category-select'];
                $table = ucfirst($_POST['category']);

                // Validate the table name
                $allowed_tables = ['Size', 'Condition', 'Furniture', 'Decoration'];
                if (in_array($table, $allowed_tables)) {
                    // Perform the deletion
                    $stmt = $dbh->prepare("DELETE FROM $table WHERE id = ?");
                    $stmt->execute([$category_id]);
                    $_SESSION['message'] = 'Category removed successfully.';
                } else {
                    $_SESSION['message'] = 'Removing the category failed.';
                }
            } else {
                $_SESSION['message'] = 'Something unexpected happened.';
            }
        } elseif ($operation === 'create') {
            if (isset($_POST['category-type']) && isset($_POST['category-name'])) {
                $category_type = $_POST['category-type'];
                $category_name = $_POST['category-name'];

                // Validate the category type
                $allowed_types = ['Condition', 'Size', 'Furniture', 'Decoration'];
                if (in_array($category_type, $allowed_types)) {
                    // Perform the insertion of the new category into the appropriate table
                    $stmt = $dbh->prepare("INSERT INTO $category_type (name) VALUES (?)");
                    $stmt->execute([$category_name]);
                    $_SESSION['message'] = 'Category added successfully.';
                } else {
                    $_SESSION['message'] = 'Adding the category failed.';
                }
            } else {
                $_SESSION['message'] = 'Something unexpected happened.';
            }
        }
    } else {
        $_SESSION['message'] = 'Something unexpected happened.';
    }
} else {
    $_SESSION['message'] = 'Something unexpected happened.';
}
header("Location: admin.php");
exit();
?>
