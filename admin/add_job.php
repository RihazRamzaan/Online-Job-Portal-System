<?php
session_start();

// Secure admin pages: check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$message = '';
$error = '';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Server-side validation: grab and sanitize inputs
    $title = trim($_POST['title'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $salary_min = intval($_POST['salary_min'] ?? 0);
    $salary_max = intval($_POST['salary_max'] ?? 0);
    $job_type = $_POST['job_type'] ?? '';
    $category_id = intval($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    
    // Auto-generate the mock company logo (first letter of the company name)
    $company_logo = !empty($company_name) ? strtoupper(substr($company_name, 0, 1)) : '';
    $posted_by = $_SESSION['admin_id'];

    // Check for required fields (fallback for client-side bypass)
    if (empty($title) || empty($company_name) || empty($location) || empty($job_type) || empty($category_id) || empty($description)) {
        $error = "All fields except salary are required.";
    } elseif ($salary_min > $salary_max && $salary_max > 0) {
        $error = "Minimum salary cannot be greater than maximum salary.";
    } else {
        // Prepared statement + bound params to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO jobs (title, company_name, company_logo, location, salary_min, salary_max, job_type, category_id, description, posted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            // "ssssiisiis" matches: 4 strings, 2 integers, 1 string, 2 integers, 1 integer (for posted_by)
            // Wait, let's verify types:
            // title (s), company_name (s), company_logo (s), location (s)
            // salary_min (i), salary_max (i)
            // job_type (s), category_id (i), description (s), posted_by (i)
            $stmt->bind_param("ssssiisiis", $title, $company_name, $company_logo, $location, $salary_min, $salary_max, $job_type, $category_id, $description, $posted_by);
            
            if ($stmt->execute()) {
                $message = "Job posted successfully!";
            } else {
                $error = "Error posting job: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

// Fetch categories to dynamically populate the dropdown
$categories = [];
$cat_result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
if ($cat_result) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>
<?php include '../includes/header.php'; ?>

<main class="admin-container">
    <h2>Post a New Job</h2>
    
    <?php if ($message): ?>
        <div class="alert success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="add_job.php" method="POST" class="admin-form">
        <div class="form-group">
            <label for="title">Job Title *</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="company_name">Company Name *</label>
            <input type="text" id="company_name" name="company_name" required>
        </div>
        
        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" required>
        </div>
        
        <div class="form-group row">
            <div class="col">
                <label for="salary_min">Minimum Salary</label>
                <input type="number" id="salary_min" name="salary_min" min="0">
            </div>
            <div class="col">
                <label for="salary_max">Maximum Salary</label>
                <input type="number" id="salary_max" name="salary_max" min="0">
            </div>
        </div>
        
        <div class="form-group">
            <label for="job_type">Job Type *</label>
            <select id="job_type" name="job_type" required>
                <option value="">Select Type</option>
                <option value="Full-Time">Full-Time</option>
                <option value="Part-Time">Part-Time</option>
                <option value="Remote">Remote</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="category_id">Category *</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>">
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Job Description *</label>
            <textarea id="description" name="description" rows="5" required></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Post Job</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</main>

<?php include '../includes/footer.php'; ?>