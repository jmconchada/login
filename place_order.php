<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'], $_POST['quantity'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $order_quantity = intval($_POST['quantity']);

    if ($order_quantity > 0) {
        // Fetch product details including available quantity
        $query = "SELECT name, price, quantity FROM products WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($product_name, $price, $available_quantity);
        
        if ($stmt->fetch()) {
            $stmt->close();

            // Check if there's enough quantity available
            if ($available_quantity >= $order_quantity) {
                $total_price = $price * $order_quantity;
                
                // Insert the order into the orders table
                $insertQuery = "INSERT INTO orders (user_id, product_id, product_name, quantity, total_price) VALUES (?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("iisid", $user_id, $product_id, $product_name, $order_quantity, $total_price);
                
                if ($insertStmt->execute()) {
                    $insertStmt->close();
                    
                    // Update the available quantity in the products table
                    $updateQuery = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("ii", $order_quantity, $product_id);
                    
                    if ($updateStmt->execute()) {
                        $updateStmt->close();
                        header("Location: orderslist.php");
                        exit();
                    } else {
                        echo "Error updating product quantity: " . $conn->error;
                    }
                } else {
                    echo "Error placing order: " . $conn->error;
                }
            } else {
                echo "Not enough quantity available. Only $available_quantity left in stock.";
            }
        } else {
            echo "Product not found.";
        }
    } else {
        echo "Invalid quantity.";
    }
}
$conn->close();
?>
