<?php
session_start();
include 'connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'];

// Uncomment the next line for debugging the current user ID and email
// echo "Current logged-in user ID: " . $user_id . " Email: " . $user_email . "<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity'])) {
    $quantities = $_POST['quantity']; // Associative array: product_id => quantity

    // Check if any product is selected (quantity > 0)
    $hasSelection = false;
    foreach ($quantities as $q) {
        if (intval($q) > 0) {
            $hasSelection = true;
            break;
        }
    }
    if (!$hasSelection) {
        echo "No products selected.";
        exit();
    }

    $conn->begin_transaction();

    try {
        foreach ($quantities as $product_id => $qty) {
            $qty = intval($qty);
            if ($qty > 0) {
                $product_id = intval($product_id);

                // Fetch product details: name, price, available quantity
                $stmt = $conn->prepare("SELECT name, price, quantity FROM products WHERE id = ?");
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $stmt->bind_result($product_name, $price, $available_quantity);
                if ($stmt->fetch()) {
                    $stmt->close();
                    if ($available_quantity < $qty) {
                        throw new Exception("Not enough stock for product: " . $product_name);
                    }
                    // Update product quantity
                    $update_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                    if (!$update_stmt) {
                        throw new Exception("Prepare failed (update): " . $conn->error);
                    }
                    $update_stmt->bind_param("ii", $qty, $product_id);
                    if (!$update_stmt->execute()) {
                        throw new Exception("Failed to update stock for: " . $product_name);
                    }
                    $update_stmt->close();

                    // Calculate total price for this order
                    $total_price = $price * $qty;

                    // Insert order with status 'checked_out' and store the user's email
                    $insert_stmt = $conn->prepare("INSERT INTO orders (user_id, email, product_id, product_name, quantity, total_price, status) VALUES (?, ?, ?, ?, ?, ?, 'checked_out')");
                    if (!$insert_stmt) {
                        throw new Exception("Prepare failed (insert): " . $conn->error);
                    }
                    // Bind: i = user_id, s = email, i = product_id, s = product_name, i = quantity, d = total_price
                    $insert_stmt->bind_param("isisid", $user_id, $user_email, $product_id, $product_name, $qty, $total_price);
                    if (!$insert_stmt->execute()) {
                        throw new Exception("Failed to create order for: " . $product_name);
                    }
                    $insert_stmt->close();
                } else {
                    $stmt->close();
                    throw new Exception("Product not found for ID: " . $product_id);
                }
            }
        }
        $conn->commit();
        header("Location: orderslist.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Checkout failed: " . $e->getMessage();
    }
} else {
    echo "No products selected.";
}
$conn->close();
?>
