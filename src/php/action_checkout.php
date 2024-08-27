<?php
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/user_info/user_info.php');
    require_once(__DIR__ . '/product_info/product_info.php');
    require_once(__DIR__ . '/selling_info/selling_info.php');

    session_start();

    $user = $_SESSION['user_id'];

    $db = new DB();
    $dbh = $db->get_connection();

    try{
        $dbh->beginTransaction();
        if(!isset($_POST['fullname'],$_POST['address'],$_POST['city'],$_POST['zipcode'],$_POST['method'])){
            $_SESSION['message'] = 'Missing arguments';
            throw new Exception("Missing arguments");
        }
        $address = $_POST['address'];
        $zipcode = $_POST['zipcode'];
        $method = $_POST['method'];

        $stmt = $dbh->prepare('SELECT product FROM Cart WHERE user = ?;');
        if($stmt->execute(array($user))){
            $products = $stmt->fetchAll();
            foreach($products as $product){
                //Dar update à tabela Sold
                $user_selling_id = get_user_id_selling($dbh,$product['product']);

                $stmt = $dbh->prepare('UPDATE Sold SET address = ?, zipcode = ?, method = ? WHERE user_bought = ? AND user = ? AND product = ?;');
                $stmt->execute(array($address, $zipcode, $method, $user, $user_selling_id,$product['product']));

                //Dar update à tabela Bought
                $stmt = $dbh->prepare('UPDATE Bought SET  method = ? WHERE user = ? AND product = ?;');
                $stmt->execute(array($method,$user,$product['product']));


                //Remover do Carrinho
                $stmt = $dbh->prepare('DELETE FROM Cart WHERE product = ? and user = ?;');
                $stmt->execute(array($product['product'], $user));
            }

        }
        $stmt = $dbh->prepare('DELETE FROM Selling
                                WHERE product in (SELECT id 
                                                    FROM Product
                                                    WHERE quantity = 0);');
        $stmt->execute();
        $_SESSION['message'] = "Your order is being processed.";                                      
        $dbh->commit();
        header("Location: main.php");
        exit();  



    }catch(Exception $e){
        $dbh->rollback();
        if(!isset($_SESSION['message'])){
            $_SESSION['message'] = 'Something unexpected happened.';
        }
        header("Location: cart.php");
        exit();
    }