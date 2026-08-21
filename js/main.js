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

    // --- 6. UX Touches ---
    // Note to Member C: Add action="delete_job.php" or class="delete-form" to delete forms
    
    document.querySelectorAll('form').forEach(form => {
        
        // 1. Delete Confirmation Dialogs
        const action = form.getAttribute('action') || '';
        if (action.includes('delete_job.php') || form.classList.contains('delete-form')) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this job? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        }

        // 2. Disable Submit Button on Processing (Prevent Double Submissions)
        form.addEventListener('submit', function(e) {
            // Using setTimeout to allow validation scripts to run first and potentially call e.preventDefault()
            setTimeout(() => {
                if (!e.defaultPrevented) {
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.cursor = 'not-allowed';
                        submitBtn.style.opacity = '0.7';
                        
                        if (submitBtn.tagName.toLowerCase() === 'button') {
                            submitBtn.innerText = 'Processing...';
                        } else {
                            submitBtn.value = 'Processing...';
                        }
                    }
                }
            }, 0);
        });
        
    });

});