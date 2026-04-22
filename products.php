<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: login.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$selected_category = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$products_query = $selected_category ? 
    "SELECT * FROM products WHERE category_id = ?" : 
    "SELECT * FROM products";
$stmt = $pdo->prepare($products_query);
if ($selected_category) {
    $stmt->execute([$selected_category]);
} else {
    $stmt->execute();
}
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - بازار ہب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
     body {
    font-family: 'Poppins', sans-serif;
    background: #f6f7fb;
    color: #111827;
    overflow-x: hidden;
}

/* NAVBAR */
.navbar {
    background: #0b1220;
    padding: 14px 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

/* PRODUCT CARD GRID ANIMATION */
.product-card {
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #eef0f3;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    transition: all 0.4s ease;
    position: relative;

    /* animation on load */
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.6s ease forwards;
}

/* stagger effect (optional if multiple cards) */
.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.2s; }
.product-card:nth-child(3) { animation-delay: 0.3s; }
.product-card:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* HOVER MOTION (important) */
.product-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 25px 60px rgba(0,0,0,0.15);
}

/* IMAGE ZOOM SMOOTH */
.product-image {
    height: 240px;
    width: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

/* TEXT */
.card-body {
    padding: 16px;
}

/* PRICE PULSE EFFECT */
.price {
    font-size: 16px;
    font-weight: 700;
    color: #10b981;
    display: inline-block;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.85; }
    100% { transform: scale(1); opacity: 1; }
}

/* BUTTON */
.btn-success {
    background: #10b981;
    border: none;
    border-radius: 10px;
    padding: 8px 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

/* BUTTON SHINE EFFECT */
.btn-success::after {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: rgba(255,255,255,0.3);
    transform: skewX(-20deg);
    transition: 0.5s;
}

.btn-success:hover::after {
    left: 120%;
}

.btn-success:hover {
    transform: translateY(-2px);
}

/* FLOATING BACKGROUND DOTS */
body::before {
    content: "";
    position: fixed;
    width: 300px;
    height: 300px;
    background: rgba(16,185,129,0.1);
    filter: blur(100px);
    top: -50px;
    left: -50px;
    animation: float 6s ease-in-out infinite alternate;
}

body::after {
    content: "";
    position: fixed;
    width: 300px;
    height: 300px;
    background: rgba(59,130,246,0.1);
    filter: blur(100px);
    bottom: -50px;
    right: -50px;
    animation: float 8s ease-in-out infinite alternate;
}

@keyframes float {
    from { transform: translateY(0px); }
    to { transform: translateY(30px); }
}

/* FOOTER */
footer {
    background: #0b1220;
    color: #9ca3af;
    padding: 25px;
    text-align: center;
    margin-top: 50px;
}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">بازار ہب</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="view_cart.php"><i class="fas fa-shopping-cart me-2"></i>View Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="mb-4">Our Products</h2>
        <div class="mb-4">
            <label for="category" class="form-label"><i class="fas fa-filter me-2"></i>Filter by Category</label>
            <select onchange="location = this.value;" class="form-select w-25" id="category">
                <option value="products.php">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="products.php?category_id=<?= $category['id'] ?>" <?= $selected_category == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                            <p class="card-text text-muted">Price: ₹<?= $product['price'] ?> <br>Offer: ₹<?= $product['offer'] ?></p>
                            <div class="d-flex justify-content-between">
                                <a href="product_details.php?id=<?= $product['id'] ?>" class="btn btn-primary"><i class="fas fa-eye me-2"></i>View Details</a>
                                <a href="cart.php?action=add&product_id=<?= $product['id'] ?>" class="btn btn-success"><i class="fas fa-cart-plus me-2"></i>Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer class="text-center">
        <div class="container">
            <p>&copy; All Rights Reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>