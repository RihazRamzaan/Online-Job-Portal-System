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
$job = null;
$admin_id = $_SESSION['admin_id'];

// Check if we have a job_id (either from GET to load the form, or POST to save it)
$job_id = intval($_GET['job_id'] ?? $_POST['job_id'] ?? 0);

if (!$job_id) {
    die("Invalid Job ID provided.");
}

// ---------------------------------------------------------
// 1. Process Update (if this is a POST request)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = trim($_POST['title'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $salary_min = intval($_POST['salary_min'] ?? 0);
    $salary_max = intval($_POST['salary_max'] ?? 0);
    $job_type = $_POST['job_type'] ?? '';
    $category_id = intval($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    // Auto-generate the mock company logo again just in case company name changed
    $company_logo = !empty($company_name) ? strtoupper(substr($company_name, 0, 1)) : '';

    // Validate
    if (empty($title) || empty($company_name) || empty($location) || empty($job_type) || empty($category_id) || empty($description)) {
        $error = "All core fields are required.";
    } elseif ($salary_min > $salary_max && $salary_max > 0) {
        $error = "Minimum salary cannot be greater than maximum salary.";
    } else {
        // Prepared statement UPDATE
        // Ensure we check posted_by = ? to prevent admins from editing other admins' jobs (security best practice)
        $update_query = "
            UPDATE jobs 
            SET title=?, company_name=?, company_logo=?, location=?, salary_min=?, salary_max=?, job_type=?, category_id=?, description=?, status=?
            WHERE job_id=? AND posted_by=?
        ";
        
        if ($stmt = $conn->prepare($update_query)) {
            // "ssssiisissii" -> 4 strings, 2 ints, 1 string, 1 int, 2 strings, 2 ints
            $stmt->bind_param("ssssiisissii", $title, $company_name, $company_logo, $location, $salary_min, $salary_max, $job_type, $category_id, $description, $status, $job_id, $admin_id);
            
            if ($stmt->execute()) {
                $message = "Job updated successfully!";
            } else {
                $error = "Error updating job: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

// ---------------------------------------------------------
// 2. Fetch Job Details (to populate the form fields)
// ---------------------------------------------------------
// Prepared statement for SELECT to prevent SQL injection on job_id
if ($stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = ? AND posted_by = ?")) {
    $stmt->bind_param("ii", $job_id, $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $job = $result->fetch_assoc();
    } else {
        die("Job not found or you do not have permission to edit it.");
    }
    $stmt->close();
} else {
    die("Database error: " . $conn->error);
}

// Fetch categories for the dropdown
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
    <h2>Edit Job</h2>
    
    <?php if ($message): ?>
        <div class="alert success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="edit_job.php?job_id=<?php echo $job_id; ?>" method="POST" class="admin-form">
        <!-- Hidden job_id field for POST submittal -->
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
        
        <div class="form-group">
            <label for="title">Job Title *</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($job['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="company_name">Company Name *</label>
            <input type="text" id="company_name" name="company_name" class="form-control" value="<?php echo htmlspecialchars($job['company_name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($job['location']); ?>" required>
        </div>
        
        <div class="form-group row">
            <div class="col">
                <label for="salary_min">Minimum Salary</label>
                <input type="number" id="salary_min" name="salary_min" class="form-control" min="0" value="<?php echo htmlspecialchars($job['salary_min']); ?>">
            </div>
            <div class="col">
                <label for="salary_max">Maximum Salary</label>
                <input type="number" id="salary_max" name="salary_max" class="form-control" min="0" value="<?php echo htmlspecialchars($job['salary_max']); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="job_type">Job Type *</label>
            <select id="job_type" name="job_type" class="form-control" required>
                <option value="Full-Time" <?php echo ($job['job_type'] === 'Full-Time') ? 'selected' : ''; ?>>Full-Time</option>
                <option value="Part-Time" <?php echo ($job['job_type'] === 'Part-Time') ? 'selected' : ''; ?>>Part-Time</option>
                <option value="Remote" <?php echo ($job['job_type'] === 'Remote') ? 'selected' : ''; ?>>Remote</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php echo ($job['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Status Dropdown: Added for Update step -->
        <div class="form-group">
            <label for="status">Job Status *</label>
            <select id="status" name="status" class="form-control" required>
                <option value="active" <?php echo ($job['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="closed" <?php echo ($job['status'] === 'closed') ? 'selected' : ''; ?>>Closed</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Job Description *</label>
            <textarea id="description" name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($job['description']); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Job</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</main>

<?php include '../includes/footer.php'; ?>