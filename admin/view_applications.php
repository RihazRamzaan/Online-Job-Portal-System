<?php
session_start();

// Secure admin pages: check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
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
<!-- TODO: HTML table for displaying applications (owner: Member C) -->