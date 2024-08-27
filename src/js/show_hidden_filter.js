const priceRangeInput = document.querySelector('#price-range');
if (priceRangeInput) {
    priceRangeInput.addEventListener('input', updatePriceDisplay);
}

function updatePriceDisplay() {
    const priceRange = document.querySelector('#price-range');
    const priceDisplay = document.querySelector('#price-display');
    if (priceRange && priceDisplay) {
        priceDisplay.textContent = `$0 - $${priceRange.value}`;
    }
}

function show_filter() {
    const noneRadio = document.querySelector('#none');
    let is_filter = false;
    if (noneRadio){
        is_filter = true;
    }
    const furnitureRadio = document.querySelector('#furniture');
    const decorationRadio = document.querySelector('#decoration');
    const furnitureOptions = document.querySelector('#furniture-options');
    const decorationOptions = document.querySelector('#decoration-options');

    if (furnitureRadio && decorationRadio && furnitureOptions && decorationOptions) {
        if (is_filter){
            noneRadio.addEventListener('change', function(){
                if (this.checked) {
                    furnitureOptions.classList.add('hidden');
                    decorationOptions.classList.add('hidden');
                }
            })
        }

        furnitureRadio.addEventListener('change', function() {
            if (this.checked) {
                furnitureOptions.classList.remove('hidden');
                decorationOptions.classList.add('hidden');
            }
        });

        decorationRadio.addEventListener('change', function() {
            if (this.checked) {
                furnitureOptions.classList.add('hidden');
                decorationOptions.classList.remove('hidden');
            }
        });
    }
}

updatePriceDisplay();
show_filter();
