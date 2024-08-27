document.addEventListener('DOMContentLoaded', function() {
    const imageContainer = document.getElementById('product-images-container');
    const changeImageButton = document.getElementById('change-image-btn');

    if (!imageContainer) {
        return; // Exit early if imageContainer is null
    }

    const images = imageContainer.querySelectorAll('.product-image');

    let currentImageIndex = 0;

    // Function to change the displayed image
    function changeImage() {
        // Hide all images
        images.forEach(image => {
            image.classList.add('hidden');
        });

        // Show the next image
        images[currentImageIndex].classList.remove('hidden');

        // Increment index for next image
        currentImageIndex = (currentImageIndex + 1) % images.length;
    }

    if (changeImageButton) {
        changeImageButton.addEventListener('click', changeImage);
    }
});
