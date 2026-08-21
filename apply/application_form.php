<?php
// TODO: application_form.php logic (owner: Member B)
// Fetch job details based on $_GET['job_id'] here
$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : '';
require_once '../includes/header.php';
?>

<div class="form-container application-container">
    <div class="job-summary" style="margin-bottom: var(--spacing-lg);">
        <h2>Apply for Job</h2>
        <!-- Member B: Output job title/company here once fetched -->
        <p>You are applying for position ID: <strong><?php echo htmlspecialchars($job_id); ?></strong></p>
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