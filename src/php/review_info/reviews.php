<?php

function get_reviews_by_product($dbh, $product_id){
    $stmt = $dbh->prepare('SELECT evaluation, comment, date, user FROM Review WHERE product = ?');
    $stmt->execute(array($product_id));
    $result = $stmt->fetchAll();

    return $result;
}

function user_already_reviewed($dbh, $user_id, $product_id){
    $stmt = $dbh->prepare('SELECT * FROM Review WHERE user = ? AND product = ?');
    $stmt->execute(array($user_id, $product_id));
    $result = $stmt->fetch();

    return !empty($result);
}

function get_reviews_by_user($dbh, $user_id){
    $stmt = $dbh->prepare('SELECT evaluation, comment, date, user_evaluating FROM Review_User WHERE user_evaluated = ?');
    $stmt->execute(array($user_id));
    $result = $stmt->fetchAll();

    return $result;
}

function user_already_reviewed_user($dbh,$user_rated,$user_rating){
    $stmt = $dbh->prepare('SELECT * FROM Review_User WHERE user_evaluated = ? AND user_evaluating = ?;');
    $stmt->execute(array($user_rated, $user_rating));
    $result = $stmt->fetch();

    return !empty($result);
}