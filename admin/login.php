<?php
// Start session for login state management
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Include database configuration (assuming $conn is a mysqli connection provided by Member A)
require_once '../config/db.php';

$error = '';

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server-side validation
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Prepared statement to prevent SQL injection
        if (isset($conn)) {
            $stmt = $conn->prepare("SELECT admin_id, password FROM admins WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $admin = $result->fetch_assoc();
                    
                    // Verify the hashed password
                    if (password_verify($password, $admin['password'])) {
                        // Password is correct, set session variables
                        $_SESSION['admin_id'] = $admin['admin_id'];
                        
                        // Redirect to dashboard
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $error = 'Invalid email or password.';
                    }
                } else {
                    $error = 'Invalid email or password.';
                }
                $stmt->close();
            } else {
                $error = 'Database query failed.';
            }
        } else {
            $error = 'Database connection not found.';
        }
    }
}
?>
<!-- TODO: HTML Form logic (owner: Member C) -->
<!-- Example placeholder for error display: -->
<?php if (!empty($error)): ?>
    <div style="color: red;"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>