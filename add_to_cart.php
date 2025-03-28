<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity'])) {
    $quantities = $_POST['quantity']; // Associative array: product_id => quantity
    
    foreach ($quantities as $product_id => $qty) {
        $qty = intval($qty);
        if ($qty > 0) {
            $product_id = intval($product_id);
            // Fetch product details: name and price
            $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stmt->bind_result($product_name, $price);
            if ($stmt->fetch()) {
                $stmt->close();
                $total_price = $price * $qty;
                // Insert order with status "pending"
                $insert = $conn->prepare("INSERT INTO orders (user_id, product_id, product_name, quantity, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                $insert->bind_param("iisid", $user_id, $product_id, $product_name, $qty, $total_price);
                $insert->execute();
                $insert->close();
            } else {
                $stmt->close();
            }
        }
    }
}
header("Location: orderslist.php");
exit();
?>
