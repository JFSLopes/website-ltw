<?php

function product_being_sell($dbh, $product_id){
    $stmt = $dbh->prepare('SELECT user FROM Selling WHERE product = ?');
    $stmt->execute(array($product_id));
    $result = $stmt->fetch();

    return !empty($result['user']);
}

function get_user_id_selling($dbh, $product_id){
    $stmt = $dbh->prepare('SELECT user FROM Selling WHERE product = ?');
    $stmt->execute(array($product_id));
    $result = $stmt->fetch();

    return $result['user'];
}

function get_product_id_selling($dbh, $user_id){
    $stmt = $dbh->prepare('SELECT product FROM Selling WHERE user = ?');
    $stmt->execute(array($user_id));
    $result = $stmt->fetch();

    return $result['product'];
}

?>