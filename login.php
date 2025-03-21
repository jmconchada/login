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


if ($_SESSION['login_attempts'] >= $max_attempts) {
    $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
    
    if ($time_since_last_attempt < $lockout_time) {
        die("Too many failed attempts. Please try again later.");
    } else {
       
        $_SESSION['login_attempts'] = 0;
    }
}

if (isset($_POST['signIn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        die("Email and password are required.");
    }

    
    $sql = "SELECT id, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

       
        if (password_verify($password, $row['password'])) {
            
            session_regenerate_id(true);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT']; 

            
            $_SESSION['login_attempts'] = 0;

            header("Location: homepage.php");
            exit();
        }
    }

    
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();

    die("Incorrect email or password. Attempt " . $_SESSION['login_attempts'] . " of $max_attempts.");
}
?>
