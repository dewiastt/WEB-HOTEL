<?php
session_start();
$page_title = "Luxury Hotel";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $page_title ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/style.css"/>
</head>
<body>

    <!-- Navbar -->
    <?php include 'include/navbar.php'; ?>

    <!-- Hero Section with Real Photo Background -->
    <section class="hero">
        <div class="hero-content">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7" data-aos="fade-right" data-aos-duration="1000">
                        <h1 class="display-2 fw-bold text-white mb-4">
                            Welcome to <span class="text-gold">Luxury</span> Hotel
                        </h1>
                        <p class="lead text-white mb-5" style="font-size: 1.3rem;">
                            Experience timeless elegance and world-class service in the heart of the Windy City.
                        </p>
                        <div>
                            <a href="rooms.php" class="btn btn-gold btn-lg px-5 py-3 me-3 shadow-lg">
                                Book Your Stay
                            </a>
                            <a href="#explore" class="btn btn-outline-light btn-lg px-5 py-3 shadow-lg">
                                Explore More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Luxury Facilities -->
    <section id="explore" class="py-5 bg-dark text-light">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-gold">Unparalleled Luxury</h2>
                <p class="lead">Every detail crafted for your comfort and delight.</p>
            </div>
            <div class="row g-5">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="facility-card text-center p-4">
                        <div class="icon-circle mb-4">
                            <i class="fas fa-concierge-bell fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">24/7 Concierge</h5>
                        <p class="text-muted">Personalized service, anytime you need it.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="facility-card text-center p-4">
                        <div class="icon-circle mb-4">
                            <i class="fas fa-swimmer fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">Rooftop Infinity Pool</h5>
                        <p class="text-muted">Breathtaking views of the Chicago skyline.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="facility-card text-center p-4">
                        <div class="icon-circle mb-4">
                            <i class="fas fa-utensils fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">Michelin-Star Dining</h5>
                        <p class="text-muted">Culinary excellence in every exquisite bite.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'include/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/script.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 1000
        });
    </script>
</body>
</html>