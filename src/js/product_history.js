document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-history-icon').forEach(function(icon) {
        icon.addEventListener('click', function() {
            let productId = this.getAttribute('data-product-id');
            fetchProductHistory(productId);
        });
    });
});

function fetchProductHistory(productId) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'product_history.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let history = JSON.parse(xhr.responseText);
            displayProductHistory(history);
        }
    };
    xhr.send('product_id=' + productId);
}

function displayProductHistory(history) {
    // Remove any existing popups before showing a new one
    let existingPopup = document.querySelector('.popup');
    if (existingPopup) {
        existingPopup.remove();
    }

    let popUp = document.createElement('div');
    popUp.className = 'popup';
    let content = `
        <div class="popup-content-product">
            <span class="close-btn-product">&times;</span>
            <h3>Product History</h3>
            <ul>
                <li>Publish Date: ${history.publishDate}</li>
                <li>Clicks: ${history.clicks}</li>
                <li>Average Review: ${history.average_review}</li>
            </ul>
        </div>
    `;
    popUp.innerHTML = content;
    document.body.appendChild(popUp);
    
    document.querySelector('.close-btn-product').addEventListener('click', function() {
        document.body.removeChild(popUp);
    });
}
