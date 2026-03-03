<?php 
// 1. CONNECT TO DATABASE
include 'db.php'; 

// 2. FETCH GLOBAL SETTINGS
$settings = [];
$s_query = $conn->query("SELECT * FROM settings");
while($r = $s_query->fetch_assoc()) { $settings[$r['site_key']] = $r['site_value']; }

// 3. PAGE ROUTING
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['lab_name'] ?? 'Al Shifa Diagnostic Lab'; ?> | Trusted Pathology Services</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-maroon: #800000;   /* Deep Maroon */
            --secondary-choc: #7B3F00;   /* Rich Chocolate */
            --accent-gold: #D4AF37;      /* Gold accent for premium feel */
            --light-cream: #fdfcfb;      /* Creamy background */
            --text-dark: #3e2723;        /* Dark Brown text */
            --text-light: #ffffff;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--light-cream); color: var(--text-dark); overflow-x: hidden; }

        /* --- UTILITIES & BUTTONS --- */
        .bg-maroon { background-color: var(--primary-maroon) !important; color: var(--text-light); }
        .text-maroon { color: var(--primary-maroon) !important; }
        .text-choc { color: var(--secondary-choc) !important; }
        .fw-boldest { font-weight: 800; }
        
        .btn-maroon {
            background-color: var(--primary-maroon); border: none; color: white; padding: 10px 30px; font-weight: 600; border-radius: 30px; transition: all 0.3s;
        }
        .btn-maroon:hover { background-color: var(--secondary-choc); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(128,0,0,0.3); }
        
        .btn-choc-outline {
            border: 2px solid var(--secondary-choc); color: var(--secondary-choc); padding: 8px 25px; font-weight: 600; border-radius: 30px; background: transparent; transition: all 0.3s;
        }
        .btn-choc-outline:hover { background-color: var(--secondary-choc); color: white; }

        /* --- TOP BAR & NAVBAR --- */
        .top-bar { background: var(--secondary-choc); color: var(--text-light); font-size: 0.9rem; padding: 8px 0; }
        .navbar { background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 15px 0; }
        .navbar-brand { font-weight: 800; font-size: 1.8rem; color: var(--primary-maroon) !important; }
        .nav-link { color: var(--text-dark) !important; font-weight: 600; margin-left: 15px; transition: color 0.3s; }
        .nav-link:hover, .nav-link.active { color: var(--primary-maroon) !important; }

        /* --- HERO SECTION --- */
        .hero-section {
            /* REPLACE WITH YOUR OWN IMAGE PATH like: url('uploads/hero-banner.jpg'); */
            background: linear-gradient(rgba(62, 39, 35, 0.7), rgba(128, 0, 0, 0.7)), url('https://healthcarentsickcare.com/cdn/shop/articles/What_are_the_Types_of_Medical_Laboratory_Tests.jpg?v=1751111203');
            background-size: cover; background-position: center; height: 75vh; display: flex; align-items: center; color: white;
        }
        .hero-content h1 { font-size: 3.5rem; font-weight: 800; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero-content p { font-size: 1.3rem; opacity: 0.9; }

        /* --- STATS COUNTER SECTION --- */
        .stats-section { background: white; padding: 60px 0; position: relative; z-index: 10; margin-top: -50px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .stat-item { text-align: center; padding: 20px; border-right: 1px solid #eee; }
        .stat-item:last-child { border-right: none; }
        .stat-icon { font-size: 3rem; color: var(--secondary-choc); margin-bottom: 15px; }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: var(--primary-maroon); display: block; }

        /* --- SECTON HEADERS --- */
        .section-header { text-align: center; margin-bottom: 50px; }
        .section-header h6 { color: var(--secondary-choc); font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .section-header h2 { font-size: 2.5rem; font-weight: 800; color: var(--primary-maroon); position: relative; display: inline-block; }
        .section-header h2::after { content: ''; position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%); width: 80px; height: 4px; background: var(--secondary-choc); border-radius: 2px; }

        /* --- THYROCARE STYLE PACKAGE CARD (CHOCOLATE/MAROON) --- */
        .package-card {
            background: #fff; border: none; border-radius: 12px; overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); transition: all 0.4s; height: 100%; position: relative;
        }
        .package-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(128,0,0,0.15); }
        .pkg-header {
            background: linear-gradient(45deg, var(--primary-maroon), var(--secondary-choc));
            color: white; padding: 20px 90px 20px 25px; position: relative; min-height: 80px; display: flex; align-items: center;
        }
        .pkg-header h4 { margin: 0; font-weight: 700; font-size: 1.2rem; }
        
        /* Hexagon Badge */
        .hexagon-badge {
            position: absolute; top: -5px; right: 20px; width: 80px; height: 85px;
            background: white; clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 10; border-top: 4px solid var(--secondary-choc);
        }
        .hex-count { font-size: 1.8rem; font-weight: 800; color: var(--primary-maroon); line-height: 1; }
        .hex-label { font-size: 0.75rem; color: var(--text-dark); font-weight: 700; text-transform: uppercase; }
        
        .pkg-body { padding: 25px; }
        .pkg-list { column-count: 2; column-gap: 20px; padding-left: 20px; margin-bottom: 25px; color: #666; font-size: 0.85rem; }
        .pkg-list li { margin-bottom: 8px; position: relative; }
        .pkg-list li::before { content: '✓'; color: var(--secondary-choc); position: absolute; left: -20px; font-weight: bold; }
        
        .pkg-price-box { background: var(--light-cream); padding: 15px; border-radius: 8px; text-align: center; }
        .old-price { text-decoration: line-through; color: #999; }
        .new-price { color: var(--primary-maroon); font-size: 1.8rem; font-weight: 800; }
        .discount-tag { background: var(--accent-gold); color: var(--text-dark); padding: 2px 10px; border-radius: 4px; font-weight: 700; font-size: 0.9rem; margin-left: 10px; }
        
        /* --- WHY CHOOSE US SECTION --- */
        .why-choose-us { background: white; padding: 80px 0; }
        .feature-box { display: flex; margin-bottom: 30px; }
        .feature-icon {
            width: 60px; height: 60px; background: var(--light-cream); border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: var(--secondary-choc); font-size: 1.8rem; margin-right: 20px; flex-shrink: 0; border: 2px solid var(--secondary-choc);
        }
        .feature-content h5 { font-weight: 700; color: var(--primary-maroon); }

        /* --- TESTIMONIALS --- */
        .testimonial-section { background: linear-gradient(to bottom, #2a1b19, #3e2723); color: white; padding: 80px 0; }
        .testimonial-card { background: rgba(255,255,255,0.05); padding: 40px; border-radius: 15px; text-align: center; border: 1px solid rgba(255,255,255,0.1); }
        .client-img { width: 80px; height: 80px; border-radius: 50%; border: 3px solid var(--accent-gold); margin: 0 auto 20px; object-fit: cover; }
        .testimonial-text { font-style: italic; font-size: 1.1rem; opacity: 0.9; }
        .client-name { color: var(--accent-gold); font-weight: 700; margin-top: 20px; display: block; }

        /* --- FOOTER --- */
        .footer { background: #2a1b19; color: #bbb; padding: 70px 0 30px; }
        .footer h5 { color: var(--accent-gold); font-weight: 700; margin-bottom: 25px; }
        .footer ul li { margin-bottom: 12px; }
        .footer ul li a { color: #bbb; text-decoration: none; transition: 0.3s; }
        .footer ul li a:hover { color: var(--accent-gold); padding-left: 5px; }
        .footer-bottom { background: #1a1110; padding: 25px 0; margin-top: 50px; text-align: center; font-size: 0.9rem; }

        /* --- MODALS --- */
        .modal-header { background: var(--primary-maroon); color: white; border-bottom: none; }
        .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }

        .text-muted {
    color: #624b00ff !important; /* Soft Chocolate Brown */
}

.text-white {  
     color: #d4af37 !important; 
    ; }


    
    </style>
</head>
<body>

    <div class="top-bar d-none d-lg-block">
        <div class="container d-flex justify-content-between">
            <div>
                <i class="fas fa-phone-alt me-2"></i> Help Line: <strong><?php echo $settings['contact_phone'] ?? ''; ?></strong> | 
                <i class="fab fa-whatsapp ms-3 me-2"></i> WhatsApp: <strong><?php echo $settings['whatsapp'] ?? ''; ?></strong>
            </div>
            <div>
                <i class="fas fa-clock me-2"></i> Mon - Sat: 7:00 AM - 8:00 PM | Sun: 8:00 AM - 2:00 PM
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-dna me-2"></i> <?php echo $settings['lab_name'] ?? 'AL SHIFA DIAGNOSTICS'; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link <?php if($page=='home') echo 'active'; ?>" href="index.php?page=home">Home</a></li>
                    <li class="nav-item"><a class="nav-link <?php if($page=='packages') echo 'active'; ?>" href="index.php?page=packages">All Packages</a></li>
                    <li class="nav-item"><a class="nav-link <?php if($page=='tests') echo 'active'; ?>" href="index.php?page=tests">Individual Tests</a></li>
                    <li class="nav-item"><a class="nav-link" href="#staff-section">Our Specialists</a></li>
                    <li class="nav-item"><a class="nav-link <?php if($page=='contact') echo 'active'; ?>" href="index.php?page=contact">Contact Us</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="https://wa.me/<?php echo $settings['whatsapp'] ?? ''; ?>" class="btn btn-maroon">
                            <i class="fas fa-calendar-check me-2"></i> Book Appointment
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <?php if($page == 'home'): ?>
        
        <header class="hero-section">
            <div class="container">
                <div class="row justify-content-start">
                    <div class="col-lg-7 hero-content">
                        <h6 class="text-warning fw-bold mb-3">WELCOME TO AL SHIFA DIAGNOSTICS</h6>
                        <h1>Precision in Every Test, <br>Care in Every Result.</h1>
                        <p class="mb-4 pe-lg-5">We use state-of-the-art technology to provide accurate and timely diagnostic services. Your health is our utmost priority.</p>
                        <div class="d-flex gap-3">
                            <a href="#popular-packages" class="btn btn-maroon btn-lg px-4">View Health Packages</a>
                            <a href="https://wa.me/<?php echo $settings['whatsapp'] ?? ''; ?>" class="btn btn-light text-maroon fw-bold btn-lg px-4"><i class="fab fa-whatsapp me-2"></i> Quick Enquiry</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="bg-dark text-white py-2">
            <div class="container d-flex align-items-center">
                <span class="badge bg-maroon me-3 rounded-0">LATEST UPDATES</span>
                <marquee onmouseover="this.stop();" onmouseout="this.start();">
                    <?php 
                    $n = $conn->query("SELECT message FROM notices WHERE is_active=1");
                    if($n->num_rows > 0) {
                        while($row = $n->fetch_assoc()) { echo '<span class="mx-4"><i class="fas fa-bullhorn text-warning me-2"></i> ' . $row['message'] . '</span> | '; }
                    } else { echo "Welcome to Al Shifa Diagnostic Lab. Contact us for home collection options."; }
                    ?>
                </marquee>
            </div>
        </div>

        <div class="container">
            <div class="stats-section">
                <div class="row g-0">
                    <div class="col-md-3 stat-item">
                        <i class="fas fa-users stat-icon"></i>
                        <span class="stat-number">50k+</span>
                        <p class="mb-0 fw-bold text-muted">Happy Patients</p>
                    </div>
                    <div class="col-md-3 stat-item">
                        <i class="fas fa-vials stat-icon"></i>
                        <span class="stat-number">1M+</span>
                        <p class="mb-0 fw-bold text-muted">Samples Tested</p>
                    </div>
                    <div class="col-md-3 stat-item">
                        <i class="fas fa-user-md stat-icon"></i>
                        <span class="stat-number">30+</span>
                        <p class="mb-0 fw-bold text-muted">Expert Doctors</p>
                    </div>
                    <div class="col-md-3 stat-item">
                        <i class="fas fa-award stat-icon"></i>
                        <span class="stat-number">15+</span>
                        <p class="mb-0 fw-bold text-muted">Years Experience</p>
                    </div>
                </div>
            </div>
        </div>

        <section class="py-5" id="popular-packages">
            <div class="container py-lg-5">
                <div class="section-header">
                    <h6>OUR BESTSELLERS</h6>
                    <h2>Popular Health Packages</h2>
                    <p class="text-muted mt-3 col-lg-6 mx-auto">Comprehensive full-body checkups designed by experts to keep track of your health parameters.</p>
                </div>

                <div class="row g-4">
                    <?php 
                    // FETCH TOP 6 PACKAGES
                    $p_query = $conn->query("SELECT * FROM packages LIMIT 6");
                    if($p_query->num_rows > 0):
                        while($row = $p_query->fetch_assoc()):
                            $tests_array = explode(',', $row['tests_included']);
                            $t_count = isset($row['test_count']) && $row['test_count'] > 0 ? $row['test_count'] : count($tests_array);
                            $disc = $row['discount_percent'] ?? 0;
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="package-card">
                            <div class="pkg-header">
                                <h4><?php echo $row['name']; ?></h4>
                                <div class="hexagon-badge">
                                    <span class="hex-count"><?php echo $t_count; ?></span>
                                    <span class="hex-label">Tests</span>
                                </div>
                            </div>
                            <div class="pkg-body">
                                <ul class="pkg-list fa-ul">
                                    <?php $i=0; foreach($tests_array as $test): if($i<8 && trim($test)!=''): ?>
                                        <li><span class="fa-li"><i class="fas fa-check text-choc"></i></span><?php echo trim($test); ?></li>
                                    <?php $i++; endif; endforeach; ?>
                                    <?php if(count($tests_array) > 8): ?><li><small class="text-muted">+ More tests</small></li><?php endif; ?>
                                </ul>
                                <div class="pkg-price-box">
                                    <div class="d-flex justify-content-center align-items-baseline mb-3">
                                        <?php if(!empty($row['original_price'])): ?><span class="old-price me-2">₹<?php echo $row['original_price']; ?></span><?php endif; ?>
                                        <span class="new-price">₹<?php echo $row['offer_price']; ?></span>
                                        <?php if($disc > 0): ?><span class="discount-tag"><?php echo $disc; ?>% OFF</span><?php endif; ?>
                                    </div>
                                    <button class="btn btn-maroon w-100 py-2" onclick="openBookModal('<?php echo addslashes($row['name']); ?>')">
                                        BOOK NOW <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                        <div class="col-12 text-center"><p>No packages currently available.</p></div>
                    <?php endif; ?>
                </div>

                <div class="text-center mt-5">
                    <a href="index.php?page=packages" class="btn btn-choc-outline btn-lg">
                        View All Packages <i class="fas fa-external-link-alt ms-2"></i>
                    </a>
                </div>
            </div>
        </section>

        <section class="why-choose-us bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5 mb-5 mb-lg-0">
                        <div style="background: url('https://media.istockphoto.com/id/2134367155/photo/scientist-analyze-biochemical-samples-in-advanced-scientific-laboratory-medical-professional.jpg?s=612x612&w=0&k=20&c=Y4pCbkw_YHosiHeYfOvwVsf9qKyfGMUKZ3LNvT1kABk=') center/cover; height: 500px; border-radius: 20px; box-shadow: 0 15px 30px rgba(0,0,0,0.1);"></div>
                    </div>
                    <div class="col-lg-7 ps-lg-5">
                        <div class="section-header text-start mb-4">
                            <h6>WHY AL SHIFA DIAGNOSTICS</h6>
                            <h2>We Ensure Accurate & Timely Results</h2>
                        </div>
                        <p class="lead text-muted mb-5">Committed to providing high-quality diagnostic services with precision technology and experienced professionals.</p>
                        
                        <div class="feature-box">
                            <div class="feature-icon"><i class="fas fa-microscope"></i></div>
                            <div class="feature-content">
                                <h5>Advanced Technology</h5>
                                <p class="text-muted mb-0">Equipped with fully automated, state-of-the-art analyzer machines for precise reporting.</p>
                            </div>
                        </div>
                        <div class="feature-box">
                            <div class="feature-icon"><i class="fas fa-home"></i></div>
                            <div class="feature-content">
                                <h5>Free Home Sample Collection</h5>
                                <p class="text-muted mb-0">Safe and hygienic sample pickup from the comfort of your home by trained phlebotomists.</p>
                            </div>
                        </div>
                        <div class="feature-box">
                            <div class="feature-icon"><i class="fas fa-file-medical-alt"></i></div>
                            <div class="feature-content">
                                <h5>Digital & Timely Reports</h5>
                                <p class="text-muted mb-0">Get your NABL guidelines compliant reports delivered via WhatsApp and Email on time.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5" id="staff-section">
            <div class="container py-lg-5">
                <div class="section-header">
                    <h6>MEET THE EXPERTS</h6>
                    <h2>Our Specialist Doctors & Staff</h2>
                </div>
                <div class="row justify-content-center">
                    <?php 
                    $staff_res = $conn->query("SELECT * FROM staff");
                    if($staff_res && $staff_res->num_rows > 0):
                        while($s = $staff_res->fetch_assoc()): 
                    ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 text-center h-100" style="background: transparent;">
                            <div style="width: 180px; height: 180px; margin: 0 auto 20px; overflow: hidden; border-radius: 50%; border: 5px solid var(--accent-gold); box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                                <img src="uploads/<?php echo $s['image']; ?>" alt="<?php echo $s['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="card-body p-0">
                                <h5 class="fw-bold text-maroon mb-1"><?php echo $s['name']; ?></h5>
                                <p class="text-choc fw-bold mb-0"><?php echo $s['designation']; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                        <p class="text-center text-muted">Staff details will be updated soon.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="testimonial-section">
            <div class="container">
                <div class="section-header text-white mb-5">
                    <h6 class="text-warning">CLIENT REVIEWS</h6>
                    <h2 class="text-white" style="border-color: var(--accent-gold);">What Our Patients Say</h2>
                </div>
                
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php 
                        // 1. Fetch Reviews from Database
                        $reviews = $conn->query("SELECT * FROM testimonials ORDER BY id DESC");
                        
                        // 2. Check if reviews exist
                        if($reviews->num_rows > 0):
                            $i = 0; // Counter to set the first slide as 'active'
                            while($row = $reviews->fetch_assoc()):
                                $active_class = ($i == 0) ? 'active' : ''; 
                        ?>
                        <div class="carousel-item <?php echo $active_class; ?>">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="testimonial-card pt-5"> <i class="fas fa-quote-left text-warning mb-3 fs-3"></i>
                                        <p class="testimonial-text mb-4">"<?php echo $row['message']; ?>"</p>
                                        
                                        <h5 class="client-name mb-1 text-white"><?php echo $row['name']; ?></h5>
                                        <small class="text-muted d-block mb-2"><?php echo $row['location']; ?></small>
                                        
                                        <div class="text-warning">
                                            <?php 
                                            $rating = (int)$row['rating'];
                                            for($s=0; $s<$rating; $s++) { echo '<i class="fas fa-star"></i>'; }
                                            for($s=$rating; $s<5; $s++) { echo '<i class="far fa-star"></i>'; } 
                                            ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            $i++; 
                            endwhile; 
                        else: 
                        ?>
                        <div class="carousel-item active">
                            <div class="text-center text-white">
                                <i class="fas fa-comment-slash fs-1 mb-3 opacity-50"></i>
                                <p class="lead">No reviews added yet. Add them from Admin Panel.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </section>

        <?php 
        $offer = $conn->query("SELECT * FROM offers WHERE is_active=1 LIMIT 1")->fetch_assoc();
        if($offer): 
        ?>
        <div class="modal fade" id="offerModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center" style="border: 4px solid var(--accent-gold);">
                    <div class="modal-header justify-content-center bg-maroon text-white">
                        <h5 class="modal-title fw-boldest"><i class="fas fa-gift me-2"></i> SPECIAL OFFER!</h5>
                        <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-5 bg-light-cream">
                        <h3 class="text-choc fw-bold mb-3"><?php echo $offer['title']; ?></h3>
                        <p class="lead mb-4"><?php echo nl2br($offer['description']); ?></p>
                        <a href="https://wa.me/<?php echo $settings['whatsapp'] ?? ''; ?>?text=I want to claim offer: <?php echo urlencode($offer['title']); ?>" class="btn btn-maroon btn-lg w-100">
                            CLAIM OFFER NOW <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <script> window.onload = function() { setTimeout(function(){ new bootstrap.Modal(document.getElementById('offerModal')).show(); }, 2000); }; </script>
        <?php endif; ?>


    <?php elseif($page == 'packages'): ?>
        <div class="container py-5 my-5">
            <div class="section-header">
                <h6>COMPREHENSIVE CARE</h6>
                <h2>All Health Packages</h2>
            </div>
            <div class="row g-4">
                <?php 
                // SHOW ALL PACKAGES (NO LIMIT)
                $p_query = $conn->query("SELECT * FROM packages");
                if($p_query->num_rows > 0):
                    while($row = $p_query->fetch_assoc()):
                        $tests_array = explode(',', $row['tests_included']);
                        $t_count = isset($row['test_count']) && $row['test_count'] > 0 ? $row['test_count'] : count($tests_array);
                        $disc = $row['discount_percent'] ?? 0;
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="package-card h-100">
                        <div class="pkg-header">
                            <h4><?php echo $row['name']; ?></h4>
                            <div class="hexagon-badge"><span class="hex-count"><?php echo $t_count; ?></span><span class="hex-label">Tests</span></div>
                        </div>
                        <div class="pkg-body d-flex flex-column h-100">
                            <ul class="pkg-list fa-ul flex-grow-1">
                                <?php foreach($tests_array as $test): if(trim($test)!=''): ?>
                                    <li><span class="fa-li"><i class="fas fa-check text-choc"></i></span><?php echo trim($test); ?></li>
                                <?php endif; endforeach; ?>
                            </ul>
                            <div class="pkg-price-box mt-auto">
                                <div class="d-flex justify-content-center align-items-baseline mb-3">
                                    <?php if(!empty($row['original_price'])): ?><span class="old-price me-2">₹<?php echo $row['original_price']; ?></span><?php endif; ?>
                                    <span class="new-price">₹<?php echo $row['offer_price']; ?></span>
                                    <?php if($disc > 0): ?><span class="discount-tag"><?php echo $disc; ?>% OFF</span><?php endif; ?>
                                </div>
                                <button class="btn btn-maroon w-100 py-2" onclick="openBookModal('<?php echo addslashes($row['name']); ?>')">BOOK NOW</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </div>

    <?php elseif($page == 'tests'): ?>
         <div class="container py-5 my-5" style="min-height: 60vh;">
            <div class="section-header">
                <h6>A-Z DIAGNOSTICS</h6>
                <h2>Individual Tests Directory</h2>
            </div>
            
            <input type="text" id="testSearch" class="form-control form-control-lg shadow-sm border-2 border-maroon mb-4 p-3" placeholder="🔍 Search test name (e.g. CBC, Lipid, Sugar)...">

            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-maroon text-white">
                        <tr>
                            <th class="py-3 ps-4">Test Name</th>
                            <th>Category</th>
                            <th>Details</th>
                            <th class="text-end pe-4">Price & Booking</th>
                        </tr>
                    </thead>
                    <tbody id="testTableBody" class="bg-white">
                        <?php $t_query = $conn->query("SELECT * FROM tests ORDER BY name ASC");
                        while($t = $t_query->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-maroon fs-5"><?php echo $t['name']; ?></td>
                            <td><span class="badge bg-choc text-white rounded-pill px-3"><?php echo $t['category']; ?></span></td>
                            <td><small class="text-muted"><i class="fas fa-vial me-1"></i> <?php echo $t['sample_type']; ?> | <i class="fas fa-clock me-1"></i> Fasting: <?php echo $t['fasting']; ?></small></td>
                            <td class="text-end pe-4">
                                <span class="fw-boldest text-maroon fs-5 me-3">₹<?php echo $t['price']; ?></span>
                                <button class="btn btn-sm btn-maroon" onclick="openBookModal('Test: <?php echo addslashes($t['name']); ?>')">Book</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
         </div>
         <script>
             document.getElementById('testSearch').addEventListener('keyup', function() {
                 let filter = this.value.toUpperCase();
                 let rows = document.getElementById('testTableBody').getElementsByTagName('tr');
                 for (let i = 0; i < rows.length; i++) {
                     let text = rows[i].getElementsByTagName('td')[0].textContent || rows[i].innerText;
                     rows[i].style.display = text.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                 }
             });
         </script>

    <?php elseif($page == 'contact'): ?>
        <div class="container py-5 my-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <div class="section-header text-start">
                        <h6>GET IN TOUCH</h6>
                        <h2>We're Here To Help</h2>
                    </div>
                    <p class="lead text-muted mb-5">Have queries about tests, packages, or home collection? Reach out to us anytime.</p>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 btn-maroon p-3 rounded-circle" style="width:60px;height:60px;display:flex;justify-content:center;align-items:center;"><i class="fas fa-map-marker-alt fs-3"></i></div>
                        <div class="ms-4">
                            <h5 class="fw-bold text-maroon">Main Center Address</h5>
                            <p class="text-muted"><?php echo nl2br($settings['address'] ?? 'Address not updated'); ?></p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 btn-maroon p-3 rounded-circle" style="width:60px;height:60px;display:flex;justify-content:center;align-items:center;"><i class="fas fa-phone-alt fs-3"></i></div>
                        <div class="ms-4">
                            <h5 class="fw-bold text-maroon">Call Us / WhatsApp</h5>
                            <p class="text-muted mb-0 fw-bold fs-5"><?php echo $settings['contact_phone'] ?? ''; ?></p>
                            <p class="text-success mb-0"><i class="fab fa-whatsapp"></i> <?php echo $settings['whatsapp'] ?? ''; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="ratio ratio-4x3 rounded-4 shadow-lg overflow-hidden border-5 border-maroon">
                         <iframe src="https://maps.google.com/maps?q=<?php echo urlencode(strip_tags($settings['address'] ?? 'New Delhi, India')); ?>&t=&z=14&ie=UTF8&iwloc=&output=embed" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>


    <footer class="footer">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-4 col-md-6">
                    <h3 class="text-white fw-bold mb-4">
                        <i class="fas fa-dna me-2 text-warning"></i> <?php echo $settings['lab_name'] ?? 'AL SHIFA LAB'; ?>
                    </h3>
                    <p class="mb-4">Your trusted partner in diagnostic healthcare. Providing accurate results with advanced technology and compassionate care.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-light rounded-circle p-2" style="width:40px;height:40px;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-light rounded-circle p-2" style="width:40px;height:40px;"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/<?php echo $settings['whatsapp'] ?? ''; ?>" class="btn btn-outline-light rounded-circle p-2" style="width:40px;height:40px;"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-6">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php?page=home">Home</a></li>
                        <li><a href="index.php?page=packages">Health Packages</a></li>
                        <li><a href="index.php?page=tests">Test Menu</a></li>
                        <li><a href="index.php?page=contact">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-6">
                    <h5>Services</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Home Collection</a></li>
                        <li><a href="#">Corporate Wellness</a></li>
                        <li><a href="#">Full Body Checkups</a></li>
                        <li><a href="#">Specialized Tests</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled text-muted fa-ul">
                        <li class="mb-3"><span class="fa-li"><i class="fas fa-map-marker-alt text-warning"></i></span> <?php echo strip_tags($settings['address'] ?? ''); ?></li>
                        <li class="mb-3"><span class="fa-li"><i class="fas fa-phone-alt text-warning"></i></span> <?php echo $settings['contact_phone'] ?? ''; ?></li>
                        <li class="mb-3"><span class="fa-li"><i class="fas fa-envelope text-warning"></i></span> info@alshifalab.com</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom px-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        © 2025 <strong><?php echo $settings['lab_name'] ?? ''; ?></strong>. All Rights Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <small>ISO 9001:2015 Certified Lab</small>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-maroon text-white">
                    <h5 class="modal-title fw-bold"><i class="fab fa-whatsapp me-2"></i> Book via WhatsApp</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 p-lg-5 bg-light-cream">
                    <form id="waForm" onsubmit="sendToWhatsApp(event)">
                        <div class="mb-4">
                            <label class="fw-bold text-maroon small text-uppercase mb-1">Selected Service</label>
                            <input type="text" id="displayPkgName" class="form-control form-control-lg bg-white fw-boldest text-choc border-0 shadow-sm" readonly>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold small">Patient Name *</label>
                                <input type="text" id="waName" class="form-control" required placeholder="Full Name">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Mobile Number *</label>
                                <input type="tel" id="waPhone" class="form-control" required placeholder="10-digit number">
                            </div>
                        </div>
                        <div class="mb-4 p-3 bg-white rounded border-start border-4 border-maroon shadow-sm">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="homeCollectCheck" onchange="toggleAddress()">
                                <label class="form-check-label fw-bold text-maroon" for="homeCollectCheck">Need Home Sample Collection?</label>
                            </div>
                            <div class="mt-3" id="addressBox" style="display:none;">
                                <label class="fw-bold small">Complete Pickup Address *</label>
                                <textarea id="waAddress" class="form-control" rows="2" placeholder="House No, Floor, Landmark..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold py-3 fs-5 rounded-pill shadow">
                            <i class="fab fa-whatsapp me-2"></i> SEND ENQUIRY NOW
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Open Booking Modal
        function openBookModal(pkgName) {
            document.getElementById('displayPkgName').value = pkgName;
            document.getElementById('waForm').reset();
            document.getElementById('addressBox').style.display = 'none';
            document.getElementById('waAddress').removeAttribute('required');
            new bootstrap.Modal(document.getElementById('bookingModal')).show();
        }

        // Toggle Address Field
        function toggleAddress() {
            const isChecked = document.getElementById('homeCollectCheck').checked;
            const addrBox = document.getElementById('addressBox');
            const addrInput = document.getElementById('waAddress');
            addrBox.style.display = isChecked ? 'block' : 'none';
            isChecked ? addrInput.setAttribute('required', 'required') : addrInput.removeAttribute('required');
        }

        // Send to WhatsApp
        function sendToWhatsApp(e) {
            e.preventDefault();
            const adminPhone = "<?php echo $settings['whatsapp'] ?? ''; ?>";
            if(adminPhone === '') { alert('Admin WhatsApp number not configured in settings.'); return; }
            
            const pkg = document.getElementById('displayPkgName').value;
            const name = document.getElementById('waName').value;
            const phone = document.getElementById('waPhone').value;
            const isHome = document.getElementById('homeCollectCheck').checked;
            
            let msg = `👋 *New Booking Enquiry @ Al Shifa Lab*\n--------------------------------\n`;
            msg += `🔬 *Service:* ${pkg}\n👤 *Patient:* ${name}\n📞 *Phone:* ${phone}\n`;
            if(isHome) {
                msg += `🏠 *Type:* Home Collection Req.\n📍 *Address:* ${document.getElementById('waAddress').value}\n`;
            } else {
                msg += `🏥 *Type:* Lab Visit\n`;
            }
            msg += `--------------------------------\nPlease confirm availability & pricing.`;
            
            window.open(`https://wa.me/${adminPhone}?text=${encodeURIComponent(msg)}`, '_blank');
        }
    </script>
</body>
</html>