function addCart(button){
    var product_id = button.getAttribute('data-product-id');
    var xhr = new XMLHttpRequest();
    xhr.open('POST','action_add_cart_from_wishlist.php',true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            if(response.success) {
                window.location.reload();
            } else {
                console.log("Failed");
            }
        }
    };
    xhr.send('product_id=' + product_id);
    setTimeout(function() {
        window.location.reload();
    }, 100);
}
