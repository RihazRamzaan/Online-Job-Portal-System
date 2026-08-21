<?php
require_once '../config/db.php';
require_once '../includes/validate.php';

// Initialize arrays to pass data to the view
$jobs = [];
$categories = [];

if (isset($conn)) {
    // 1. Fetch categories for the filter dropdown
    $cat_query = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
    $cat_result = $conn->query($cat_query);
    if ($cat_result) {
        while ($row = $cat_result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    // 2. Handle search and filters
    $search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
    $category_id = isset($_GET['category_id']) ? sanitize_input($_GET['category_id']) : '';
    $job_type = isset($_GET['job_type']) ? sanitize_input($_GET['job_type']) : '';

    // Base query to fetch active jobs only
    $query = "SELECT j.job_id, j.title, j.company_name, j.company_logo, j.location, 
                     j.salary_min, j.salary_max, j.job_type, j.created_at, c.category_name 
              FROM jobs j 
              LEFT JOIN categories c ON j.category_id = c.category_id 
              WHERE j.status = 'active'";
    
    $params = [];
    $types = "";

    // Append conditions based on filters
    if (!empty($search)) {
        $query .= " AND j.title LIKE ?";
        $params[] = "%" . $search . "%";
        $types .= "s";
    }

    if (!empty($category_id) && validate_numeric($category_id)) {
        $query .= " AND j.category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }

    if (!empty($job_type)) {
        $valid_types = ['Full-Time', 'Part-Time', 'Remote'];
        if (in_array($job_type, $valid_types)) {
            $query .= " AND j.job_type = ?";
            $params[] = $job_type;
            $types .= "s";
        }
    }

    $query .= " ORDER BY j.created_at DESC";

    // Prepare and execute the statement
    $stmt = $conn->prepare($query);
    if ($stmt) {
        if (!empty($params)) {
            // Bind parameters dynamically
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
        $stmt->close();
    }
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="page-header">
    <h1>Browse Jobs</h1>
    <p>Find your next career opportunity</p>
</div>

<!-- Search and Filter Bar -->
<section class="filter-section">
    <form action="index.php" method="GET" class="filter-form" id="jobFilterForm">
        <div class="filter-group">
            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search job title..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <div class="filter-group">
            <select name="category_id" id="categorySelect" class="form-control">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category_id == $cat['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <select name="job_type" id="jobTypeSelect" class="form-control">
                <option value="">All Job Types</option>
                <option value="Full-Time" <?php echo ($job_type === 'Full-Time') ? 'selected' : ''; ?>>Full-Time</option>
                <option value="Part-Time" <?php echo ($job_type === 'Part-Time') ? 'selected' : ''; ?>>Part-Time</option>
                <option value="Remote" <?php echo ($job_type === 'Remote') ? 'selected' : ''; ?>>Remote</option>
            </select>
        </div>
        
        <div class="filter-group filter-actions">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="index.php" class="btn btn-outline">Clear</a>
        </div>
    </form>
</section>

<!-- Job Listing Grid -->
<section class="job-grid" id="jobListings">
    <?php if (empty($jobs)): ?>
        <div class="empty-state">
            <p>No jobs found matching your criteria.</p>
        </div>
    <?php else: ?>
        <?php foreach ($jobs as $job): 
            // Generate mock logo: first letter of company name
            $mock_logo = !empty($job['company_logo']) ? $job['company_logo'] : substr($job['company_name'], 0, 1);
            $logo_color_class = 'logo-color-' . ((ord(strtoupper($mock_logo)) % 5) + 1);
        ?>
            <div class="job-card">
                <div class="job-card-header">
                    <div class="logo-avatar <?php echo $logo_color_class; ?>">
                        <?php echo htmlspecialchars(strtoupper($mock_logo)); ?>
                    </div>
                    <div class="job-company-info">
                        <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></p>
                    </div>
                </div>
                <div class="job-card-body">
                    <ul class="job-details">
                        <li>📍 <?php echo htmlspecialchars($job['location']); ?></li>
                        <li>💼 <?php echo htmlspecialchars($job['category_name'] ?? 'Uncategorized'); ?></li>
                        <?php if (!empty($job['salary_min']) && !empty($job['salary_max'])): ?>
                            <li>💰 $<?php echo number_format($job['salary_min']); ?> - $<?php echo number_format($job['salary_max']); ?></li>
                        <?php endif; ?>
                    </ul>
                    <div class="job-badges">
                        <span class="badge badge-<?php echo strtolower(str_replace('-', '', $job['job_type'])); ?>">
                            <?php echo htmlspecialchars($job['job_type']); ?>
                        </span>
                    </div>
                </div>
                <div class="job-card-footer">
                    <a href="../apply/application_form.php?job_id=<?php echo $job['job_id']; ?>" class="btn btn-primary btn-block">Apply Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php require_once '../includes/footer.php'; ?>