<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    
    $sql = "DELETE FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Check if there are no orders left in the table
        $result = $conn->query("SELECT COUNT(*) AS count FROM orders");
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['count'] == 0) {
                // Reset auto_increment to 1 if table is empty
                $conn->query("ALTER TABLE orders AUTO_INCREMENT = 1");
            }
        }
        
        header("Location: orderslist.php");
        exit();
    } else {
        echo "Error deleting order: " . $conn->error;
    }
} else {
    header("Location: orderslist.php");
    exit();
}
?>
s