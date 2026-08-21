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
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="form-container login-container">
    <h2>Admin Login</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error" style="color: var(--color-status-failed); margin-bottom: var(--spacing-md);">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="login.php" method="POST" id="loginForm">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>