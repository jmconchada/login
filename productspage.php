<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit();
}

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching products: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products</title>
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
          box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
          border-radius: 10px;
          overflow: hidden;
      }
      th, td {
          padding: 12px;
          border-bottom: 1px solid #ddd;
          text-align: center;
      }
      th {
          background-color: #007BFF;
          color: white;
      }
      input[type="number"] {
          width: 60px;
          text-align: center;
      }
      .checkout-btn {
          padding: 10px 20px;
          background: #28a745;
          color: white;
          border: none;
          border-radius: 5px;
          cursor: pointer;
      }
      .checkout-btn:hover {
          background: #218838;
      }
      .btn {
          display: inline-block;
          padding: 10px 15px;
          margin: 5px;
          background: darkblue;
          color: white;
          text-decoration: none;
          border-radius: 5px;
      }
  </style>
</head>
<body>
  <h1>Available Products</h1>
  <!-- Form to select multiple products for checkout -->
  <form method="post" action="checkout_from_products.php">
      <table>
          <tr>
              <th>Product Name</th>
              <th>Price</th>
              <th>Order Quantity</th>
          </tr>
          <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                  <td><?php echo htmlspecialchars($row['name']); ?></td>
                  <td><?php echo number_format($row['price'], 2); ?></td>
                  <td>
                      <!-- Name as associative array, key = product id -->
                      <input type="number" name="quantity[<?php echo $row['id']; ?>]" min="0" value="0">
                  </td>
              </tr>
              <?php endwhile; ?>
          <?php else: ?>
              <tr>
                  <td colspan="3">No products available.</td>
              </tr>
          <?php endif; ?>
      </table>
      <br>
      <!-- Check Out button -->
      <button type="submit" class="checkout-btn">Check Out</button>
  </form>
  <br>
  <a href="logout.php" class="btn">Logout</a>
</body>
</html>
