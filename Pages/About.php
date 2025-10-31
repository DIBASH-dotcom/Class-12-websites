<?php
require '../database.php';
$contributors = [
    [
        'name' => 'Dibash Magar',
        'role' => 'Developer',
        'image' => '../images/dibash.png'
    ],
    [
        'name' => 'Kumar Shan',
        'role' => 'Collector - Management Section Papers',
        'image' => '../images/Edusphere.png'
    ],
    [
        'name' => 'Sussan Gyawali',
        'role' => 'Collector - Education Section Papers',
        'image' => '../images/Cakes.png'
    ],
];

// Get current page URL for canonical
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') 
               . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Class 12 Portal</title>
    <link rel="canonical" href="<?= htmlspecialchars($current_url, ENT_QUOTES, 'UTF-8'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* All your existing CSS copied exactly from your previous file */
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --secondary: #64748b;
            --accent: #06b6d4;
            --success: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --white: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --radius: 0.75rem;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-container img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 0.5rem;
        }

        .nav-item a {
            color: var(--text-primary);
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-item a:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-primary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius);
            transition: var(--transition);
        }

        .mobile-menu-toggle:hover {
            background: var(--light);
            transform: scale(1.1);
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero-section {
            text-align: center;
            padding: 4rem 1rem;
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--white);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .cta-button {
            display: inline-block;
            background: var(--accent);
            color: var(--white);
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: var(--radius);
            font-weight: 600;
            margin: 1rem 0.5rem;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .cta-button:hover {
            background: var(--success);
            transform: translateY(-3px) scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        .hero-description {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 2rem 0;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            margin: 3rem 0 2rem;
            color: var(--white);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Contributors Section */
        .contributors-wrapper {
            display: grid;
            grid-template-columns: 1fr auto 300px;
            gap: 3rem;
            align-items: start;
            margin: 3rem 0;
        }

        .contributors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .contributor-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--shadow);
            border: 2px solid transparent;
        }

        .contributor-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .contributor-card:hover .contributor-avatar {
            transform: scale(1.1);
            border-color: var(--accent);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        }

        .contributor-card:hover .contributor-name {
            color: var(--primary);
        }

        .contributor-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1.5rem;
            border: 3px solid var(--border);
            transition: var(--transition);
        }

        .contributor-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }

        .contributor-role {
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 500;
        }

        /* Vertical Separator */
        .vertical-separator {
            width: 2px;
            background: linear-gradient(to bottom, transparent, var(--primary), transparent);
            align-self: stretch;
            min-height: 200px;
        }

        /* QR Section */
        .qr-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .qr-section:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .qr-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .qr-code {
            width: 180px;
            height: 180px;
            border-radius: var(--radius);
            transition: var(--transition);
            cursor: pointer;
            border: 3px solid var(--border);
        }

        .qr-code:hover {
            transform: scale(1.1);
            border-color: var(--primary);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        }

        .more-options {
            margin-top: 1.5rem;
            padding: 1rem;
            background: var(--primary);
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            color: var(--white);
            font-weight: 500;
        }

        .more-options:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .more-options:hover i {
            transform: scale(1.2);
            color: #fbbf24;
        }

        .more-options i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
            transition: var(--transition);
        }

        /* Thank You Section */
        .thank-you {
            text-align: center;
            margin: 4rem 0;
            padding: 3rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }

        .thank-you:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .thank-you p {
            font-size: 1.5rem;
            color: var(--white);
            font-weight: 500;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: var(--white);
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .contributors-wrapper {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .vertical-separator {
                display: none;
            }

            .qr-section {
                max-width: 400px;
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
            .nav-container {
                padding: 0 1rem;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 1rem;
                box-shadow: var(--shadow-lg);
                border-radius: 0 0 var(--radius) var(--radius);
            }

            .nav-menu.active {
                display: flex;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .container {
                padding: 1rem;
            }

            .hero-section {
                padding: 2rem 1rem;
            }

            .contributors-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .contributor-card {
                padding: 1.5rem;
            }

            .contributor-avatar {
                width: 100px;
                height: 100px;
            }

            .qr-code {
                width: 150px;
                height: 150px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }

            .cta-button {
                padding: 0.75rem 1.5rem;
                font-size: 0.9rem;
                display: block;
                margin: 1rem auto;
                max-width: 250px;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        *:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-container">
                <img src="../images/hello.png" alt="Class 12 Portal Logo">
                <span class="logo-text">Class 12 Portal</span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li class="nav-item"><a href="https://dibashmagar123.com.np/">Home</a></li>
                <li class="nav-item"><a href="https://dibashmagar123.com.np/UploadsQuestion/province.php">Questions</a></li>
                <li class="nav-item"><a href="https://dibashmagar123.com.np/UploadsSolution/province.php">Solutions</a></li>
                <li class="nav-item"><a href="https://dibashmagar123.com.np/UploadNotes/View_Notes.php">Notes</a></li>
                <li class="nav-item"><a href="https://dibashmagar123.com.np/Pages/About.php">About Us</a></li>
                <li class="nav-item"><a href="https://dibashmagar123.com.np/Pages/Contact.php">Contact</a></li>
            </ul>
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <div class="container">
        <section class="hero-section">
            <h1 class="hero-title">Thanks for Using Our Platform!</h1>
            <p class="hero-description">
                Empowering Class 12 students with comprehensive study materials, 
                practice questions, and expert solutions to excel in their academic journey.
            </p>
            <a href="https://dibashmagar123.com.np/Pages/Contact.php" class="cta-button">
                <i class="fas fa-paper-plane"></i> Share Your Feedback
            </a>
        </section>

        <h2 class="section-title">Meet Our Amazing Contributors</h2>

        <div class="contributors-wrapper">
            <div class="contributors-grid">
                <?php foreach ($contributors as $contributor): ?>
                    <div class="contributor-card">
                        <img src="<?= htmlspecialchars($contributor['image']) ?>" 
                             alt="<?= htmlspecialchars($contributor['name']) ?>" 
                             class="contributor-avatar">
                        <h3 class="contributor-name"><?= htmlspecialchars($contributor['name']) ?></h3>
                        <p class="contributor-role"><?= htmlspecialchars($contributor['role']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="vertical-separator" aria-hidden="true"></div>

            <div class="qr-section">
                <h3 class="qr-title">Support Our Mission</h3>
                <img src="../images/scanner.png" alt="Support QR Code" class="qr-code">
                <div class="more-options" role="button" tabindex="0" aria-label="More support options">
                    <i class="fas fa-heart" aria-hidden="true"></i>
                    <span>More Ways to Help</span>
                </div>
            </div>
        </div>

        <div class="thank-you">
            <p>
                <i class="fas fa-heart" style="color: #ef4444; margin-right: 0.5rem;"></i>
                Thank you for being part of our educational community!
            </p>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Class 12 Portal. All rights reserved. Developed with ❤️ by Dibash Magar.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const navMenu = document.getElementById('navMenu');
            const menuIcon = mobileMenuToggle.querySelector('i');

            mobileMenuToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
                if (navMenu.classList.contains('active')) {
                    menuIcon.classList.remove('fa-bars');
                    menuIcon.classList.add('fa-times');
                } else {
                    menuIcon.classList.remove('fa-times');
                    menuIcon.classList.add('fa-bars');
                }
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.navbar')) {
                    navMenu.classList.remove('active');
                    menuIcon.classList.remove('fa-times');
                    menuIcon.classList.add('fa-bars');
                }
            });

            const moreOptions = document.querySelector('.more-options');
            if (moreOptions) {
                moreOptions.addEventListener('click', function() {
                    alert('Thank you for your interest in supporting us! You can help by:\n\n• Sharing our platform with friends\n• Providing feedback and suggestions\n• Contributing study materials\n• Reporting any issues you find');
                });

                moreOptions.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            }

            console.log('✨ Class 12 Portal About Page loaded successfully!');
        });
    </script>
</body>
</html>
