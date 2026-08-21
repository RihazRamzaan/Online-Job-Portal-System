<?php
session_start();
require_once '../includes/validate.php';

// Receive data from application_form.php
$job_id = isset($_POST['job_id']) ? sanitize_input($_POST['job_id']) : '';
$name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
$cover_letter = isset($_POST['cover_letter']) ? sanitize_input($_POST['cover_letter']) : '';

$error = '';
// Basic check to ensure required data is present before showing payment form
if (empty($job_id) || empty($name) || empty($email) || empty($cover_letter)) {
    $error = "Missing application data. Please go back and fill out the form completely.";
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="form-container payment-container">
    <h2>Premium Application Fee</h2>
    <p style="margin-bottom: var(--spacing-md); color: var(--color-text-muted);">Please provide your mock payment details to submit your application. (Simulation only - do not enter real card details)</p>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error" style="color: var(--color-status-failed); margin-bottom: var(--spacing-md); padding: var(--spacing-md); border: 1px solid var(--color-status-failed); border-radius: var(--border-radius);">
            <?php echo htmlspecialchars($error); ?>
            <div style="margin-top: 15px;">
                <a href="../jobs/index.php" class="btn btn-outline">Return to Job Board</a>
            </div>
        </div>
    <?php else: ?>
        <form action="submit_application.php" method="POST" id="paymentForm">
            <!-- Hidden fields passed from application_form.php -->
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="cover_letter" value="<?php echo htmlspecialchars($cover_letter); ?>">
            
            <div class="form-group">
                <label for="card_name">Name on Card <span class="required">*</span></label>
                <input type="text" id="card_name" name="card_name" class="form-control" placeholder="John Doe" required>
            </div>
            
            <div class="form-group">
                <label for="card_number">Card Number <span class="required">*</span></label>
                <input type="text" id="card_number" name="card_number" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required>
            </div>
            
            <div class="form-row" style="display: flex; gap: var(--spacing-md);">
                <div class="form-group" style="flex: 1;">
                    <label for="expiry">Expiry (MM/YY) <span class="required">*</span></label>
                    <input type="text" id="expiry" name="expiry" class="form-control" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="cvv">CVV <span class="required">*</span></label>
                    <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123" maxlength="4" required>
                </div>
            </div>
            
            <div class="payment-summary" style="background-color: var(--color-bg-base); padding: var(--spacing-md); border-radius: var(--border-radius); margin-bottom: var(--spacing-md); border: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; font-weight: bold;">
                    <span>Total Amount:</span>
                    <span>$5.00</span>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Pay $5.00 & Submit Application</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>