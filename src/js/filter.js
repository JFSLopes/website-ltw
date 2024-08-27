const filterForm = document.getElementById('filter-form');

// Check if filter-form element exists
if (filterForm) {
    filterForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        const queryString = new URLSearchParams(formData).toString();
        const filterUrl = `filter_products.php?${queryString}`;
        const drawUrl = `draw_products.php`;

        // Send GET request to server
        fetch(filterUrl)
        .then(response => response.json())
        .then(data => {
            // Process the response (list of product IDs)
            drawProducts(data, drawUrl);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
}

function drawProducts(productIds, drawUrl) {
    // Send POST request to server
    fetch(drawUrl, {
        method: 'POST',
        body: JSON.stringify(productIds)
    })
    .then(response => response.text())
    .then(html => {
        // Update products section with the response
        document.getElementById('products').innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
