<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Check if ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = $_GET['id'];

// Fetch the existing product details
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $description = trim($_POST['description']);

    if (!empty($name) && !empty($brand) && !empty($price) && !empty($quantity) && !empty($description)) {
        $update_sql = "UPDATE products SET name = ?, brand = ?, price = ?, quantity = ?, description = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssdiss", $name, $brand, $price, $quantity, $description, $id);

        if ($update_stmt->execute()) {
            header("Location: products.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <style>
        body {
            background-color: lightblue;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        input {
            padding: 10px;
            width: 80%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        button, .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button {
            background: darkblue;
            color: white;
        }
        .btn {
            background: red;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>Update Product</h1>
    <form method="post" action="">
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        <input type="text" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>" required>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        <input type="number" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
        <input type="text" name="description" value="<?php echo htmlspecialchars($product['description']); ?>" required>
        <button type="submit" name="update_product">Update Product</button>
    </form>

    <div class="btn-container">
        <a href="products.php" class="btn">Cancel</a>
    </div>
</body>
</html>
