<?php
// Force error reporting to debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch Order History
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$user_id]);
$orderHistory = $orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;500;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #faf9f6; font-family: 'DM Sans', sans-serif; }
        .card { border: none; border-radius: 0; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar { background-color: #fff; box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .navbar-brand { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand fs-3 fw-bold" href="index.php">AURORA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php#catalog">Collections</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#refills">Refills</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-dark" href="index.php">Cart</a></li>
                    <li class="nav-item dropdown ms-3 d-flex align-items-center">
                        <a class="nav-link dropdown-toggle fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="profile.php">Order History</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="api/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">My Profile</h2>
            <div class="card p-4 mb-5">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username'] ?? 'User'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                <a href="index.php" class="btn btn-dark rounded-0 px-4">Back to Catalog</a>
            </div>
            
            <h3 class="mb-3">Order History</h3>
            <div class="card p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orderHistory)): ?>
                            <tr><td colspan="4" class="text-center py-4">No orders found.</td></tr>
                        <?php else: ?>
                            <?php foreach($orderHistory as $o): ?>
                            <tr>
                                <td>#<?php echo $o['id']; ?></td>
                                <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($o['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>