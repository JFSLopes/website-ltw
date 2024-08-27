<?php
require_once(__DIR__ . '/database/connection.php');
session_start();

$db = new DB();
$dbh = $db->get_connection();
$user_id = $_SESSION['user_id'] ?? ''; 

$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';

if (!empty($searchTerm) && !empty($user_id)) {
    try {
        /// Does not show if products come from the logged user
        $stmt = $dbh->prepare('SELECT * FROM Product P JOIN Selling S ON P.id = S.product WHERE name LIKE :searchTerm AND S.user != :userID');
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
        $stmt->bindValue(':userID', $user_id);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($products);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    }
} else {
    echo json_encode(array());
}
?>
