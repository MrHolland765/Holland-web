 <?php
include('connection.php');

$message = "";
$order_id = $_GET['order_id'] ?? $_POST['order_id'] ?? '';
$order_data = null;

if (!empty($order_id)) {
    $sql = "SELECT * FROM orders WHERE id = '$order_id'";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $order_data = $res->fetch_assoc();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_btn'])) {
    $order_id = $_POST['order_id'];
    $new_quantity = $_POST['quantity'];
    
    if ($order_data) {
        $price = $order_data['price'];
        $new_total = $price * $new_quantity;
        
        $update_sql = "UPDATE orders SET quantity = '$new_quantity', total = '$new_total' WHERE id = '$order_id'";
        if ($conn->query($update_sql) === TRUE) {
            $message = "<div class='alert success'>Order #$order_id imesasishwa (updated) kikamilifu!</div>";
            $res = $conn->query("SELECT * FROM orders WHERE id = '$order_id'");
            $order_data = $res->fetch_assoc();
        } else {
            $message = "<div class='alert danger'>Kosa: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order - Duka Langu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #b9eee6 0%, #ffd9a8 100%); background-attachment: fixed; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: rgba(255, 250, 240, 0.94); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { color: #f39c12; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #333; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .btn { background-color: #f39c12; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; font-weight: bold; cursor: pointer; }
        .btn:hover { background-color: #d68910; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 14px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .danger { background-color: #f8d7da; color: #721c24; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #11b0b0; text-decoration: none; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="css/update_order.css">
</head>
<body>
    <div class="card">
        <h2>✏️ Update Order</h2>
        <?php echo $message; ?>
        
        <?php if ($order_data): ?>
        <form action="update_order.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_data['id']; ?>">
            
            <div class="form-group">
                <label>Jina la Bidhaa:</label>
                <input type="text" value="<?php echo $order_data['product_name']; ?>" disabled>
            </div>
            <div class="form-group">
                <label>Bei kwa Moja (TSh):</label>
                <input type="text" value="<?php echo number_format($order_data['price']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Idadi Mpya (Quantity):</label>
                <input type="number" name="quantity" min="1" value="<?php echo $order_data['quantity']; ?>" required>
            </div>
            <button type="submit" name="update_btn" class="btn">Update Order Sasa</button>
        </form>
        <?php else: ?>
            <p style="text-align:center; color:#666;">Choose orders from this pages <a href="my_orders.php"> My orders </a> In order to update.</p>
        <?php endif; ?>
        
        <a href="my_orders.php" class="back-link">← Go back to Order</a>
    </div>
</body>
</html>
