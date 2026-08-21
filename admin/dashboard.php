<?php
session_start();

// Secure admin pages: check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

// Fetch jobs for the currently logged-in admin
$admin_id = $_SESSION['admin_id'];

// Using prepared statements for the SELECT query to securely bind the admin_id
// We use a LEFT JOIN to grab the human-readable category_name instead of just category_id
$query = "
    SELECT j.job_id, j.title, j.company_name, j.location, j.job_type, j.status, j.created_at, c.category_name 
    FROM jobs j
    LEFT JOIN categories c ON j.category_id = c.category_id
    WHERE j.posted_by = ?
    ORDER BY j.created_at DESC
";

$jobs = [];
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    
    // Get the result set from the executed statement
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }
    $stmt->close();
} else {
    // Basic error handling for the query failure
    die("Database error: " . $conn->error);
}
?>
<?php include '../includes/header.php'; ?>

<main class="admin-container">
    <div class="dashboard-header">
        <h2>Admin Dashboard - My Jobs</h2>
        <div class="dashboard-actions">
            <a href="add_job.php" class="btn btn-primary">Post New Job</a>
            <a href="view_applications.php" class="btn btn-secondary">View Applications</a>
        </div>
    </div>

    <!-- Job list rendered as a table -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date Posted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jobs)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No jobs posted yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($job['category_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($job['location']); ?></td>
                            <td><?php echo htmlspecialchars($job['job_type']); ?></td>
                            <td>
                                <!-- Status badge hook for Member C's CSS -->
                                <span class="badge status-<?php echo strtolower($job['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($job['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                            <td class="actions">
                                <a href="edit_job.php?job_id=<?php echo $job['job_id']; ?>" class="btn btn-small">Edit</a>
                                
                                <!-- Delete action: using POST via a mini form rather than a GET link to prevent accidental deletions or CSRF -->
                                <form action="delete_job.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this job?');">
                                    <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>