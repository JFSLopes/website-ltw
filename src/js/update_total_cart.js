function updateTotal(product_id,price){
    var quantity = document.getElementById('quantity_' + product_id).value;
    var total = price * quantity;
    document.getElementById('total_' + product_id).textContent = total.toString() + " €";
    updateFinalPrice();
}
function updateFinalPrice(){
    var final_price = 0;
    var price_quantity = document.querySelectorAll('td[id^="total_"]');
    price_quantity.forEach(function (element) {
        final_price += parseFloat(element.textContent);
    });
    document.getElementById("final_price").textContent = final_price.toString() + " €";
}