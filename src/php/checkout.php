<?php
require_once(__DIR__ . '/user_info/user_info.php');
require_once(__DIR__ . '/database/connection.php');
require_once(__DIR__ . '/templates/common.tpl.php');
require_once(__DIR__ . '/display_message.php');

session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}
$db = new DB();
$dbh = $db->get_connection();

draw_header('Checkout Page',$dbh, $_SESSION['user_id']);
draw_checkout_options();
draw_footer();

?>

<?php function draw_checkout_options(){ ?>
    <h2>Checkout</h2>
    <form action="action_checkout.php" method="POST" id="checkout_form">
        
        <h3>Delivery Address</h3>

        <div id="delivery">

            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name"><br><br>
        
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" placeholder="Enter your address" required><br><br>
        
            <label for="city">City:</label>
            <input type="text" id="city" name="city"  placeholder="Enter your city" required><br><br>
        
            <label for="zipcode">Zip Code:</label>
            <input type="text" id="zipcode" name="zipcode" placeholder="Enter your zipcode" required><br><br>

        </div>

        <h3>Payment methods</h3>

        <div id="pay">

            <label for="method">Method:</label>
            <select id="method" name="method" required>
                <option value="" disabled selected>Select a payment method</option>
                <option value="Paypal">Paypal</option>
                <option value="Card">Debit/Credit Card</option>
                <option value="MB WAY"> MB Way </option>
                <option value="Apple Pay"> Apple Pay</option>
            </select><br><br>

        </div>

        <button type="submit" id="checkout-button"> Order </button>

    </form>
<?php 
} 
?>