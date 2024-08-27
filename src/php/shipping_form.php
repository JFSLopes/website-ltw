<?php
    
    require_once(__DIR__ . '/user_info/user_info.php');
    require_once(__DIR__.'/product_info/product_info.php');
    require_once(__DIR__ . '/database/connection.php');
    require_once(__DIR__ . '/templates/common.tpl.php');
    require_once(__DIR__ . '/display_message.php');
    require_once(__DIR__ . '/location/get_location_info.php');
    
    session_start();
    if(!isset($_SESSION['user_id'])){
        header('Location: login.php');
        exit();
    }

    $db = new DB();
    $dbh = $db->get_connection();
    $user = $_SESSION['user_id'];
    $sold_id = $_GET['id'];
    draw_shipping_form($dbh,$user,$sold_id);
?>


<?php function draw_shipping_form($dbh, $user_id, $sold_id){ ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shipping Form</title>
        <link rel="stylesheet" href="../css/shipping-form.css">
        <link rel="stylesheet" href="../css/display_messages.css">
    </head>

    <?php

    $product_id;
    $receiver_id;
    $to_address;
    $to_zipcode;
    try{
        $stmt = $dbh->prepare("SELECT product, user_bought, address, zipcode FROM Sold WHERE id = ?;");
        $stmt->execute([$sold_id]);
        $result1 = $stmt->fetch();
        $product_id = $result1['product'];
        $receiver_id = $result1['user_bought'];
        $to_address = $result1['address'];
        $to_zipcode = $result1['zipcode'];
    } catch (Exception $e){
        $_SESSION['message'] = "Something unexpected happended.";
        header("Location: profile.php?user_id=" . $user_id);
        exit();
    }
    $product = New Product($dbh,$product_id);
    $seller_user = New User($dbh,$user_id);
    $seller_name = $seller_user->getFirstName() . " " . $seller_user->getLastName();
    $seller_phone = $seller_user->getPhone();
    $seller_email = $seller_user->getEmail();
    $from_address =  $product->getAddress();
    $from_zipcode = $product->getZipcode();

    $receiver_user = New User($dbh, $receiver_id);
    $receiver_name = $receiver_user->getFirstName() . " " . $receiver_user->getLastName();
    $receiver_phone = $receiver_user->getPhone();
    $receiver_email = $receiver_user->getEmail();

    $from_coordinates = get_coordinates_locationiq($from_address);
    $to_coordinates = get_coordinates_locationiq($to_address);

    if ($from_coordinates && $to_coordinates) {
        $distance = calculate_distance($from_coordinates, $to_coordinates);
        $delivery_price = round(calculate_delivery_price($distance), 2);
    } else {
        $distance = null;
        $delivery_price = "Unable to calculate";
    }
    
    ?>

        <div id="title_ship">
            <h2>Shipping form</h2>
        </div>

        <div id="shipping">

            <h3>Shipper Information</h3>

            <div id= "shipper-info">
                <p class="line"><span class='label'>Name:</span> <?= $seller_name ?></p>
                <p class="line"><span class='label'>Phone:</span> <?= $seller_phone ?></p>
                <p class="line"><span class='label'>Email:</span> <?= $seller_email ?></p>
                <p class="line"><span class='label'>Shipping from:</span> <?= $from_address ?></p>
                <p class="line"><span class='label'>Zip Code:</span> <?= $from_zipcode ?></p>
            </div>

            <h3> Receiver Information </h3>

            <div id= "receiver-info">
                <p class="line"><span class='label'>Name:</span> <?= $receiver_name ?></p>
                <p class="line"><span class='label'>Phone:</span> <?= $receiver_phone ?></p>
                <p class="line"><span class='label'>Email:</span> <?= $receiver_email ?></p>
                <p class="line"><span class='label'>Shipping to:</span> <?= $to_address ?></p>
                <p class="line"><span class='label'>Zip Code:</span> <?= $to_zipcode ?></p>
            </div>
            <p>Price: <?= $delivery_price ?>€</p>
        </div>

   <?php } ?>

<?php

function calculate_distance($coord1, $coord2) {
    $earth_radius = 6371;

    $lat_from = deg2rad($coord1['lat']);
    $lon_from = deg2rad($coord1['lng']);
    $lat_to = deg2rad($coord2['lat']);
    $lon_to = deg2rad($coord2['lng']);

    $lat_delta = $lat_to - $lat_from;
    $lon_delta = $lon_to - $lon_from;

    $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) +
      cos($lat_from) * cos($lat_to) * pow(sin($lon_delta / 2), 2)));

    return $angle * $earth_radius;
}

function calculate_delivery_price($distance) {
    $base_price = 2.00;
    $price_per_km = 0.20;

    return $base_price + ($price_per_km * $distance);
}

?>