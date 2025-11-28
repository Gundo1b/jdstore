document.addEventListener('DOMContentLoaded', function() {
    // Get the elements for the categories dropdown
    const categoriesBtn = document.getElementById('categories-btn');
    const categoriesDropdown = document.getElementById('categories-dropdown');

    // Check if the elements exist before adding event listeners
    if (categoriesBtn && categoriesDropdown) {
        // Toggle dropdown on button click
        categoriesBtn.addEventListener('click', function(event) {
            // Stop the click from propagating to the window listener
            event.stopPropagation();
            categoriesDropdown.classList.toggle('show');
        });

        // Close the dropdown if the user clicks outside of it
        window.addEventListener('click', function(event) {
            if (!categoriesBtn.contains(event.target)) {
                if (categoriesDropdown.classList.contains('show')) {
                    categoriesDropdown.classList.remove('show');
                }
            }
        });

        // Optional: Prevent the dropdown from closing when clicking inside it
        categoriesDropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }

    // Hamburger menu toggle
    const navToggle = document.querySelector('.nav-toggle');
    const navLinksContainer = document.querySelector('.nav-links-container');

    if (navToggle && navLinksContainer) {
        navToggle.addEventListener('click', function () {
            navLinksContainer.classList.toggle('show');
        });
    }
});
