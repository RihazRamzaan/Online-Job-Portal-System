<?php
session_start();

// Secure admin pages: check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$admin_id = $_SESSION['admin_id'];

// Optional filter by job_id as per the endpoint contract
$filter_job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

// ==================================================================================
// MEMBER A: The JOIN query
// We join applications with jobs so we can display the human-readable job_title.
// We also strictly filter by posted_by to ensure the admin only sees apps for their own jobs.
// ==================================================================================
$query = "
    SELECT 
        a.application_id, 
        a.applicant_name, 
        a.applicant_email, 
        a.cover_letter, 
        a.payment_status, 
        a.payment_amount, 
        a.transaction_ref, 
        a.applied_at,
        j.title AS job_title
    FROM applications a
    INNER JOIN jobs j ON a.job_id = j.job_id
    WHERE j.posted_by = ?
";

// Append filter if a specific job was clicked from the dashboard
if ($filter_job_id > 0) {
    $query .= " AND j.job_id = ?";
}

$query .= " ORDER BY a.applied_at DESC";

$applications = [];
if ($stmt = $conn->prepare($query)) {
    // Bind dynamically based on whether the filter is active
    if ($filter_job_id > 0) {
        $stmt->bind_param("ii", $admin_id, $filter_job_id);
    } else {
        $stmt->bind_param("i", $admin_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
    $stmt->close();
} else {
    die("Database error: " . $conn->error);
}

// ==================================================================================
// MEMBER B: Hand-off!
// The $applications array is fully populated securely. 
// You can now iterate over it to build the HTML table with Member C.
// ==================================================================================
?>
<?php include '../includes/header.php'; ?>

<main class="admin-container">
    <h2>View Applications</h2>
    

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Applied For</th>
                    <th>Date Applied</th>
                    <th>Payment Status</th>
                    <th>Amount</th>
                    <th>Ref ID</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No applications found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                            <td><a href="mailto:<?php echo htmlspecialchars($app['applicant_email']); ?>"><?php echo htmlspecialchars($app['applicant_email']); ?></a></td>
                            <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                            <td>
                                <?php $status_class = strtolower($app['payment_status']); ?>
                                <span class="badge status-<?php echo htmlspecialchars($status_class); ?>">
                                    <?php echo ucfirst(htmlspecialchars($app['payment_status'])); ?>
                                </span>
                            </td>
                            <td>$<?php echo number_format($app['payment_amount'], 2); ?></td>
                            <td><span style="font-family: monospace; font-size: 0.85em;"><?php echo htmlspecialchars($app['transaction_ref'] ?? 'N/A'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- (Optional) Uncomment the block below if you want to inspect the array shape during development -->
    <!--
    <pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">
        <?php print_r($applications); ?>
    </pre>
    -->
    
</main>

<?php include '../includes/footer.php'; ?>