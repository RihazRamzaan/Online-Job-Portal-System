<?php
session_start();
require_once '../config/db.php';
require_once '../includes/validate.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Gather and sanitize inputs
    $job_id = isset($_POST['job_id']) ? sanitize_input($_POST['job_id']) : '';
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $cover_letter = isset($_POST['cover_letter']) ? sanitize_input($_POST['cover_letter']) : '';
    
    // Mock Payment fields
    $card_number_raw = isset($_POST['card_number']) ? sanitize_input($_POST['card_number']) : '';
    // Strip out spaces added by JS auto-formatting so it validates as exactly 16 digits
    $card_number = str_replace(' ', '', $card_number_raw);
    $expiry = isset($_POST['expiry']) ? sanitize_input($_POST['expiry']) : '';
    $cvv = isset($_POST['cvv']) ? sanitize_input($_POST['cvv']) : '';

    // 2. Validate inputs
    if (!validate_required($job_id) || !validate_numeric($job_id)) {
        $error_message = "Invalid job selected.";
    } elseif (!validate_required($name)) {
        $error_message = "Name is required.";
    } elseif (!validate_required($email) || !validate_email($email)) {
        $error_message = "Valid email is required.";
    } elseif (!validate_required($cover_letter) || !validate_min_length($cover_letter, 10)) {
        $error_message = "Cover letter is required (min 10 characters).";
    } elseif (!validate_mock_card($card_number)) {
        $error_message = "Invalid mock card format (must be 16 digits).";
    } elseif (!validate_mock_expiry($expiry)) {
        $error_message = "Invalid mock expiry format (MM/YY).";
    } elseif (!validate_mock_cvv($cvv)) {
        $error_message = "Invalid mock CVV format (3 or 4 digits).";
    }

    // 3. Process application and mock payment
    if (empty($error_message) && isset($conn)) {
        // Generate mock transaction ref for demo purposes
        $transaction_ref = uniqid('TXN_');
        $payment_status = 'paid';
        $payment_amount = 5.00; // Premium application fee from schema

        // Prepared statement to insert application
        $stmt = $conn->prepare("INSERT INTO applications (job_id, applicant_name, applicant_email, cover_letter, payment_status, payment_amount, transaction_ref) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("issssss", $job_id, $name, $email, $cover_letter, $payment_status, $payment_amount, $transaction_ref);
            
            if ($stmt->execute()) {
                $success_message = "Application submitted successfully! Your mock transaction ID is: " . $transaction_ref;
            } else {
                $error_message = "Failed to submit application. Please try again.";
            }
            $stmt->close();
        } else {
            $error_message = "Database query failed.";
        }
    } elseif (empty($error_message)) {
        $error_message = "Database connection not found.";
    }
}
?>
<!-- TODO: HTML confirmation or error display logic (owner: Member C) -->
<?php if (!empty($success_message)): ?>
    <div style="color: green; font-weight: bold; padding: 20px; border: 1px solid green; margin: 20px;">
        <p><?php echo htmlspecialchars($success_message); ?></p>
        <a href="../index.php" style="display: inline-block; margin-top: 10px; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">Back to Home</a>
    </div>
<?php endif; ?>
<?php if (!empty($error_message)): ?>
    <div style="color: red; padding: 20px; border: 1px solid red; margin: 20px;">
        <p><?php echo htmlspecialchars($error_message); ?></p>
        <a href="javascript:history.back()" style="display: inline-block; margin-top: 10px; padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Go Back</a>
    </div>
<?php endif; ?>