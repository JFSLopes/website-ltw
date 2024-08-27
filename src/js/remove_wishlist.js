function removeProductWishlist(button) {
    var productId = button.getAttribute('data-product-id'); 

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'action_remove_wishlist.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            if(response.success) {
                var row = document.getElementById(productId);
                row.parentNode.removeChild(row);
            } else {
                console.log("Deletion failed");
            }
        }
    };
    xhr.send('product_id=' + productId);
    setTimeout(function() {
        window.location.reload();
    }, 100);
}
