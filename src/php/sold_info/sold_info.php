<?php
    function get_user_id_sold($dbh, $product_id){
        $stmt = $dbh->prepare('SELECT user FROM Sold WHERE product = ?');
        $stmt->execute(array($product_id));
        $result = $stmt->fetch();
    
        return $result['user'];
    }
?>