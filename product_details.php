<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: login.php");
    exit;
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}


$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 3");
$related_stmt->execute([$product['category_id'], $product_id]);
$related_products = $related_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - بازار ہب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
 body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f8;
    color: #1f2937;
}

/* NAVBAR */
.navbar {
    background: linear-gradient(90deg, #0b1220, #0f172a);
    padding: 14px 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.navbar-brand, .nav-link {
    color: #fff !important;
    font-weight: 500;
}

.nav-link:hover {
    color: #10b981 !important;
}

/* MAIN PRODUCT WRAPPER */
.product-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    background: rgba(255,255,255,0.9);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.08);

    /* animation */
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* IMAGE */
.product-image {
    width: 100%;
    border-radius: 18px;
    object-fit: cover;
    transition: 0.5s ease;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}

.product-image:hover {
    transform: scale(1.05);
}

/* DETAILS */
.product-details h2 {
    font-size: 32px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 10px;
}

.product-details p {
    color: #64748b;
    line-height: 1.7;
    font-size: 15px;
}

/* PRICE (premium highlight) */
.price {
    font-size: 26px;
    font-weight: 700;
    color: #10b981;
    margin: 18px 0;
    display: inline-block;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* BUTTON */
.btn-success {
    background: linear-gradient(90deg, #10b981, #34d399);
    border: none;
    padding: 12px 22px;
    border-radius: 12px;
    font-weight: 600;
    transition: 0.3s;
    box-shadow: 0 10px 25px rgba(16,185,129,0.2);
}

.btn-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(16,185,129,0.3);
}

/* RELATED SECTION */
.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 22px;
    margin-top: 20px;
}

/* RELATED CARD */
.related-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: 0.4s ease;
}

.related-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

/* IMAGE */
.related-image {
    height: 180px;
    width: 100%;
    object-fit: cover;
    transition: 0.5s;
}

.related-card:hover .related-image {
    transform: scale(1.08);
}

/* TEXT */
.related-card-body {
    padding: 14px;
}

.related-card-body h5 {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
}

.related-card-body .price {
    font-size: 14px;
    color: #10b981;
    font-weight: 600;
}

/* FOOTER */
footer {
    margin-top: 60px;
    background: #0b1220;
    color: #9ca3af;
    text-align: center;
    padding: 25px;
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
                        <a class="nav-link" href="products.php"><i class="fas fa-arrow-left me-2"></i>Back to Products</a>
                    </li>
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
        <h2 class="mb-4">Product Details</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid product-image" alt="<?= htmlspecialchars($product['title']) ?>">
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h3><?= htmlspecialchars($product['title']) ?></h3>
                    <p class="text-muted"><i class="fas fa-tag me-2"></i><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                    <p><i class="fas fa-info-circle me-2"></i><strong>Description:</strong> <?= htmlspecialchars($product['description']) ?></p>
                    <p><i class="fas fa-dollar-sign me-2"></i><strong>Price:</strong> ₹<?= $product['price'] ?></p>
                    <p><i class="fas fa-tags me-2"></i><strong>Offer:</strong> ₹<?= $product['offer'] ?></p>
                    <a href="cart.php?action=add&product_id=<?= $product['id'] ?>" class="btn btn-success"><i class="fas fa-cart-plus me-2"></i>Add to Cart</a>
                </div>
            </div>
        </div>
        <?php if (!empty($related_products)): ?>
            <h3 class="mt-5 mb-4">Related Products</h3>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card related-card">
                            <img src="<?= htmlspecialchars($related['image']) ?>" class="card-img-top related-image" alt="<?= htmlspecialchars($related['title']) ?>">
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($related['title']) ?></h6>
                                <p class="card-text text-muted">Offer: ₹<?= $related['offer'] ?></p>
                                <a href="product_details.php?id=<?= $related['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye me-2"></i>View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <footer class="text-center">
        <div class="container">
            <p>&copy; All Rights Reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
