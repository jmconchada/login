<?php
session_start();
include 'connect.php';


if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}


if (!isset($_GET['email'])) {
    header("Location: users.php");
    exit();
}

$old_email = $_GET['email'];

$sql = "SELECT firstName, lastName, email FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $old_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $new_email = trim($_POST['email']);

    if (!empty($firstName) && !empty($lastName) && !empty($new_email)) {
        
        $check_email_sql = "SELECT email FROM users WHERE email = ? AND email != ?";
        $check_stmt = $conn->prepare($check_email_sql);
        $check_stmt->bind_param("ss", $new_email, $old_email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "Error: Email is already in use.";
        } else {
            
            $update_sql = "UPDATE users SET firstName = ?, lastName = ?, email = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssss", $firstName, $lastName, $new_email, $old_email);

            if ($update_stmt->execute()) {
                header("Location: users.php");
                exit();
            } else {
                echo "Error updating user.";
            }
        }
    } else {
        echo "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            text-align: center;
        }
        form {
            background: white;
            padding: 20px;
            width: 50%;
            margin: 50px auto;
            border-radius: 5px;
        }
        input {
            display: block;
            width: 80%;
            margin: 10px auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 15px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Update User</h1>
    <form method="post">
        <input type="text" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
        <input type="text" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <button type="submit" class="btn">Update</button>
        <a href="users.php" class="btn">Cancel</a>
    </form>
</body>
</html>
