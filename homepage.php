<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <style>
        body {
            background-color: lightblue;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 400px;
        }
        .btn-container {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
            background: darkblue;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn {
            background: blue;
        }
    </style>
</head>
<body>
    <div class="container">
        <p style="font-size: 30px; font-weight: bold;">
            Hello, <?php echo $_SESSION['email']; ?>! 😊
        </p>
        <div class="btn-container">
            <a href="users.php" class="btn">View Users</a>
            <a href="products.php" class="btn">Products</a>
            <a href="logout.php" class="btn logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>
