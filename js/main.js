// js/main.js (owner: Member D)

document.addEventListener('DOMContentLoaded', function () {

    // --- 5. Live Search / Filter Behavior for Job Board ---
    // Note to Member C: Please ensure the following IDs and classes are used in your HTML:
    // Filters:
    // <input type="text" id="searchInput" placeholder="Search by title...">
    // <select id="categoryFilter"> (Values must match category_id)
    // <select id="typeFilter"> (Values must match job_type enum)
    // 
    // Job Cards:
    // <div class="job-card" data-category-id="[ID]" data-job-type="[TYPE]">
    //    <h3 class="job-title">[TITLE]</h3>
    // </div>

    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const typeFilter = document.getElementById('typeFilter');
    const jobCards = document.querySelectorAll('.job-card');

    function filterJobs() {
        const searchText = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedCategory = categoryFilter ? categoryFilter.value : '';
        const selectedType = typeFilter ? typeFilter.value : '';

        jobCards.forEach(card => {
            const titleElement = card.querySelector('.job-title');
            const title = titleElement ? titleElement.textContent.toLowerCase() : '';
            const categoryId = card.getAttribute('data-category-id') || '';
            const jobType = card.getAttribute('data-job-type') || '';

            // Check if card matches each filter criterion
            let matchSearch = searchText === '' || title.includes(searchText);
            let matchCategory = selectedCategory === '' || categoryId === selectedCategory;
            let matchType = selectedType === '' || jobType === selectedType;

            if (matchSearch && matchCategory && matchType) {
                card.style.display = ''; // Show card
            } else {
                card.style.display = 'none'; // Hide card
            }
        });
    }

    // Attach event listeners for real-time filtering without page reload
    if (searchInput) {
        searchInput.addEventListener('input', filterJobs);
    }
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterJobs);
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', filterJobs);
    }

});