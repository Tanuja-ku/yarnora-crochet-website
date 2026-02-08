<?php
session_start();
require_once "products.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crochet Haven | Handmade Crochet Marketplace</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
  
    <style>
        :root {
            --primary-color: #ff9eb5;
            --secondary-color: #a8e6cf;
            --accent-color: #ffd3b6;
            --dark-color: #6d6875;
            --light-color: #fff9f4;
            --text-color: #5a5a5a;
            --border-radius: 12px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
      
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
      
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: #ffffff;
            line-height: 1.6;
        }
      
        h1, h2, h3, h4, h5 {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            color: var(--dark-color);
        }
      
        /* Navbar Styles */
        .navbar {
            background-color: white;
            padding: 1.2rem 0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand img {
             border-radius: 8px;
         }
      
        .navbar-brand {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.5px;
        }
      
        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            margin: 0 0.5rem;
            transition: var(--transition);
            position: relative;
        }
      
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
      
        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
        }
      
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0.5rem;
            right: 0.5rem;
            height: 2px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }
      
        .nav-icons {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }
      
        .nav-icons a {
            color: #6D6875;
            font-size: 1.2rem;
            transition: var(--transition);
            position: relative;
             font-weight: 500;
        }
      
        .nav-icons a:hover {
            color: #FF9EB5;
        }
      
        .cart-count, .wishlist-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--primary-color);
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
      
        /* Filter Sidebar */
        .filter-section {
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--box-shadow);
        }
      
        .filter-title {
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
      
        .filter-group {
            margin-bottom: 1.8rem;
        }
      
        .filter-group h5 {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            color: var(--dark-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
      
        .filter-group h5 i {
            transition: var(--transition);
        }
      
        .filter-group h5.collapsed i {
            transform: rotate(180deg);
        }
      
        .filter-options {
            padding-left: 0.5rem;
        }
      
        .form-check {
            margin-bottom: 0.6rem;
        }
      
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
      
        .form-check-label {
            cursor: pointer;
        }
      
        .price-slider {
            width: 100%;
        }
      
        .price-range {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--dark-color);
        }
      
        .in-stock-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 0.8rem 1rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }
      
        /* Product Grid */
        .product-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
        }
      
        .sort-dropdown .btn {
            background-color: white;
            border: 1px solid #eee;
            color: var(--dark-color);
            border-radius: var(--border-radius);
            padding: 0.5rem 1.2rem;
        }
      
        .sort-dropdown .btn:hover {
            border-color: var(--primary-color);
        }
      
        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
      
        /* Product Card */
        .product-card {
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            position: relative;
        }
      
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
      
        .product-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            cursor: pointer;
            transition: var(--transition);
        }
      
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
      
        .product-info {
            padding: 1.5rem;
        }
      
        .product-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
      
        .product-desc {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
      
        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
        }
      
        .product-actions {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            opacity: 0;
            transition: var(--transition);
        }
      
        .product-card:hover .product-actions {
            opacity: 1;
        }
      
        .wishlist-btn, .quick-view-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-color);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            border: none;
        }
      
        .wishlist-btn:hover, .quick-view-btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
      
        .wishlist-btn.active {
            background-color: var(--primary-color);
            color: white;
        }
      
        .add-to-cart-btn {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 1rem;
            font-weight: 600;
            transform: translateY(100%);
            transition: var(--transition);
            cursor: pointer;
            border: none;
        }
      
        .product-card:hover .add-to-cart-btn {
            transform: translateY(0);
        }
      
        .add-to-cart-btn:hover {
            background-color: #ff7b9d;
        }
      
        /* Product Details Modal Enhancements */
        .modal-content {
            border-radius: var(--border-radius);
            overflow: hidden;
            border: none;
        }
      
        .product-detail-gallery {
            position: relative;
        }
      
        .carousel-inner img {
            height: 400px;
            object-fit: cover;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
      
        .carousel-control-prev, .carousel-control-next {
            width: 5%;
        }
      
        .carousel-indicators {
            bottom: -50px;
        }
      
        .zoom-img {
            transition: transform 0.3s ease;
            cursor: zoom-in;
        }
      
        .zoom-img:hover {
            transform: scale(1.1);
        }
      
        .modal-title {
            color: var(--dark-color);
        }
      
        .modal-price {
            font-size: 1.8rem;
            color: var(--primary-color);
            font-weight: 700;
            margin: 1rem 0;
        }
      
        .modal-description {
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }
      
        .product-specs {
            margin-bottom: 1.5rem;
        }
      
        .product-specs dt {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
      
        .product-specs dd {
            color: var(--text-color);
            margin-bottom: 1rem;
        }
      
        .option-group {
            margin-bottom: 1.5rem;
        }
      
        .option-group label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: block;
        }
      
        .form-select, .btn-color-option {
            border-radius: var(--border-radius);
        }
      
        .btn-color-option {
            width: 40px;
            height: 40px;
            border: 2px solid transparent;
            margin: 0.25rem;
        }
      
        .btn-color-option.active {
            border-color: var(--primary-color);
        }
      
        .stock-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
      
        .stock-in {
            color: #28a745;
        }
      
        .stock-out {
            color: #dc3545;
        }
      
        .delivery-info {
            background-color: var(--secondary-color);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
      
        .detail-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
      
        .btn-primary, .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.7rem 1.8rem;
            border-radius: var(--border-radius);
            font-weight: 600;
        }
      
        .btn-primary:hover, .btn-success:hover {
            background-color: #ff7b9d;
            border-color: #ff7b9d;
        }
      
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            padding: 0.7rem 1.8rem;
            border-radius: var(--border-radius);
            font-weight: 600;
        }
      
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
      
        /* Cart Modal Enhancements */
        .cart-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
      
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: var(--border-radius);
        }
      
        .cart-item-info {
            flex: 1;
            padding: 0 1.2rem;
        }
      
        .cart-item-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.3rem;
        }
      
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }
      
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }
      
        .quantity-btn:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
      
        .quantity-display {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }
      
        .cart-item-subtotal {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.1rem;
        }
      
        .cart-item-remove {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.2rem;
        }
      
        .cart-item-remove:hover {
            color: var(--primary-color);
        }
      
        .cart-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.3rem;
            font-weight: 700;
            margin: 1.5rem 0;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }
      
        /* Footer */
        footer {
            background-color: var(--light-color);
            padding: 4rem 0 2rem;
            margin-top: 4rem;
        }
      
        .footer-logo {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
      
        .footer-heading {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }
      
        .footer-links {
            list-style: none;
            padding: 0;
        }
      
        .footer-links li {
            margin-bottom: 0.8rem;
        }
      
        .footer-links a {
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
        }
      
        .footer-links a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }
      
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 3rem;
            border-top: 1px solid #eee;
            color: #888;
        }
      
        /* Responsive Design */
        @media (max-width: 992px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }
      
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
                padding-top: 1rem;
            }
          
            .nav-icons {
                justify-content: center;
                padding-top: 1rem;
            }
          
            .product-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
          
            .products-grid {
                grid-template-columns: 1fr;
            }
          
            .detail-actions {
                flex-direction: column;
            }
          
            .detail-actions button {
                width: 100%;
            }
          
            .cart-item {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
          
            .quantity-controls {
                justify-content: center;
            }
        }
      
        /* Utility Classes */
        .soft-bg {
            background-color: var(--light-color);
        }
      
        .rounded-xl {
            border-radius: var(--border-radius);
        }
      
        .text-primary {
            color: var(--primary-color) !important;
        }

        /* Thumbnail images */
#thumbnailContainer img {
    border: 2px solid transparent;
    border-radius: 10px;
    transition: all 0.3s ease;
}

#thumbnailContainer img:hover {
    border-color: #ff9eb5;
    transform: scale(1.05);
}

/* Active selected thumbnail */
#thumbnailContainer img.active-thumb {
    border-color: #ff9eb5;
    box-shadow: 0 0 8px rgba(255,158,181,0.6);
}

/* Zoom effect on main image */
#mainProductImage {
    transition: transform 0.4s ease;
    cursor: zoom-in;
}

#mainProductImage:hover {
    transform: scale(1.15);
}

     .search-box input {
    border-radius: 20px;
    padding-left: 12px;
}

.search-box i {
    position: relative;
    right: 30px;
    pointer-events: none;
}
    </style>
</head>
<body>
    <!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">

       <!-- BRAND LOGO -->
<a class="navbar-brand d-flex align-items-center" href="index.php">
    <img src="logo.png" alt="Yarnora Logo"
         style="height:clamp(70px, 8vw, 90px); width:auto;"
         class="me-2">

    <div class="d-flex flex-column" style="line-height:1.1;">

        <span style="
            font-family:'Pacifico', cursive;
            font-size:clamp(22px, 4vw, 34px);
            font-weight:700;
            color:#FF9EB5;
            letter-spacing:1px;
        ">
            Yarnora
        </span>

        <small style="
            font-size:clamp(11px, 2vw, 15px);
            color:#6D6875;
            margin-top:-2px;
        ">
            Where Threads Bloom
        </small>

    </div>
</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- CENTER MENU -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="customization.php">Customization</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="sustainability.php">Sustainability Guide</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>

            <!-- RIGHT ICONS -->
            <div class="nav-icons d-flex align-items-center gap-3">

                <!-- SEARCH -->
                <div class="search-box d-flex align-items-center">
                    <input type="text" id="searchInput"
                           class="form-control form-control-sm me-2"
                           placeholder="Search products..." style="width:200px;border-radius:20px;">
                    <i class="fas fa-search text-muted"></i>
                </div>

                <!-- USER -->
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="user-icon dropdown-toggle text-decoration-none"
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($_SESSION['full_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="my_orders.php">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="#" class="user-icon text-decoration-none"
                       data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-user"></i> Login
                    </a>
                <?php endif; ?>

                <!-- WISHLIST -->
                <a href="#" class="wishlist-icon position-relative"
                   data-bs-toggle="modal" data-bs-target="#wishlistModal">
                    <i class="fas fa-heart"></i>
                    <span class="wishlist-count">0</span>
                </a>

                <!-- CART -->
                <a href="#" class="cart-icon position-relative"
                   data-bs-toggle="modal" data-bs-target="#cartModal">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count">0</span>
                </a>

            </div>
        </div>
    </div>
</nav>
    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Left Filter Section -->
            <div class="col-lg-3">
                <div class="filter-section">
                    <div class="filter-title">
                        <span>Browse</span>
                        <button class="btn btn-sm btn-outline-secondary" id="clearFilters">Clear All</button>
                    </div>
                  
                    <div class="in-stock-toggle">
                        <span>In stock only</span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="inStockToggle">
                        </div>
                    </div>
                  
                    <div class="filter-group">
                        <h5 data-bs-toggle="collapse" data-bs-target="#categoryFilter" aria-expanded="true">
                            Category
                            <i class="fas fa-chevron-up"></i>
                        </h5>
                        <div class="collapse show" id="categoryFilter">
                            <div class="filter-options">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="category1" data-category="Bags">
                                    <label class="form-check-label" for="category1">Bags & Totes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="category2" data-category="Jewelry">
                                    <label class="form-check-label" for="category2">Jewelry</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="category3" data-category="Accessories">
                                    <label class="form-check-label" for="category3">Accessories</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="category4" data-category="Home">
                                    <label class="form-check-label" for="category4">Home Decor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="category5" data-category="Clothing">
                                    <label class="form-check-label" for="category5">Clothing</label>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                    <div class="filter-group">
                        <h5 data-bs-toggle="collapse" data-bs-target="#priceFilter" aria-expanded="true">
                            Price
                            <i class="fas fa-chevron-up"></i>
                        </h5>
                        <div class="collapse show" id="priceFilter">
                            <div class="filter-options">
                                <input type="range" class="form-range price-slider" min="0" max="5000" step="100" id="priceRange">
                                <div class="price-range">
                                    <span>₹0</span>
                                    <span>₹5000</span>
                                </div>
                                <div class="mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="priceFilter" id="price1" value="0-1000">
                                        <label class="form-check-label" for="price1">Under ₹1000</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="priceFilter" id="price2" value="1000-2000">
                                        <label class="form-check-label" for="price2">₹1000 - ₹2000</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="priceFilter" id="price3" value="2000-5000">
                                        <label class="form-check-label" for="price3">₹2000 - ₹5000</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                    <div class="filter-group">
                        <h5 data-bs-toggle="collapse" data-bs-target="#typeFilter" aria-expanded="true">
                            Product Type
                            <i class="fas fa-chevron-up"></i>
                        </h5>
                        <div class="collapse show" id="typeFilter">
                            <div class="filter-options">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="type1" data-type="Handmade">
                                    <label class="form-check-label" for="type1">Handmade</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="type2" data-type="Eco-friendly">
                                    <label class="form-check-label" for="type2">Eco-friendly</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="type3" data-type="Customizable">
                                    <label class="form-check-label" for="type3">Customizable</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          
            <!-- Right Product Section -->
            <div class="col-lg-9">
                <div class="product-header">
                    <h3>Crochet Products</h3>
                    <div class="sort-dropdown">
                        <select class="form-select" id="sortProducts">
                            <option value="featured">Featured</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="name">Alphabetical</option>
                        </select>
                    </div>
                </div>
              
                <div class="products-grid" id="productsGrid">
                    <!-- Product cards will be dynamically generated here -->
                </div>
            </div>
        </div>
    </div>
    <!-- Product Details Modal with Reviews -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="productModalLabel">Product Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-4">

          <!-- LEFT : IMAGE VIEWER -->
          <div class="col-md-6">

            <!-- Main Image -->
            <div class="border rounded-4 mb-3 text-center p-2">
              <img id="mainProductImage" src="" class="img-fluid rounded-4"
                   style="max-height:400px; object-fit:contain;">
            </div>

            <!-- Thumbnails -->
            <div class="d-flex gap-2 justify-content-center flex-wrap" id="thumbnailContainer">
              <!-- thumbnails inserted dynamically -->
            </div>

          </div>

          <!-- RIGHT : DETAILS -->
          <div class="col-md-6">

            <h3 id="detailTitle" class="fw-bold"></h3>
            <p class="fs-3 text-primary fw-bold" id="detailPrice"></p>

            <div id="stockStatus" class="mb-2"></div>

            <p class="text-muted" id="detailDescription"></p>

            <dl class="row">
              <dt class="col-4">Materials:</dt>
              <dd class="col-8" id="detailMaterials"></dd>

              <dt class="col-4">Delivery:</dt>
              <dd class="col-8" id="detailDelivery"></dd>
            </dl>

            <div class="mb-3">
              <label class="fw-bold">Size:</label>
              <select class="form-select" id="sizeSelect"></select>
            </div>

            <div class="mb-3">
              <label class="fw-bold">Color:</label>
              <div id="colorOptions" class="d-flex gap-2"></div>
            </div>

            <div class="d-flex gap-3 mt-3">
              <button class="btn btn-success px-4" id="buyNowBtn">
                <i class="fas fa-bolt me-2"></i>Buy Now
              </button>
              <button class="btn btn-primary px-4" id="addToCartFromModal">
                <i class="fas fa-shopping-bag me-2"></i>Add to Cart
              </button>
              <button class="btn btn-outline-danger px-4" id="addToWishlistFromModal">
                <i class="fas fa-heart"></i>
              </button>
            </div>

            <!-- REVIEWS SECTION -->
            <hr class="my-4">

            <h5>⭐ Customer Reviews</h5>

            <!-- Reviews List -->
            <div id="reviewsContainer" class="mb-3">
              <!-- reviews will load here -->
            </div>

            <!-- Add Review Form -->
            <div class="border rounded p-3">
              <h6>Add Your Review</h6>

              <form id="reviewForm" enctype="multipart/form-data">

                <input type="hidden" id="reviewProductId" name="product_id">

                <div class="mb-2">
                  <label class="form-label">Rating</label>
                  <select class="form-select" id="reviewRating" name="rating" required>
                    <option value="">Select Rating</option>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="1">⭐</option>
                  </select>
                </div>

                <div class="mb-2">
                  <label class="form-label">Review</label>
                  <textarea class="form-control" id="reviewText"
                            name="review_text" rows="3" required></textarea>
                </div>

                <div class="mb-2">
                  <label class="form-label">Upload Images</label>
                  <input type="file" class="form-control"
                         id="reviewPhotos" name="photos[]" multiple accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary btn-sm mt-2">
                  Submit Review
                </button>

              </form>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>
    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="cartModalLabel">Your Shopping Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cartItems">
                        <!-- Cart items will be dynamically generated here -->
                        <p class="text-center py-4" id="emptyCartMessage">Your cart is empty</p>
                    </div>
                    <div class="cart-total">
                        <span>Total:</span>
                        <span id="cartTotal">₹0</span>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                    <button type="button" class="btn btn-primary" id="proceedToCheckout">Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Wishlist Modal -->
    <div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="wishlistModalLabel">Your Wishlist ❤️</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="wishlistItems">
                        <!-- Wishlist items will be dynamically generated here -->
                        <p class="text-center py-4" id="emptyWishlistMessage">Your wishlist is empty. Start adding your favorites!</p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                    <button type="button" class="btn btn-outline-danger" id="clearWishlist">Clear Wishlist</button>
                </div>
            </div>
        </div>
    </div>


      <!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Login</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="loginForm">
          <input type="text" name="login_input" class="form-control mb-2" placeholder="Email or Phone" required>
          <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="mt-2 text-center">
          No account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Register</a>
        </p>
        <div id="loginMsg" class="mt-2 text-center text-danger"></div>
      </div>
    </div>
  </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Register</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="registerForm">
          <input type="text" name="full_name" class="form-control mb-2" placeholder="Full Name" required>
          <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
          <input type="text" name="phone" class="form-control mb-2" placeholder="Phone" required>
          <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
          <button type="submit" class="btn btn-success w-100">Register</button>
        </form>
        <div id="registerMsg" class="mt-2 text-center text-danger"></div>
      </div>
    </div>
  </div>
</div>
    <!-- FOOTER -->
<footer style="background:#fff1f4; padding-top:50px; margin-top:60px;">
    <div class="container">

        <div class="row">

            <!-- BRAND -->
            <div class="col-lg-4 mb-4">
                <h4 style="font-family:'Pacifico',cursive; color:#ff9eb5;">
                    Yarnora
                </h4>
                <p style="color:#6d6875;">
                    Where threads bloom into beautiful handmade creations.
                    Discover sustainable crochet products crafted with love,
                    comfort, and creativity.
                </p>

                <!-- SOCIAL ICONS -->
                <div class="mt-3">
                    <a href="#" class="me-3 text-dark"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="me-3 text-dark"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="me-3 text-dark"><i class="fab fa-pinterest"></i></a>
                    <a href="#" class="text-dark"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- SHOP LINKS -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Shop</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-decoration-none text-muted">All Products</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">New Arrivals</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">Best Sellers</a></li>
                    <li><a href="customization.php" class="text-decoration-none text-muted">Custom Orders</a></li>
                </ul>
            </div>

            <!-- HELP -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Help</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-decoration-none text-muted">FAQs</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">Shipping & Returns</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">Size Guide</a></li>
                    <li><a href="contact.php" class="text-decoration-none text-muted">Contact Us</a></li>
                </ul>
            </div>

            <!-- NEWSLETTER -->
            <div class="col-lg-4 mb-4">
                <h6 class="fw-bold mb-3">Stay Connected</h6>
                <p class="text-muted">
                    Subscribe for new collections, offers and crochet inspiration.
                </p>

                <div class="input-group">
                    <input type="email"
                           class="form-control"
                           placeholder="Enter your email">
                    <button class="btn"
                            style="background:#ff9eb5; color:white;">
                        Subscribe
                    </button>
                </div>
            </div>

        </div>

        <!-- BOTTOM BAR -->
        <hr>

        <div class="d-md-flex justify-content-between align-items-center pb-3">
            <p class="mb-0 text-muted">
                © 2026 Yarnora. All rights reserved.
            </p>

            <p class="mb-0 text-muted">
                Handmade with <i class="fas fa-heart" style="color:#ff9eb5;"></i>
            </p>
        </div>

    </div>
</footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
const products = <?php echo json_encode($products); ?>;

// Cart and Wishlist
let cart = [];
let wishlist = [];

// DOM Elements
const productsGrid = document.getElementById('productsGrid');
const cartCount = document.querySelector('.cart-count');
const wishlistCount = document.querySelector('.wishlist-count');
const cartItemsContainer = document.getElementById('cartItems');
const cartTotal = document.getElementById('cartTotal');
const emptyCartMessage = document.getElementById('emptyCartMessage');
const wishlistItemsContainer = document.getElementById('wishlistItems');
const emptyWishlistMessage = document.getElementById('emptyWishlistMessage');

const productModal = new bootstrap.Modal(document.getElementById('productModal'));
const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
const wishlistModal = new bootstrap.Modal(document.getElementById('wishlistModal'));

// Load LocalStorage
function loadFromLocalStorage() {
    cart = JSON.parse(localStorage.getItem("cart")) || [];
    wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
}
function saveToLocalStorage() {
    localStorage.setItem("cart", JSON.stringify(cart));
    localStorage.setItem("wishlist", JSON.stringify(wishlist));
}

// Init
document.addEventListener("DOMContentLoaded", () => {
    loadFromLocalStorage();
    renderProducts(products);
    updateCartCount();
    updateWishlistCount();
});

// ================= RENDER PRODUCTS =================
function renderProducts(list) {
    productsGrid.innerHTML = "";

    list.forEach(product => {
        const card = document.createElement("div");
        card.className = "product-card";

        const isWish = wishlist.includes(product.id);

        card.innerHTML = `
            <img src="${product.images[0]}" class="product-img" data-id="${product.id}">
            <div class="product-actions">
                <button class="wishlist-btn ${isWish ? 'active' : ''}" data-id="${product.id}">
                    <i class="fas fa-heart"></i>
                </button>
                <button class="quick-view-btn" data-id="${product.id}">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="product-info">
                <h4>${product.name}</h4>
                <p>${product.description}</p>
                <strong>₹${product.price}</strong>
                ${!product.inStock ? '<span class="badge bg-secondary">Out of Stock</span>' : ''}
            </div>
            <button class="add-to-cart-btn" data-id="${product.id}" ${!product.inStock?'disabled':''}>
                ${!product.inStock ? 'Out of Stock' : 'Add to Cart'}
            </button>
        `;
        productsGrid.appendChild(card);
    });

    attachProductEvents();
}

// ================= EVENTS =================
function attachProductEvents() {
    document.querySelectorAll(".product-img,.quick-view-btn").forEach(el=>{
        el.onclick = ()=> showProductDetails(parseInt(el.dataset.id));
    });

    document.querySelectorAll(".add-to-cart-btn").forEach(btn=>{
        btn.onclick = ()=> addToCart(parseInt(btn.dataset.id));
    });

    document.querySelectorAll(".wishlist-btn").forEach(btn=>{
        btn.onclick = ()=> toggleWishlist(parseInt(btn.dataset.id), btn);
    });
}

// ================= PRODUCT MODAL =================
     function showProductDetails(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    document.getElementById("reviewProductId").value = productId;
    loadReviews(productId);

    document.getElementById("detailTitle").innerText = product.name;
    document.getElementById("detailPrice").innerText = "₹" + product.price;
    document.getElementById("detailDescription").innerText = product.description;

    document.getElementById("detailMaterials").innerText = product.materials || "Premium cotton yarn";
    document.getElementById("detailDelivery").innerText = "3-5 business days";

    const stockStatus = document.getElementById("stockStatus");
    stockStatus.innerHTML = product.inStock
        ? `<span class="badge bg-success">In Stock</span>`
        : `<span class="badge bg-danger">Out of Stock</span>`;

      // MAIN IMAGE
const mainImg = document.getElementById("mainProductImage");
mainImg.src = product.images[0];

// THUMBNAILS
const thumbContainer = document.getElementById("thumbnailContainer");
thumbContainer.innerHTML = "";

product.images.forEach((img, index) => {
    const thumb = document.createElement("img");
    thumb.src = img;
    thumb.className = "img-thumbnail";
    thumb.style.width = "80px";
    thumb.style.cursor = "pointer";

    // First image active by default
    if(index === 0){
        thumb.classList.add("active-thumb");
    }

      thumb.onclick = () => {

    // Change main image
    mainImg.src = img;

    // SAVE selected image
    selectedImageIndex = index;

    // Remove active class from all thumbnails
    document.querySelectorAll("#thumbnailContainer img")
        .forEach(t => t.classList.remove("active-thumb"));

    // Add active class
    thumb.classList.add("active-thumb");
};


    thumbContainer.appendChild(thumb);
});

    document.getElementById("addToCartFromModal").onclick = () => {
    addToCart(productId, selectedImageIndex);
    productModal.hide();
};

    document.getElementById("addToWishlistFromModal").onclick = () => {
        toggleWishlist(productId);
        productModal.hide();
    };

   document.getElementById("buyNowBtn").onclick = () => {
    addToCart(productId, selectedImageIndex);
    productModal.hide();
    cartModal.show();
};


    productModal.show();
}


    // ================= REVIEWS ONLY =================

// Call this inside showProductDetails(productId)
function loadReviews(productId){
    fetch("get_reviews.php?product_id=" + productId)
        .then(res => res.json())
        .then(data => {

            const container = document.getElementById("reviewsContainer");
            container.innerHTML = "";

            if(data.length === 0){
                container.innerHTML = "<p>No reviews yet.</p>";
                return;
            }

            data.forEach(r => {

                let photosHtml = "";
                if(r.photos && r.photos.length > 0){
                    r.photos.forEach(img => {
                        photosHtml += `<img src="${img}" width="80" class="me-2 mb-2 rounded">`;
                    });
                }

                container.innerHTML += `
                <div class="border rounded p-2 mb-2">
                    <strong>${"⭐".repeat(r.rating)}</strong>
                    <p>${r.review_text}</p>
                    ${photosHtml}
                    <small class="text-muted">By ${r.full_name} | ${r.created_at}</small>
                </div>
                `;
            });

        });
}


// SUBMIT REVIEW
document.getElementById("reviewForm").addEventListener("submit", function(e){
    e.preventDefault();

    const formData = new FormData();
    formData.append("product_id", document.getElementById("reviewProductId").value);
    formData.append("rating", document.getElementById("reviewRating").value);
    formData.append("review_text", document.getElementById("reviewText").value);

    const files = document.getElementById("reviewPhotos").files;
    for(let i=0;i<files.length;i++){
        formData.append("photos[]", files[i]);
    }

    fetch("add_review.php", {
        method:"POST",
        body: formData
    })
    .then(res=>res.json())
    .then(data=>{
        alert(data.message);
        if(data.success){
            loadReviews(document.getElementById("reviewProductId").value);
            document.getElementById("reviewForm").reset();
        }
    });
});
// ================= CART =================
function addToCart(productId, imageIndex = 0){
    const product = products.find(p=>p.id===productId);
    if(!product || !product.inStock) return;

    let item = cart.find(i=>i.id===productId);
    if(item){
        item.quantity++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.images[imageIndex],
            quantity: 1
        });
    }

    updateCartCount();
    saveToLocalStorage();
    showNotification(product.name+" added to cart");
}

function updateCartCount(){
    let total = cart.reduce((sum,i)=>sum+i.quantity,0);
    cartCount.textContent = total;
    cartCount.style.display = total>0?"flex":"none";
}

function renderCart(){
    cartItemsContainer.innerHTML="";
    if(cart.length===0){
        emptyCartMessage.style.display="block";
        cartTotal.textContent="₹0";
        return;
    }
    emptyCartMessage.style.display="none";

    let total=0;

    cart.forEach(item=>{
        let subtotal = item.price * item.quantity;
        total += subtotal;

        cartItemsContainer.innerHTML += `
        <div class="cart-item" style="cursor:pointer;">

            <img src="${item.image}" class="cart-item-img"
                 onclick="showProductDetails(${item.id})">

            <div class="cart-item-info" onclick="showProductDetails(${item.id})">
                <div class="cart-item-title">${item.name}</div>
                <div>₹${item.price}</div>

                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="updateQty(${item.id}, -1); event.stopPropagation();">-</button>
                    <span class="quantity-display">${item.quantity}</span>
                    <button class="quantity-btn" onclick="updateQty(${item.id}, 1); event.stopPropagation();">+</button>
                </div>
            </div>

            <div class="cart-item-subtotal">₹${subtotal}</div>

            <button class="cart-item-remove" onclick="removeFromCart(${item.id}); event.stopPropagation();">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        `;
    });

    cartTotal.textContent="₹"+total;
}

function removeFromCart(productId){
    cart = cart.filter(item => item.id !== productId);
    saveToLocalStorage();
    updateCartCount();
    renderCart();
}

function updateQty(productId, change){
    let item = cart.find(i => i.id === productId);
    if(!item) return;

    item.quantity += change;
    if(item.quantity <= 0){
        removeFromCart(productId);
        return;
    }

    saveToLocalStorage();
    updateCartCount();
    renderCart();
}

// ================= WISHLIST =================
function toggleWishlist(productId,btn=null){
    const index = wishlist.indexOf(productId);
    const product = products.find(p=>p.id===productId);

    if(index>-1){
        wishlist.splice(index,1);
        if(btn) btn.classList.remove("active");
        showNotification(product.name+" removed from wishlist");
    } else {
        wishlist.push(productId);
        if(btn) btn.classList.add("active");
        showNotification(product.name+" added to wishlist");
    }

    updateWishlistCount();
    saveToLocalStorage();
    renderWishlist();
}

function updateWishlistCount(){
    wishlistCount.textContent = wishlist.length;
    wishlistCount.style.display = wishlist.length>0?"flex":"none";
}

   function renderWishlist(){
    wishlistItemsContainer.innerHTML="";
    if(wishlist.length===0){
        emptyWishlistMessage.style.display="block";
        return;
    }
    emptyWishlistMessage.style.display="none";

    wishlist.forEach(id=>{
        const p = products.find(x=>x.id===id);

        wishlistItemsContainer.innerHTML += `
        <div class="cart-item" style="cursor:pointer;">

            <img src="${p.images[0]}" class="cart-item-img"
                 onclick="showProductDetails(${p.id})">

            <div class="cart-item-info" onclick="showProductDetails(${p.id})">
                <div class="cart-item-title">${p.name}</div>
                <div>₹${p.price}</div>
            </div>

            <div class="d-flex flex-column gap-1">
                <button class="btn btn-sm btn-primary"
                        onclick="moveWishlistToCart(${p.id}); event.stopPropagation();">
                    Add to Cart
                </button>

                <button class="cart-item-remove"
                        onclick="removeFromWishlist(${p.id}); event.stopPropagation();">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        `;
    });
}

function moveWishlistToCart(productId){
    const product = products.find(p=>p.id===productId);
    if(!product || !product.inStock) return;

    // Add to cart
    let item = cart.find(i=>i.id===productId);
    if(item){
        item.quantity++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.images[0],
            quantity: 1
        });
    }

    // Remove from wishlist
    wishlist = wishlist.filter(id => id !== productId);

    saveToLocalStorage();
    updateCartCount();
    updateWishlistCount();
    renderCart();
    renderWishlist();

    showNotification(product.name + " moved to cart");
}

function removeFromWishlist(productId){
    wishlist = wishlist.filter(id => id !== productId);
    saveToLocalStorage();
    updateWishlistCount();
    renderWishlist();
}
// Render when modal opens
document.getElementById('cartModal').addEventListener('show.bs.modal', renderCart);
document.getElementById('wishlistModal').addEventListener('show.bs.modal', renderWishlist);

// ================= NOTIFICATION =================
function showNotification(msg){
    const toast=document.createElement("div");
    toast.className="toast show position-fixed bottom-0 end-0 m-3";
    toast.innerHTML=`<div class="toast-body">${msg}</div>`;
    document.body.appendChild(toast);
    setTimeout(()=>toast.remove(),3000);
}

     // LOGIN
document.getElementById("loginForm").addEventListener("submit", function(e){
  e.preventDefault();

  fetch("login.php", {
    method: "POST",
    body: new FormData(this)
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById("loginMsg").innerText = data.message;

    if(data.success){
      // Redirect based on role
      window.location.href = data.redirect;
    }
  })
  .catch(err => console.error("Login error:", err));
});


// REGISTER
document.getElementById("registerForm").addEventListener("submit", function(e){
  e.preventDefault();
  fetch("register.php", { method:"POST", body:new FormData(this) })
  .then(res=>res.json())
  .then(data=>{
    document.getElementById("registerMsg").innerText=data.message;
    if(data.success){ alert("Registration successful! Please login."); }
  });
});


    document.getElementById("proceedToCheckout").addEventListener("click", function () {

    if(cart.length === 0){
        alert("Your cart is empty!");
        return;
    }

    // Create form dynamically
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "checkout.php";

    // Send cart data
    cart.forEach(item => {
        for (let key in item) {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = `cart[${item.id}][${key}]`;
            input.value = item[key];
            form.appendChild(input);
        }
    });

    document.body.appendChild(form);
    form.submit();
});


      const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("input", function () {
    const query = this.value.toLowerCase();

    const filteredProducts = products.filter(product =>
        product.name.toLowerCase().includes(query) ||
        product.description.toLowerCase().includes(query)
    );

    renderProducts(filteredProducts);
});

   /* ================= FILTER LOGIC ================= */

const categoryCheckboxes = document.querySelectorAll("[data-category]");
const typeCheckboxes = document.querySelectorAll("[data-type]");
const priceRadios = document.querySelectorAll("input[name='priceFilter']");
const stockToggle = document.getElementById("inStockToggle");
const sortSelect = document.getElementById("sortProducts");
const clearBtn = document.getElementById("clearFilters");

function applyFilters() {

    let filtered = [...products];

    /* ===== CATEGORY FILTER ===== */
    const selectedCategories = Array.from(categoryCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.dataset.category);

    if (selectedCategories.length > 0) {
        filtered = filtered.filter(p =>
            selectedCategories.includes(p.category)
        );
    }

    /* ===== TYPE FILTER ===== */
    const selectedTypes = Array.from(typeCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.dataset.type);

    if (selectedTypes.length > 0) {
        filtered = filtered.filter(p =>
            p.type && selectedTypes.some(t => p.type.includes(t))
        );
    }

    /* ===== STOCK FILTER ===== */
    if (stockToggle.checked) {
        filtered = filtered.filter(p => p.inStock);
    }

    /* ===== PRICE FILTER ===== */
    const selectedPrice = document.querySelector("input[name='priceFilter']:checked");

    if (selectedPrice) {
        const [min, max] = selectedPrice.value.split("-").map(Number);

        filtered = filtered.filter(p =>
            p.price >= min && p.price <= max
        );
    }

    /* ===== SORTING ===== */
    const sortValue = sortSelect.value;

    if (sortValue === "price-low") {
        filtered.sort((a,b)=>a.price-b.price);
    }
    else if (sortValue === "price-high") {
        filtered.sort((a,b)=>b.price-a.price);
    }
    else if (sortValue === "name") {
        filtered.sort((a,b)=>a.name.localeCompare(b.name));
    }

    renderProducts(filtered);
}

/* ===== EVENTS ===== */
categoryCheckboxes.forEach(cb => cb.addEventListener("change", applyFilters));
typeCheckboxes.forEach(cb => cb.addEventListener("change", applyFilters));
priceRadios.forEach(r => r.addEventListener("change", applyFilters));
stockToggle.addEventListener("change", applyFilters);
sortSelect.addEventListener("change", applyFilters);

/* ===== CLEAR FILTERS ===== */
clearBtn.addEventListener("click", () => {
    document.querySelectorAll(".filter-section input").forEach(i=>{
        i.checked = false;
    });

    sortSelect.value = "featured";
    renderProducts(products);
});

</script>
</body>
</html>