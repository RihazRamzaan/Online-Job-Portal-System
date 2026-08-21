<?php
session_start();

// Secure admin pages: check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

// Only allow POST requests for deletion!
// This prevents CSRF attacks where an attacker tricks the admin into clicking a GET link like delete_job.php?job_id=5
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method. Deletion must happen via a POST form.");
}

$job_id = intval($_POST['job_id'] ?? 0);
$admin_id = $_SESSION['admin_id'];

if ($job_id > 0) {
    // Note on Foreign Keys:
    // The 'applications' table references the 'jobs' table. 
    // Because we didn't specify ON DELETE CASCADE in the schema, we must delete child applications first,
    // otherwise the database will block the job deletion to enforce referential integrity.
    
    // 1. Delete associated applications (only if the job is owned by this admin)
    // Using a subquery ensures the admin can't delete applications for jobs they don't own.
    $delete_apps_query = "DELETE FROM applications WHERE job_id = (SELECT job_id FROM jobs WHERE job_id = ? AND posted_by = ?)";
    if ($stmt_apps = $conn->prepare($delete_apps_query)) {
        $stmt_apps->bind_param("ii", $job_id, $admin_id);
        $stmt_apps->execute();
        $stmt_apps->close();
    }
    
    // 2. Delete the actual job posting
    $delete_job_query = "DELETE FROM jobs WHERE job_id = ? AND posted_by = ?";
    if ($stmt_job = $conn->prepare($delete_job_query)) {
        $stmt_job->bind_param("ii", $job_id, $admin_id);
        $stmt_job->execute();
        $stmt_job->close();
    }
}

// Redirect back to the dashboard after processing
header("Location: dashboard.php");
exit;
?>