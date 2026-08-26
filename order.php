<?php
include('connection.php');

$message = "";
$product_id = $_GET['product_id'] ?? '';

// Hakikisha table ya orders ipo
$create_table = "CREATE TABLE IF NOT EXISTS orders (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Product_name VARCHAR(100) NOT NULL,
    Price INT NOT NULL,
    Quantity INT NOT NULL,
    Total INT NOT NULL,
    Order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($create_table);

$products = [
    1 => ["name" => "Laptop Dell", "price" => 850000],
    2 => ["name" => "Smart phones", "price" => 350000],
    3 => ["name" => "Headphones", "price" => 45000]
];

$selected_product = $products[$product_id] ?? ["name" => "Laptop Dell", "price" => 850000];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $p_name = $_POST['product_name'] ?? '';
    $p_price = (int) ($_POST['price'] ?? 0);
    $qty = (int) ($_POST['quantity'] ?? 0);
    $total = $p_price * $qty;

    $safe_product_name = $conn->real_escape_string($p_name);
    $sql = "INSERT INTO orders (Product_name, Price, Quantity, Total) VALUES ('$safe_product_name', '$p_price', '$qty', '$total')";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert success'>Your Order success! Total price is TSh " . number_format($total) . "</div>";
    } else {
        $message = "<div class='alert danger'>Kosa: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamilisha Order - Duka Langu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #b9eee6 0%, #ffd9a8 100%); background-attachment: fixed; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: rgba(255, 250, 240, 0.94); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { color: #11b0b0; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #333; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .btn { background-color: #11b0b0; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn:hover { background-color: #0e8f8f; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 14px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .danger { background-color: #f8d7da; color: #721c24; }
        .back-link { display: inline-block; margin-top: 15px; color: #11b0b0; text-decoration: none; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="css/order.css">
</head>
<body>
    <div class="card">
        <h2>🛒 Complete Your Orders</h2>
        <?php echo $message; ?>
        <form action="order.php" method="POST">
            <div class="form-group">
                <label>Product name:</label>
                <input type="text" name="product_name" value="<?php echo $selected_product['name']; ?>" readonly>
            </div>
            <div class="form-group">
                <label>Per price (TSh):</label>
                <input type="number" name="price" value="<?php echo $selected_product['price']; ?>" readonly>
            </div>
            <div class="form-group">
                <label>Total Quantity:</label>
                <input type="number" name="quantity" min="1" value="1" required>
            </div>
            <button type="submit" class="btn">Confirm & Buy now</button>
        </form>
        <a href="my_orders.php" class="back-link">Shows  my orders →</a> | 
        <a href="home.php" class="back-link">Back to Home Page</a>
    </div>
</body>
</html>
