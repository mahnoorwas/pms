<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] == 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: products.php");
        }
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - بازار ہب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
    margin: 0;
    height: 100vh;
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;

    background: radial-gradient(circle at 20% 20%, #0f766e, transparent 40%),
                radial-gradient(circle at 80% 80%, #1e3a8a, transparent 40%),
                radial-gradient(circle at 50% 50%, #0f172a, #020617);
}

/* floating glow blobs */
body::before,
body::after {
    content: "";
    position: absolute;
    width: 450px;
    height: 450px;
    border-radius: 50%;
    filter: blur(120px);
    opacity: 0.6;
    z-index: 0;
}

body::before {
    background: #10b981;
    top: -100px;
    left: -100px;
}

body::after {
    background: #3b82f6;
    bottom: -120px;
    right: -120px;
}

/* Login Card */
.login-card {
    width: 380px;
    padding: 40px;
    border-radius: 18px;
    position: relative;
    z-index: 2;

    background: rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);

    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.6);

    text-align: center;
    color: #fff;

    transition: 0.4s ease;
}

.login-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 30px 90px rgba(0, 0, 0, 0.7);
}

/* Title */
.login-card h2 {
    font-size: 30px;
    margin-bottom: 25px;
    font-weight: 600;

    background: linear-gradient(90deg, #34d399, #60a5fa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Inputs */
.form-control {
    width: 100%;
    padding: 13px 15px;
    margin-bottom: 15px;
    border-radius: 12px;

    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.07);

    color: #fff;
    outline: none;

    transition: 0.3s;
}

.form-control::placeholder {
    color: rgba(255,255,255,0.5);
}

.form-control:focus {
    border-color: #34d399;
    box-shadow: 0 0 15px rgba(52,211,153,0.4);
    background: rgba(255,255,255,0.1);
}

/* Button */
.btn-primary {
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    border: none;
    cursor: pointer;

    font-weight: 600;
    color: #06281f;

    background: linear-gradient(90deg, #34d399, #60a5fa);

    transition: 0.3s;
}

.btn-primary:hover {
    transform: scale(1.06);
    box-shadow: 0 15px 35px rgba(52,211,153,0.35);
}

/* small text */
.small-text {
    margin-top: 12px;
    font-size: 12px;
    color: rgba(255,255,255,0.6);
}
    </style>
</head>
<body>
    <div class="card login-card p-4">
        <h2 class="text-center mb-4"><i class="fas fa-sign-in-alt me-2"></i>Login</h2>
        <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label"><i class="fas fa-user me-2"></i>Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>