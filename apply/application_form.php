<?php
require_once '../config/db.php';

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$job_title = "Unknown Position";
$company_name = "";

if ($job_id > 0 && isset($conn)) {
    $stmt = $conn->prepare("SELECT title, company_name FROM jobs WHERE job_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $job = $result->fetch_assoc();
            $job_title = $job['title'];
            $company_name = $job['company_name'];
        }
        $stmt->close();
    }
}

require_once '../includes/header.php';
?>

<div class="form-container application-container">
    <div class="job-summary" style="margin-bottom: var(--spacing-lg);">
        <h2>Apply for Job</h2>
        <?php if (!empty($company_name)): ?>
            <p>You are applying for: <strong><?php echo htmlspecialchars($job_title); ?></strong> at <strong><?php echo htmlspecialchars($company_name); ?></strong></p>
        <?php else: ?>
            <p>You are applying for: <strong><?php echo htmlspecialchars($job_title); ?></strong></p>
        <?php endif; ?>
    </div>

    <!-- The form POSTs to payment.php for the next step -->
    <form action="payment.php" method="POST" id="applicationForm">
        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
        
        <div class="form-group">
            <label for="name">Full Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="cover_letter">Cover Letter <span class="required">*</span></label>
            <textarea id="cover_letter" name="cover_letter" class="form-control" rows="6" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Continue to Payment</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>