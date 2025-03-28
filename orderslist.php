<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// Debug: Uncomment to verify logged-in email
// echo "<p>Logged in as: " . $email . "</p>";

$sql = "SELECT o.id, o.product_id, o.product_name, o.quantity, o.total_price, o.status
        FROM orders o
        WHERE o.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Orders</title>
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
      }
      th, td {
          padding: 12px;
          border: 1px solid #ddd;
          text-align: center;
      }
      th {
          background-color: #007BFF;
          color: white;
      }
      .button-container {
          margin-top: 20px;
      }
      .btn {
          display: inline-block;
          padding: 10px 15px;
          margin: 5px;
          background: darkblue;
          color: white;
          text-decoration: none;
          border-radius: 5px;
          border: none;
          cursor: pointer;
      }
      .btn-green { background: green; }
      .btn-red { background: red; }
      .btn:hover { opacity: 0.8; }
  </style>
</head>
<body>
  <h1>Your Orders</h1>
  <table>
      <tr>
          <th>ID</th>
          <th>Product ID</th>
          <th>Product Name</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Status</th>
          <th>Action</th>
      </tr>
      <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo $row['product_id']; ?></td>
                  <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                  <td><?php echo $row['quantity']; ?></td>
                  <td><?php echo number_format($row['total_price'], 2); ?></td>
                  <td><?php echo $row['status']; ?></td>
                  <td>
                      <form method="post" action="delete_order.php" onsubmit="return confirm('Are you sure you want to delete this order?');">
                          <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                          <button type="submit" class="btn btn-red">Delete</button>
                      </form>
                  </td>
              </tr>
          <?php endwhile; ?>
      <?php else: ?>
          <tr>
              <td colspan="7">No orders found.</td>
          </tr>
      <?php endif; ?>
  </table>
  <div class="button-container">
      <a href="productspage.php" class="btn btn-green">Back to Products</a>
      <a href="logout.php" class="btn btn-red">Logout</a>
  </div>
</body>
</html>
