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
<!-- TODO: HTML mock payment form logic (owner: Member C) -->
<?php if (empty($error)): ?>
    <!-- 
    Note for Member C: 
    Create a form that POSTs to submit_application.php.
    Include these hidden fields inside the form to pass the applicant's data:
    
    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
    <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
    <input type="hidden" name="cover_letter" value="<?php echo htmlspecialchars($cover_letter); ?>">
    -->
<?php else: ?>
    <div style="color: red; padding: 20px; border: 1px solid red;">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>