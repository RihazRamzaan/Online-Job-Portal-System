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
    
    <!-- Handoff Note for the team -->
    <div style="background-color: #e6f7ff; padding: 15px; border-left: 4px solid #1890ff; margin-bottom: 20px;">
        <p><strong>Member B / Member C:</strong> The backend SQL JOIN is complete! The <code>$applications</code> array contains all the data you need (including the job title and payment status). You can build out the HTML table below this banner.</p>
    </div>

    <!-- Member B: Implement the HTML table structure here -->
    
    <!-- (Optional) Uncomment the block below if you want to inspect the array shape during development -->
    <!--
    <pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">
        <?php print_r($applications); ?>
    </pre>
    -->
    
</main>

<?php include '../includes/footer.php'; ?>