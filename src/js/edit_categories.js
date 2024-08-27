document.addEventListener("DOMContentLoaded", function() {
    let editOperation = document.getElementById("edit-operation");

    if(!editOperation){
        return; // Exit early if editOperation is null
    }

    // Only attach event listener if edit-operation is visible
    if (!editOperation.classList.contains("hidden")) {
        editOperation.addEventListener("change", function() {
            let operation = this.value;
            let deleteForm = document.getElementById("delete-category-form");
            let createForm = document.getElementById("create-category-form");

            // Show/hide forms based on selected operation
            if (operation === "delete") {
                deleteForm.classList.remove("hidden");
                createForm.classList.add("hidden");
            } else if (operation === "create") {
                deleteForm.classList.add("hidden");
                createForm.classList.remove("hidden");
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    let categoryRadioButtons = document.querySelectorAll("#delete-category-form input[type='radio']");
    let deleteButton = document.getElementById("delete-button");

    // Function to check if any of the divs is visible
    function isAnyDivVisible() {
        let divs = document.querySelectorAll("#delete-category-form div");
        for (let div of divs) {
            if (!div.classList.contains("hidden")) {
                return true;
            }
        }
        return false;
    }

    // Add event listeners to each radio button
    categoryRadioButtons.forEach(function(radioButton) {
        radioButton.addEventListener("change", function() {
            let selectedCategory = document.querySelector("input[name='category']:checked").value;
            let selectedSection = document.getElementById("admin-" + selectedCategory);

            // Hide all category management sections first
            document.querySelectorAll("#delete-category-form div").forEach(function(section) {
                section.classList.add("hidden");
            });

            // Show the selected category management section
            selectedSection.classList.remove("hidden");

            if (isAnyDivVisible()) {
                deleteButton.style.display = "block";
            } else {
                deleteButton.style.display = "none";
            }

        });
    });
});
