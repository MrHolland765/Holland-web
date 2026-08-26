<?php
session_start();
include('connection.php');

$home_user = null;
$home_avatar = '';
if (!empty($_SESSION['user_email'])) {
    $home_email = $_SESSION['user_email'];
    $statement = $conn->prepare('SELECT Fullname, Username, Address, Phone, Email FROM users WHERE Email = ?');
    $statement->bind_param('s', $home_email);
    $statement->execute();
    $home_user = $statement->get_result()->fetch_assoc();
    $statement->close();

    $home_profile_key = hash('sha256', strtolower($home_email));
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
        $avatar_file = __DIR__ . '/image/profiles/' . $home_profile_key . '.' . $extension;
        if (is_file($avatar_file)) {
            $home_avatar = 'image/profiles/' . $home_profile_key . '.' . $extension . '?v=' . filemtime($avatar_file);
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOP Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #b9eee6 0%, #ffd9a8 100%);
            background-attachment: fixed;
        }

        /* Layout kuu ya ukurasa */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* 1. UPANDE WA KUSHOTO (Sidebar) */
        .sidebar {
            width: 260px;
            background-color: #11b0b0; /* Rangi uliyotumia kwenye Login/Signup */
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            transition: width 0.3s ease, padding 0.3s ease;
        }

        .dashboard-container.sidebar-closed .sidebar {
            width: 0;
            padding-left: 0;
            padding-right: 0;
        }

        .dashboard-container.sidebar-closed .sidebar > div {
            visibility: hidden;
            opacity: 0;
        }

        .sidebar > div { transition: opacity 0.2s ease; }

        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 2;
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 5px;
            background-color: #11b0b0;
            color: white;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.18);
        }

        .sidebar-toggle:hover { background-color: #0e8f8f; }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 22px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #0e8f8f;
        }

        .sidebar .profile-link { background-color: #2878c8; }
        .sidebar .profile-link:hover { background-color: #1f5f9f; }

        .sidebar .password-link { background-color: #e67e22; }
        .sidebar .password-link:hover { background-color: #ba641b; }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: block;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        /* 2. SEHEMU YA KATIKATI (Products Area) */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        .main-content h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            max-width: 560px;
        }

        .search-form input {
            flex: 1;
            min-width: 0;
            padding: 12px 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        .search-form button {
            border: 0;
            border-radius: 5px;
            padding: 0 18px;
            background-color: #11b0b0;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .search-form button:hover { background-color: #0e8f8f; }

        .no-results {
            display: none;
            color: #666;
            margin-top: 20px;
        }

        /* Grid ya Bidhaa */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: rgba(255, 250, 240, 0.94);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            border-radius: 8px;
        }

        .product-card h3 {
            margin: 10px 0 5px 0;
            font-size: 18px;
            color: #333;
        }

        .product-card .price {
            color: #11b0b0;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .buy-btn {
            background-color: #11b0b0;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            transition: 0.3s;
        }

        .buy-btn:hover {
            background-color: #0e8f8f;
        }
    </style>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>

    <div class="dashboard-container">

        <button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-label="Close menu" aria-expanded="true">×</button>

        <!-- UPANDE WA KUSHOTO: Navigation Menu -->
        <div class="sidebar">
            <div>
                <h2>Menu</h2>
                <ul>
                    <li><a href="profile.php" class="profile-link">👤 Personal Information / Edit Profile</a></li>
                    <li><a href="change_password.php" class="password-link">🔒 Change Password</a></li>
                    <li><a href="my_orders.php">📦 my orders</a></li>
                    <li><a href="update_order.php">✏️ Update Order</a></li>
                </ul>
        
            
            <!-- Sehemu ya Sign Out -->
            <a href="logout.php" class="logout-btn">🚪 Sign Out</a>
            </div>
        </div>

        <!-- KATIKATI: Sehemu ya Bidhaa -->
        <div class="main-content">
            <h1>HOLLAND WORK</h1>

            <form class="search-form" id="product-search">
                <input type="search" id="product-search-input" placeholder="Search product..." aria-label="Search product">
                <button type="submit" aria-label="Search products">🔍 Search</button>
            </form>

            <div class="product-grid">

                <!-- Bidhaa ya 1 -->
                <div class="product-card">
                    <img src="image/image 3.jpg" alt="Bidhaa 1" >
                    <h3>Laptop Dell</h3>
                    <div class="price">TSh 850,000</div>
                    <!-- Kitufe kitatuma ID ya bidhaa kwenda order.php -->
                    <a href="order.php?product_id=1" class="buy-btn">Buy now</a>
                </div>

                <!-- Bidhaa ya 2 -->
                <div class="product-card">
                    <img src="image/image 2.jpg" alt="Bidhaa 2">
                    <h3>Smart phone</h3>
                    <div class="price">TSh 350,000</div>
                    <a href="order.php?product_id=2" class="buy-btn">Buy now</a>
                </div>

                <!-- Bidhaa ya 3 -->
                <div class="product-card">
                    <img src="image/image 1.jpg" alt="Bidhaa 3">
                    <h3>Headphones</h3>
                    <div class="price">TSh 45,000</div>
                    <a href="order.php?product_id=3" class="buy-btn">Buy now</a>
                </div>

            </div>
            <p class="no-results" id="no-results">No product found.</p>
        </div>

        <aside class="profile-summary">
            <a href="profile.php" aria-label="Open profile">
                <?php if ($home_avatar): ?>
                    <img class="dashboard-avatar" src="<?php echo htmlspecialchars($home_avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile picture">
                <?php else: ?>
                    <div class="dashboard-avatar avatar-placeholder" aria-label="No profile picture">&#128100;</div>
                <?php endif; ?>
            </a>
        </aside>

    </div>

    <script>
        const dashboard = document.querySelector('.dashboard-container');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const searchForm = document.getElementById('product-search');
        const searchInput = document.getElementById('product-search-input');
        const productCards = document.querySelectorAll('.product-card');
        const noResults = document.getElementById('no-results');

        sidebarToggle.addEventListener('click', () => {
            const isClosed = dashboard.classList.toggle('sidebar-closed');
            sidebarToggle.textContent = isClosed ? '☰' : '×';
            sidebarToggle.setAttribute('aria-expanded', String(!isClosed));
            sidebarToggle.setAttribute('aria-label', isClosed ? 'Open menu' : 'Close menu');
        });

        const filterProducts = () => {
            const searchTerm = searchInput.value.trim().toLowerCase();
            let visibleProducts = 0;

            productCards.forEach((card) => {
                const productName = card.querySelector('h3').textContent.toLowerCase();
                const matches = searchTerm !== '' && (searchTerm === 'product' || productName.includes(searchTerm));
                card.classList.toggle('is-hidden', !matches);
                visibleProducts += matches ? 1 : 0;
            });

            noResults.style.display = searchTerm !== '' && visibleProducts === 0 ? 'block' : 'none';
        };

        searchForm.addEventListener('submit', (event) => {
            event.preventDefault();
            filterProducts();
        });

        searchInput.addEventListener('input', filterProducts);
        filterProducts();
    </script>
</body>
</html>
