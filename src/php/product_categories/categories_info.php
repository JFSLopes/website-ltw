<?php
    function getAttributeId($dbh, $tableName, $name) {
        $stmt = $dbh->prepare("SELECT id FROM $tableName WHERE name = ?");
        $stmt->execute([$name]);
        
        $result = $stmt->fetch();
        
        if ($result) {
            return $result['id'];
        } else {
            return null;
        }
    }

    function addProductAttribute($dbh, $product_id, $category_id, $tableName) {
        $stmt = $dbh->prepare("INSERT INTO $tableName (product, category) VALUES (?, ?)");
        $stmt->execute([$product_id, $category_id]);
        
        return $stmt->rowCount() > 0;
    }

?>