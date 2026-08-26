<?php
include('connection.php');

// SQL ya kutengeneza table ya orders kiotomatiki ikiwa haipo
$create_table = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    quantity INT NOT NULL,
    total INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($create_table);

$sql = "SELECT * FROM orders ORDER BY id asc";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Zangu - Duka Langu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #b9eee6 0%, #ffd9a8 100%); background-attachment: fixed; padding: 30px; }
        .container { max-width: 900px; margin: 0 auto; background: rgba(255, 250, 240, 0.94); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #11b0b0; margin-bottom: 20px; border-bottom: 2px solid #11b0b0; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #11b0b0; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .btn-edit { background-color: #f39c12; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        .back-link { display: inline-block; margin-top: 20px; color: #11b0b0; text-decoration: none; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="css/my_orders.css">
</head>
<body>
    <div class="container">
        <h2>📦 My orders Lists</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Products</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Totals</th>
                    <th>Date</th>
                    <th>Changes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>#" . $row['id'] . "</td>";
                        echo "<td>" . $row['product_name'] . "</td>";
                        echo "<td>" . number_format($row['price']) . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . number_format($row['total']) . "</td>";
                        echo "<td>" . $row['order_date'] . "</td>";
                        echo "<td><a href='update_order.php?order_id=" . $row['id'] . "' class='btn-edit'>Update</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>Bado hujafanya order yoyote.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="home.php" class="back-link">← Back to Home Page</a>
    </div>
</body>
</html>
