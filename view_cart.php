<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.*, p.title, p.price, p.offer, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $cart_id = $_GET['cart_id'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    header("Location: view_cart.php");
    exit;
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['offer'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart - بازار ہب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f8;
    color: #111827;
    margin: 0;
    position: relative;
    overflow-x: hidden;
}

/* NAVBAR */
.navbar {
    background: #0b1220;
    padding: 14px 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

/* PAGE WRAPPER */
.container {
    max-width: 1100px;
    margin: 40px auto;
    position: relative;
    z-index: 2;
}

/* CART LAYOUT */
.cart-wrapper {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

/* CART ITEMS CARD */
.table {
    width: 100%;
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    animation: fadeUp 0.5s ease;
}

/* REMOVE TABLE LOOK */
.table thead {
    display: none;
}

.table tbody tr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px;
    border-bottom: 1px solid #f1f5f9;
    transition: 0.3s;
}

.table tbody tr:hover {
    background: #f9fafb;
    transform: scale(1.01);
}

/* PRODUCT IMAGE */
.cart-image {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* REMOVE BUTTON */
.btn-danger {
    background: #ef4444;
    border: none;
    border-radius: 10px;
    padding: 6px 12px;
    font-weight: 500;
    transition: 0.3s;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

/* CHECKOUT SIDEBAR */
.cart-summary {
    background: #ffffff;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    position: sticky;
    top: 20px;
    height: fit-content;
    animation: fadeUp 0.6s ease;
}

.cart-summary h3 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #0f172a;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    margin: 10px 0;
    color: #6b7280;
}

.total {
    font-size: 22px;
    font-weight: 700;
    color: #10b981;
    margin-top: 15px;
}

/* CHECKOUT BUTTON */
.btn-success {
    width: 100%;
    margin-top: 15px;
    background: linear-gradient(90deg, #10b981, #34d399);
    border: none;
    border-radius: 12px;
    padding: 12px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(16,185,129,0.3);
}

/* EMPTY CART */
.empty-cart {
    text-align: center;
    padding: 60px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    color: #6b7280;
}

/* FADE ANIMATION */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ========================= */
/* 🌈 ANIMATED BACKGROUND */
/* ========================= */

/* moving gradient layer */
body::before {
    content: "";
    position: fixed;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        rgba(16,185,129,0.08),
        rgba(59,130,246,0.08),
        rgba(236,72,153,0.05),
        rgba(245,158,11,0.06)
    );
    animation: moveBg 18s linear infinite;
    z-index: -2;
}

/* floating glow 1 */
body::after {
    content: "";
    position: fixed;
    width: 400px;
    height: 400px;
    background: rgba(16,185,129,0.15);
    filter: blur(120px);
    top: 10%;
    left: -120px;
    border-radius: 50%;
    animation: float1 10s ease-in-out infinite alternate;
    z-index: -1;
}

/* floating glow 2 */
.container::before {
    content: "";
    position: fixed;
    width: 350px;
    height: 350px;
    background: rgba(59,130,246,0.12);
    filter: blur(120px);
    bottom: 5%;
    right: -120px;
    border-radius: 50%;
    animation: float2 12s ease-in-out infinite alternate;
    z-index: -1;
}

/* animations */
@keyframes moveBg {
    0% { transform: translate(0,0) rotate(0deg); }
    50% { transform: translate(5%,5%) rotate(180deg); }
    100% { transform: translate(0,0) rotate(360deg); }
}

@keyframes float1 {
    from { transform: translateY(0px); }
    to { transform: translateY(40px); }
}

@keyframes float2 {
    from { transform: translateY(0px); }
    to { transform: translateY(-40px); }
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
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="mb-4">Your Cart</h2>
        <?php if (empty($cart_items)): ?>
            <p class="text-muted">Your cart is empty.</p>
        <?php else: ?>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Offer</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($item['image']) ?>" class="cart-image" alt="<?= htmlspecialchars($item['title']) ?>"></td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td>$<?= $item['price'] ?></td>
                            <td>$<?= $item['offer'] ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>$<?= $item['offer'] * $item['quantity'] ?></td>
                            <td>
                                <a href="view_cart.php?action=remove&cart_id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt me-2"></i>Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <h4>Total: ₹<?= $total ?></h4>
                <a href="#" class="btn btn-success"><i class="fas fa-check me-2"></i>Proceed to Checkout</a>
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