<?php
session_start();
include 'connect.php';

// Redirect users to product page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: productpage.php");
    exit();
}


// Delete User Logic
if (isset($_GET['delete_email'])) {
    $email = $_GET['delete_email'];

    if ($email === $_SESSION['email']) {
        echo "<script>alert('You cannot delete your own account.'); window.location='users.php';</script>";
        exit();
    }

    $delete_sql = "DELETE FROM users WHERE email = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $email);

    if ($delete_stmt->execute()) {
        echo "<script>alert('User deleted successfully.'); window.location='users.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting user. Please try again.'); window.location='users.php';</script>";
    }
    $delete_stmt->close();
}

// Fetch all users
$sql = "SELECT firstName, lastName, email FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        text-align: center;
    }
    table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        background: white;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }
    th, td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #007BFF;
        color: white;
    }
    .btn {
        display: inline-block;
        padding: 10px 15px;
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
        text-align: center;
        border-radius: 5px;
        transition: 0.3s;
    }
    .update-btn {
        background: #28a745;
        color: white;
        border: 2px solid #28a745;
    }
    .update-btn:hover {
        background: white;
        color: #28a745;
        border: 2px solid #28a745;
    }
    .delete-btn {
        background: #dc3545;
        color: white;
        border: 2px solid #dc3545;
    }
    .delete-btn:hover {
        background: white;
        color: #dc3545;
        border: 2px solid #dc3545;
    }
    .btn-container {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    .nav-container {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .nav-btn {
        background: #007BFF;
        color: white;
        border: 2px solid #007BFF;
        padding: 12px 20px;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        text-align: center;
        border-radius: 5px;
        transition: 0.3s;
    }
    .nav-btn:hover {
        background: white;
        color: #007BFF;
    }
    .logout-btn {
        background: #dc3545;
        color: white;
        border: 2px solid #dc3545;
    }
    .logout-btn:hover {
        background: white;
        color: #dc3545;
    }
</style>

</head>
<body>
    <h1>Registered Users</h1>
    <table>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result !== false && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['firstName']) . "</td>
                        <td>" . htmlspecialchars($row['lastName']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td class='btn-container'>";
                if ($row['email'] !== $_SESSION['email']) {
                    echo "<a href='update_user.php?email=" . urlencode($row['email']) . "' class='btn update-btn'>Update</a>
                          <a href='users.php?delete_email=" . urlencode($row['email']) . "' class='btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>";
                } else {
                    echo "<span>Cannot delete self</span>";
                }
                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No users found.</td></tr>";
        }
        ?>
    </table>
    
    <div class="nav-container">
        <a href="homepage.php" class="nav-btn">Back to Homepage</a>
        <a href="logout.php" class="nav-btn logout-btn">Logout</a>
    </div>

    <?php
    // Close database connection
    $conn->close();
    ?>
</body>
</html>
