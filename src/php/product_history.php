<?php
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/product_info/product_info.php');

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $db = new DB();
    $dbh = $db->get_connection();
    
    // Fetch product history information
    $stmt = $dbh->prepare('SELECT p.publishDate, p.clicks, AVG(r.evaluation) AS average_review
                           FROM Product p
                           LEFT JOIN Review r ON p.id = r.product
                           WHERE p.id = ?;');
    $stmt->execute(array($product_id));
    $history = $stmt->fetch(PDO::FETCH_ASSOC);

    if (is_null($history['average_review'])) {
        $history['average_review'] = 'No reviews yet';
    } else {
        $history['average_review'] = round($history['average_review'], 2);
    }

    echo json_encode($history);
}
?>
