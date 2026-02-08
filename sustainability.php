<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sustainability Guide | Crochet Haven</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8faf9;
            font-family: "Segoe UI", sans-serif;
        }

        .hero {
            background: linear-gradient(135deg, #a8e6cf, #dcedc1);
            padding: 60px 20px;
            text-align: center;
            border-radius: 0 0 40px 40px;
        }

        .hero h1 {
            font-weight: 700;
        }

        .section {
            padding: 50px 0;
        }

        .icon-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .icon-box i {
            font-size: 36px;
            color: #4caf50;
            margin-bottom: 15px;
        }

        .highlight {
            background: #e8f5e9;
            padding: 40px;
            border-radius: 16px;
        }

        .badge-green {
            background: #4caf50;
        }

        footer {
            background: #222;
            color: #ccc;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- HERO -->
<section class="hero">
    <h1>🌿 Sustainability Guide</h1>
    <p class="mt-3 fs-5">
        Learn how your choices support the planet, handmade art, and ethical living.
    </p>
</section>

<!-- WHAT IS SUSTAINABLE CROCHET -->
<section class="section container">
    <h3 class="text-center mb-4">What Is Sustainable Crochet?</h3>
    <p class="text-center">
        Sustainable crochet focuses on mindful creation, slow fashion, and eco-friendly materials.
        Each handmade piece is created with care, reducing waste and promoting long-lasting use.
    </p>
</section>

<!-- MATERIALS -->
<section class="section bg-light">
    <div class="container">
        <h3 class="text-center mb-5">🌱 Materials We Use</h3>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="icon-box">
                    <i class="fas fa-leaf"></i>
                    <h5>Eco-Friendly Yarn</h5>
                    <p>We prefer cotton and low-impact fibers that are gentle on nature.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="icon-box">
                    <i class="fas fa-recycle"></i>
                    <h5>Reusable Packaging</h5>
                    <p>Minimal and recyclable packaging to reduce waste.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="icon-box">
                    <i class="fas fa-hand-holding-heart"></i>
                    <h5>Handmade With Care</h5>
                    <p>No factories. Every product is crafted with patience and love.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WHY HANDMADE -->
<section class="section container">
    <h3 class="text-center mb-4">❤️ Why Handmade Matters</h3>
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <span class="badge badge-green p-2">Supports Artisans</span>
        </div>
        <div class="col-md-3 mb-3">
            <span class="badge badge-green p-2">Reduces Fast Fashion</span>
        </div>
        <div class="col-md-3 mb-3">
            <span class="badge badge-green p-2">Each Piece Is Unique</span>
        </div>
        <div class="col-md-3 mb-3">
            <span class="badge badge-green p-2">Long-Lasting Quality</span>
        </div>
    </div>
</section>

<!-- CARE GUIDE -->
<section class="section bg-light">
    <div class="container">
        <h3 class="text-center mb-4">🧼 How to Care for Your Crochet Items</h3>
        <ul class="list-group list-group-flush fs-5">
            <li class="list-group-item">✔ Hand wash gently in cold water</li>
            <li class="list-group-item">✔ Use mild detergent only</li>
            <li class="list-group-item">✔ Do not wring or twist</li>
            <li class="list-group-item">✔ Dry flat in shade</li>
            <li class="list-group-item">✔ Store folded to keep shape</li>
        </ul>
    </div>
</section>

<!-- HOW USERS CAN HELP -->
<section class="section container">
    <h3 class="text-center mb-4">🌍 How You Can Help the Planet</h3>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="icon-box">
                <i class="fas fa-seedling"></i>
                <p>Buy consciously and only what you truly need.</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="icon-box">
                <i class="fas fa-recycle"></i>
                <p>Reuse and repair instead of throwing away.</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="icon-box">
                <i class="fas fa-heart"></i>
                <p>Support handmade & small businesses.</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="icon-box">
                <i class="fas fa-globe"></i>
                <p>Choose quality over quantity for a better future.</p>
            </div>
        </div>
    </div>
</section>

<!-- BRAND PROMISE -->
<section class="section highlight container text-center">
    <h3>✨ Our Promise</h3>
    <p class="fs-5 mt-3">
        At <strong>Crochet Haven</strong>, we believe in slow fashion, ethical creation,
        and meaningful handmade products that respect people and the planet.
    </p>
</section>

<footer>
    <p>© <?= date('Y') ?> Crochet Haven · Made with ♡ for a greener future</p>
</footer>

</body>
</html>
