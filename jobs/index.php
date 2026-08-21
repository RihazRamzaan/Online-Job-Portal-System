<?php
require_once '../config/db.php';
require_once '../includes/validate.php';

// Initialize arrays to pass data to the view
$jobs = [];
$categories = [];

if (isset($conn)) {
    // 1. Fetch categories for the filter dropdown
    $cat_query = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
    $cat_result = $conn->query($cat_query);
    if ($cat_result) {
        while ($row = $cat_result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    // 2. Handle search and filters
    $search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
    $category_id = isset($_GET['category_id']) ? sanitize_input($_GET['category_id']) : '';
    $job_type = isset($_GET['job_type']) ? sanitize_input($_GET['job_type']) : '';

    // Base query to fetch active jobs only
    $query = "SELECT j.job_id, j.title, j.company_name, j.company_logo, j.location, 
                     j.salary_min, j.salary_max, j.job_type, j.created_at, c.category_name 
              FROM jobs j 
              LEFT JOIN categories c ON j.category_id = c.category_id 
              WHERE j.status = 'active'";
    
    $params = [];
    $types = "";

    // Append conditions based on filters
    if (!empty($search)) {
        $query .= " AND j.title LIKE ?";
        $params[] = "%" . $search . "%";
        $types .= "s";
    }

    if (!empty($category_id) && validate_numeric($category_id)) {
        $query .= " AND j.category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }

    if (!empty($job_type)) {
        $valid_types = ['Full-Time', 'Part-Time', 'Remote'];
        if (in_array($job_type, $valid_types)) {
            $query .= " AND j.job_type = ?";
            $params[] = $job_type;
            $types .= "s";
        }
    }

    $query .= " ORDER BY j.created_at DESC";

    // Prepare and execute the statement
    $stmt = $conn->prepare($query);
    if ($stmt) {
        if (!empty($params)) {
            // Bind parameters dynamically
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
        $stmt->close();
    }
}
?>
<!-- TODO: HTML Logic for jobs board, filters and cards (owner: Member C) -->