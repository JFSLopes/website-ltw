<?php
require_once('database/connection.php');

$db = new DB();
$dbh = $db->get_connection();

session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

try{
    if (isset($_GET['category'])){
        $category_type = $_GET['category'];
        $category_type_name = '';
        if ($category_type == 'decoration'){ /// In case the category is a decoration
            if (isset($_GET['decoration'])){
                $category_type_name = $_GET['decoration'];
            } else {
                $_SESSION['message'] = 'Missing data.';
                throw new Exception('Missing data');
            }
        }

        else if ($category_type == 'furniture'){ /// In case the category is a furnitue
            if (isset($_GET['furniture'])){
                $category_type_name = $_GET['furniture'];
            } else {
                $_SESSION['message'] = 'Missing data.';
                throw new Exception('Missing data');
            }
        }


        /// Checking the size and condition filters
        $size = '';
        $condition = '';
        if (isset($_GET['condition'], $_GET['size'])){
            $size = $_GET['size'];
            $condition = $_GET['condition'];
        } else {
            $_SESSION['message'] = 'Missing data.';
            throw new Exception('Missing data');
        }
        /// Retrive the order and the max value
        $order_query = '';
        $max_value = '';
        if (isset($_GET['price-order'], $_GET['price-range'])){
            /// Get how the products should be sorted
            if ($_GET['price-order'] == 'low-to-high'){
                $order_query = 'ORDER BY p.price ASC;';
            } else if($_GET['price-order'] == 'high-to-low'){
                $order_query = 'ORDER BY p.price DESC;';
            }
            /// Get the maximum value
            $max_value = $_GET['price-range'];
        } else {
            $_SESSION['message'] = 'Missing data.';
            throw new Exception('Missing data');
        }
        /// Get the id
        $category_type_id = $category_type == 'furniture' ? getProductId($dbh, 'Furniture', $category_type_name) : getProductId($dbh, 'Decoration', $category_type_name);
        $sizeId = getProductId($dbh, 'Size', $size);
        $conditionId = getProductId($dbh, 'Condition', $condition);

        /// Create and execute the query
        if($category_type == 'furniture'){
            if($category_type_name == "none"){
                $query = "SELECT p.id from Product p 
                            JOIN Product_Furniture pf ON 
                            p.id = pf.product $order_query";
            }
            else{
                $query = "SELECT p.id from Product p 
                            JOIN Product_Furniture pf ON p.id = pf.product AND pf.category = $category_type_id
                            $order_query";
            }
        }
        else if ($category_type == 'decoration'){
            if($category_type_name == "none"){
                $query = "SELECT p.id from Product p 
                            JOIN Product_Decoration pd  ON p.id = pd.product 
                            $order_query";
            }
            else{
                $query = "SELECT p.id from Product p 
                            JOIN Product_Decoration pd ON p.id = pd.product AND pd.category = $category_type_id
                            $order_query";
            }
        }
        else{
            $query = "SELECT p.id from Product p $order_query";
        }
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $category_array = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $stmt = $dbh->prepare("SELECT id from Product p
                    WHERE p.price <= ? $order_query");
        $stmt->execute(array($max_value));
        $price_array = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $query;
        if ($conditionId == ''){
            $query = "SELECT p.id from Product p 
                    JOIN Product_Condition pc ON p.id = pc.product
                    $order_query";
        } else {
            $query = "SELECT p.id from Product p 
                    JOIN Product_Condition pc ON p.id = pc.product AND pc.category = $conditionId
                    $order_query";
        }
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $condition_array = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);


        if ($sizeId == ''){
            $query = "SELECT p.id from Product p 
                    JOIN Product_Size ps ON p.id = ps.product
                    $order_query";
        } else {
            $query = "SELECT p.id from Product p 
                    JOIN Product_Size ps ON p.id = ps.product AND ps.category = $sizeId
                    $order_query";
        }
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $size_array = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        $query = "SELECT p.id from Product p $order_query";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $productIds = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        if (!empty($size_array)){
            $productIds = array_intersect($productIds, $size_array);
        }
        if (!empty($category_array)){
            $productIds = array_intersect($productIds, $category_array);
        }
        if (!empty($condition_array)){
            $productIds = array_intersect($productIds, $condition_array);
        }
        if (!empty($price_array)){
            $productIds = array_intersect($productIds, $price_array);
        }

        $productIds = array_values($productIds);
    
        // Set the response content type to JSON
        header('Content-Type: application/json');

        // JSON-encode the array of product IDs
        echo json_encode($productIds);
    
    } else {
        $_SESSION['message'] = 'Missing data.';
        throw new Exception('Missing data');
    }

} catch(Exception $e) {
    if (!isset($_SESSION['message'])){
        $_SESSION['message'] = 'Something unexpected happened.';
    }
}
?>

<?php
function getProductId($dbh, $tableName, $productName) {
    $allowedTables = ['Furniture', 'Decoration', 'Size', 'Condition'];
    if (!in_array($tableName, $allowedTables)) {
        throw new InvalidArgumentException('Invalid table name');
    }

    $stmt = $dbh->prepare("SELECT id FROM $tableName WHERE name = :name");
    $stmt->bindParam(':name', $productName, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        return $result['id'];
    } else {
        return '';
    }
}
?>
