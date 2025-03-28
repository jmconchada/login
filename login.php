<?php 
include 'connect.php';
session_start();

$max_attempts = 5;
$lockout_time = 300;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = 0;
}

// Check if the user is locked out
if ($_SESSION['login_attempts'] >= $max_attempts) {
    $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
    
    if ($time_since_last_attempt < $lockout_time) {
        echo "Too many failed attempts. Please try again later.";
        exit();
    } else {
        $_SESSION['login_attempts'] = 0;
    }
}

// Check if form is submitted
if (isset($_POST['signIn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "Email and password are required.";
        exit();
    }

    // Fetch user info including role
    $sql = "SELECT id, email, password, TRIM(role) as role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = strtolower(trim($row['role'])); // Ensure role is lowercase & trimmed
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

            $_SESSION['login_attempts'] = 0;

            // ✅ Correct Redirect Logic
            if ($_SESSION['role'] === 'admin') { 
                header("Location: homepage.php"); // Admin goes to homepage.php
                exit();
            } elseif ($_SESSION['role'] === 'user') {
                header("Location: productspage.php"); // User goes to productspage.php
                exit();
            } else {
                echo "Invalid role detected.";
                exit();
            }
        }
    }

    // Failed login attempt
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();

    echo "Incorrect email or password. Attempt " . $_SESSION['login_attempts'] . " of $max_attempts.";
    exit();
}

// Close database connection
$stmt->close();
$conn->close();
?>
