<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');
    require_once(__DIR__ . '/product_info/product_info.php');
    require_once(__DIR__ . '/selling_info/selling_info.php');

    session_start();

    $date = date('m/d/Y h:i:s a', time());
    $user = $_SESSION['user_id'];


    $db = new DB();
    $dbh = $db->get_connection();
    try{
        $dbh->beginTransaction();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(empty($_POST)){
                $_SESSION['message'] = "There is no products on Cart.";
                throw new Exception("There is no products on Cart.");
            }
            $quantities = array();
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'quantity_') === 0) {
                    $product_id = substr($key, strlen('quantity_'));
                    $product = new Product($dbh,$product_id);
                    if($product->isValid()){ /// Check if product is valid
                        $stmt = $dbh->prepare('SELECT user, product FROM Cart WHERE user = ? AND product = ?;');
                        if($stmt->execute(array($user,$product_id))){ /// Check if user really has this products in the DB
                            if(!empty($stmt->fetch())){
                                /// Check if the product quantity is greater than 0
                                if ($product->getQuantity() < $value) {
                                    $_SESSION['message'] = "Product does not have enough quantity";
                                    throw new Exception("Product does not have enough quantity");
                                }
                                $quantities[$product_id] = $value;
                            }
                            else{
                                $_SESSION['message'] = "Values not found in Cart Table";
                                throw new Exception("Values not found in Cart Table");
                            }
                        }
                    }
                    else{
                        $_SESSION['message'] = "Query not executed";
                        throw new Exception("Query not executed");
                    }
                }
            }
            foreach($quantities as $product_id => $quantity){
                /// Adicionar à tabela Sold
                $user_selling_id = get_user_id_selling($dbh, $product_id);
               
                $stmt = $dbh->prepare('INSERT INTO Sold (user, user_bought, product, quantity, date) VALUES (?,?,?,?,?);');
                $stmt->execute(array($user_selling_id, $user, $product_id, $quantity, $date));
                
                /// Adicionar à tabela Bought
                $stmt = $dbh->prepare('INSERT INTO Bought (user, product, quantity, date) VALUES (?,?,?,?);');
                $stmt->execute(array($user, $product_id, $quantity, $date));
                

                /// Update Product quantity tables
                $stmt = $dbh->prepare('UPDATE Product SET quantity = quantity - ? WHERE id = ?;');
                $stmt->execute(array($quantity, $product_id));
            }
        }
        $dbh->commit();
        header("Location: checkout.php");
        exit();  

    }catch(Exception $e) {
        $dbh->rollback();
        if(!isset($_SESSION['message'])){
            $_SESSION['message'] = 'Something unexpected happened.';
        }
        header("Location: cart.php");
        exit();
    }

?>
