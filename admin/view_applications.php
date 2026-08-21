<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';
require_once '../includes/validate.php';

$applications = [];

if (isset($conn)) {
    // Member A's JOIN query (wired by Member B)
    $query = "SELECT a.application_id, a.applicant_name, a.applicant_email, a.payment_status, 
                     a.payment_amount, a.transaction_ref, a.applied_at, j.title AS job_title 
              FROM applications a
              JOIN jobs j ON a.job_id = j.job_id";
    
    // Optional filter by job_id
    $filter_job_id = isset($_GET['job_id']) ? sanitize_input($_GET['job_id']) : '';
    $params = [];
    
    if (!empty($filter_job_id) && validate_numeric($filter_job_id)) {
        $query .= " WHERE a.job_id = ?";
        $params[] = $filter_job_id;
    }
    
    $query .= " ORDER BY a.applied_at DESC";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param("i", $params[0]);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $applications[] = $row;
        }
        $stmt->close();
    }
}
?>
<?php require_once '../includes/header.php'; ?>

<main class="admin-container">
    <div class="dashboard-header">
        <h2>View Applications</h2>
        <div class="dashboard-actions">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

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
</main>

<?php require_once '../includes/footer.php'; ?>