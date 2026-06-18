<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurora Pens | Premium Writing Instruments</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;500;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1a1a1a;
            --accent-color: #d4af37;
            --bg-color: #faf9f6;
        }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--primary-color);
        }

        h1, h2, h3, h4, h5, .navbar-brand {
            font-family: 'Playfair Display', serif;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .hero-section {
            background-color: var(--primary-color);
            color: #fff;
            padding: 100px 0;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }

        .product-card {
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .product-img-wrapper {
            height: 250px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }

        .product-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .btn-accent {
            background-color: var(--accent-color);
            color: #fff;
            border: none;
            border-radius: 0;
            padding: 10px 20px;
            font-weight: 500;
        }

        .btn-accent:hover {
            background-color: #b5952f;
            color: #fff;
        }
        
        .badge-specs {
            background-color: #eee;
            color: #555;
            font-size: 0.8rem;
            margin-right: 5px;
        }

        .section-divider {
            border-top: 1px solid #e0dbd1;
            margin: 60px 0;
        }
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
                    <li class="nav-item"><a class="nav-link" href="#catalog">Collections</a></li>
                    <li class="nav-item"><a class="nav-link" href="#refills">Refills</a></li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-dark" href="#" data-bs-toggle="modal" data-bs-target="#cartModal" id="cartToggle">
                            Cart (<span id="cartCount">0</span>)
                        </a>
                    </li>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
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
                    <?php else: ?>
                        <li class="nav-item ms-3 d-flex align-items-center gap-2">
                            <a class="btn btn-outline-dark btn-sm rounded-0 px-3 py-2" href="register.html">Register</a>
                            <a class="btn btn-dark text-white btn-sm rounded-0 px-4 py-2" href="login.html">Login</a>
                        </li>
                    <?php endif; ?>
                    
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <h1>Leave Your Mark</h1>
            <p class="lead mb-4">Precision engineered ballpens for the modern thinker.</p>
            <a href="#catalog" class="btn btn-accent btn-lg">Explore the Catalog</a>
        </div>
    </header>

    <section id="catalog" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Featured Instruments</h2>
            <div class="row" id="productGrid">
                <div class="text-center w-100">
                    <div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container"><div class="section-divider"></div></div>

    <section id="refills" class="py-5" style="background-color: #f4f2ec;">
        <div class="container">
            <h2 class="text-center mb-2">Official Refills & Spares</h2>
            <p class="text-center text-muted mb-5">Keep your fine instruments writing forever with genuine component modules.</p>
            <div class="row" id="refillGrid">
                <div class="text-center w-100">
                    <div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Loading Refills...</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-0 border-0 shadow">
                <div class="modal-header border-0 bg-dark text-white rounded-0">
                    <h5 class="modal-title" id="cartModalLabel">Your Configuration Cart</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="cartModalBodyContent">
                        <p class="text-muted text-center my-4">Your layout queue configuration drawer is empty.</p>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light d-flex justify-content-between rounded-0">
                    <div>
                        <h5 class="mb-0 fw-bold">Grand Total: $<span id="cartModalGrandTotal">0.00</span></h5>
                    </div>
                    <div class="gap-2 d-flex">
                        <button type="button" class="btn btn-outline-secondary rounded-0 px-4 py-2" data-bs-dismiss="modal">Continue Reviewing</button>
                        <button type="button" class="btn btn-dark rounded-0 px-4 py-2" id="submitCheckoutBtn" onclick="executeCartCheckout()">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        document.addEventListener('DOMContentLoaded', () => {
            fetchProducts();
            const myModalEl = document.getElementById('cartModal');
            myModalEl.addEventListener('show.bs.modal', () => {
                renderCartModalContents();
            });
        });

        async function fetchProducts() {
            try {
                const response = await fetch('api/get_products.php');
                const result = await response.json();
                if(result.status === 'success') {
                    distributeCatalogData(result.data);
                } else {
                    renderFallbackProducts();
                }
            } catch (error) {
                renderFallbackProducts(); 
            }
        }

        function distributeCatalogData(allProducts) {
            const penCollection = allProducts.filter(item => parseInt(item.category_id) !== 4);
            const refillCollection = allProducts.filter(item => parseInt(item.category_id) === 4);
            renderProductGrid(penCollection, 'productGrid', false);
            renderProductGrid(refillCollection, 'refillGrid', true);
        }

        function renderProductGrid(products, targetContainerId, isRefillCatalog) {
            const container = document.getElementById(targetContainerId);
            container.innerHTML = '';
            products.forEach(product => {
                const optionsLayout = isRefillCatalog 
                    ? `<option value="1">Single Refill Pack</option><option value="10">Wholesale Box (10 Packs)</option>`
                    : `<option value="1">Single Unit Pen</option><option value="12">Box of 12 Pens (Bulk Lot)</option><option value="1200">Commercial Carton (1,200 Pens)</option>`;
                const card = `
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <div class="product-img-wrapper"><img src="${product.image_url}" class="product-img" alt=""></div>
                            <div class="card-body d-flex flex-column">
                                <span class="text-muted small mb-2">${product.category_name || 'Accessory'}</span>
                                <h5 class="card-title">${product.name}</h5>
                                <div class="mb-3">
                                    <span class="badge badge-specs">${product.ink_color} Ink</span>
                                    <span class="badge badge-specs">${product.tip_size}</span>
                                    <span class="badge bg-light text-dark border">Stock: ${product.stock_quantity}</span>
                                </div>
                                <h6 class="mb-3 fw-bold">$${parseFloat(product.price).toFixed(2)} each</h6>
                                <div class="mb-3 mt-auto">
                                    <select id="mode-${product.id}" class="form-select form-select-sm rounded-0 mb-2">${optionsLayout}</select>
                                    <input type="number" id="qty-${product.id}" class="form-control rounded-0" value="1" min="1">
                                </div>
                                <button class="btn btn-accent w-100 add-to-cart-btn" data-id="${product.id}" data-name="${product.name.replace(/"/g, '&quot;')}" data-price="${product.price}">Add to Cart</button>
                            </div>
                        </div>
                    </div>`;
                container.innerHTML += card;
            });
            document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                btn.onclick = function() { processAddToCart(this.dataset.id, this.dataset.name, this.dataset.price); };
            });
        }

        function processAddToCart(id, name, price) {
            const mode = parseInt(document.getElementById('mode-' + id).value);
            const qty = parseInt(document.getElementById('qty-' + id).value);
            cart.push({ id, name, price: parseFloat(price), quantity: mode * qty });
            updateCartUI();
            alert(`Added to cart.`);
        }

        function updateCartUI() {
            document.getElementById('cartCount').textContent = cart.reduce((s, i) => s + i.quantity, 0);
        }

        function renderCartModalContents() {
            const bodyContent = document.getElementById('cartModalBodyContent');
            bodyContent.innerHTML = cart.map((item, index) => `
                <div class="d-flex justify-content-between mb-2">
                    <span>${item.name} (x${item.quantity})</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})">Delete</button>
                </div>`).join('');
            document.getElementById('cartModalGrandTotal').textContent = cart.reduce((s, i) => s + (i.price * i.quantity), 0).toFixed(2);
        }

        function removeItem(index) {
            cart.splice(index, 1);
            updateCartUI();
            renderCartModalContents();
        }

        async function executeCartCheckout() {
            const res = await fetch('api/checkout.php', { method: 'POST', body: JSON.stringify({ cart }) });
            const data = await res.json();
            alert(data.message);
            location.reload();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>