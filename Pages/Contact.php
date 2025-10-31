<?php
require '../database.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Class 12 Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="canonical" href="https://dibashmagar123.com.np/Pages/Contact.php" />

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #3730a3;
            --secondary: #64748b;
            --accent: #06b6d4;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
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
            flex-wrap: wrap;
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

        .nav-links {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: var(--text-primary);
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius);
            font-weight: 500;
            transition: var(--transition);
            white-space: nowrap;
        }

        /* Enhanced hover effect for navigation */
        .nav-links a:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius);
            transition: var(--transition);
        }

        .mobile-menu-btn:hover {
            background: rgba(79, 70, 229, 0.1);
            transform: scale(1.1);
        }

        .mobile-menu-btn i {
            transition: var(--transition);
        }

        .mobile-menu-btn.active i {
            transform: rotate(90deg);
        }

        /* Main Content */
        .main-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 1rem;
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 700;
            color: var(--white);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Contact Form */
        .contact-form {
            background: var(--white);
            border-radius: var(--radius);
            padding: 3rem;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .contact-form:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .form-title {
            font-size: 2rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .form-description {
            text-align: center;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            font-family: inherit;
            transition: var(--transition);
            background: var(--white);
        }

        /* Enhanced hover and focus effects for form inputs */
        .form-input:hover,
        .form-textarea:hover {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
            transform: translateY(-1px);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-button {
            width: 100%;
            padding: 1rem 2rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        /* Enhanced hover effect for submit button */
        .submit-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .submit-button:disabled {
            background: var(--secondary);
            cursor: not-allowed;
            transform: none;
        }

        /* Loading state */
        .submit-button.loading {
            background: var(--secondary);
            cursor: not-allowed;
        }

        .submit-button.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid var(--white);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Success/Error Messages */
        .message {
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            font-weight: 500;
            display: none;
        }

        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .message.error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .message.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Thank You Section */
        .thank-you {
            text-align: center;
            margin: 3rem 0;
            padding: 2rem;
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
            font-size: 1.25rem;
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

        /* Contact Info Cards */
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }

        .info-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .info-card i {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .info-card:hover i {
            transform: scale(1.1);
            color: var(--success);
        }

        .info-card h3 {
            color: var(--white);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .info-card p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
                position: relative;
            }

            .mobile-menu-btn {
                display: block;
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
            }

            .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
                background: rgba(255, 255, 255, 0.98);
                border-radius: var(--radius);
                padding: 1rem;
                margin-top: 1rem;
                box-shadow: var(--shadow);
                animation: slideDown 0.3s ease;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                padding: 0.75rem 1rem;
                text-align: center;
                border-radius: var(--radius);
                margin-bottom: 0.5rem;
            }

            .nav-links a:last-child {
                margin-bottom: 0;
            }

            .main-container {
                padding: 1rem;
            }

            .contact-form {
                padding: 2rem 1.5rem;
            }

            .hero-section {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.75rem;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .contact-form {
                padding: 1.5rem 1rem;
            }
        }

        /* Focus styles for accessibility */
        *:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* Reduced motion support */
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
            
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle navigation menu">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            
            <div class="nav-links" id="navLinks">
                <a href="https://dibashmagar123.com.np/">Home</a>
    <a href="https://dibashmagar123.com.np/UploadsQuestion/province.php">Questions</a>
    <a href="https://dibashmagar123.com.np/UploadsSolution/province.php">Solutions</a>
    <a href="https://dibashmagar123.com.np/UploadNotes/View_Notes.php">Notes</a>
    <a href="https://dibashmagar123.com.np/Pages/About.php">About Us</a>
    <a href="https://dibashmagar123.com.np/Pages/Contact.php">Contact Us</a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <section class="hero-section">
            <h1 class="hero-title">Get in Touch</h1>
            <p class="hero-subtitle">
                We're here to help! Share your questions, answers, notes, or report any issues you encounter.
            </p>
        </section>

        <div class="contact-info">
            <div class="info-card">
                <i class="fas fa-question-circle"></i>
                <h3>Questions & Answers</h3>
                <p>Share your academic questions or provide answers to help fellow students</p>
            </div>
            <div class="info-card">
                <i class="fas fa-sticky-note"></i>
                <h3>Study Notes</h3>
                <p>Contribute your study notes or request specific topics you need help with</p>
            </div>
            <div class="info-card">
                <i class="fas fa-bug"></i>
                <h3>Report Issues</h3>
                <p>Found a bug or facing technical difficulties? Let us know so we can fix it</p>
            </div>
        </div>

        <div class="contact-form">
            <h2 class="form-title">Contact Us</h2>
            <p class="form-description">
                If you have any <strong>questions</strong>, <strong>answers</strong>, or <strong>notes</strong> to share,<br>
                or if you're facing any issues — please feel free to contact us anytime!
            </p>
            
            <div id="messageContainer"></div>
            
            <form action="https://formspree.io/f/mrbkodne" method="POST" id="contactForm">
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i> Name
                    </label>
                    <input type="text" id="name" name="name" class="form-input" required placeholder="Enter your full name">
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your email address">
                </div>
                <div class="form-group">
                    <label for="subject" class="form-label">
                        <i class="fas fa-tag"></i> Subject
                    </label>
                    <input type="text" id="subject" name="subject" class="form-input" required placeholder="What is this about?">
                </div>
                <div class="form-group">
                    <label for="message" class="form-label">
                        <i class="fas fa-comment"></i> Message
                    </label>
                    <textarea id="message" name="message" class="form-textarea" rows="5" required placeholder="Tell us more about your question, suggestion, or issue..."></textarea>
                </div>
                <button type="submit" class="submit-button" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>

        <div class="thank-you">
            <p>
                <i class="fas fa-heart" style="color: #ef4444; margin-right: 0.5rem;"></i>
                Thank you for being part of our educational community!
            </p>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Class 12 Portal. All rights reserved. Developed with ❤️ by Dibash Magar.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const navLinks = document.getElementById('navLinks');

            mobileMenuBtn.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                mobileMenuBtn.classList.toggle('active');
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!mobileMenuBtn.contains(e.target) && !navLinks.contains(e.target)) {
                    navLinks.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                }
            });

            // Close mobile menu when window is resized to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    navLinks.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                }
            });

            // Existing form functionality
            const form = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitBtn');
            const messageContainer = document.getElementById('messageContainer');

            // Form validation
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', validateField);
                input.addEventListener('input', clearErrors);
            });

            function validateField(e) {
                const field = e.target;
                const value = field.value.trim();
                
                // Remove existing error styling
                field.classList.remove('error');
                
                if (field.hasAttribute('required') && !value) {
                    showFieldError(field, 'This field is required');
                    return false;
                }
                
                if (field.type === 'email' && value && !isValidEmail(value)) {
                    showFieldError(field, 'Please enter a valid email address');
                    return false;
                }
                
                return true;
            }

            function showFieldError(field, message) {
                field.classList.add('error');
                field.style.borderColor = 'var(--danger)';
                
                // Remove error styling after 3 seconds
                setTimeout(() => {
                    field.classList.remove('error');
                    field.style.borderColor = '';
                }, 3000);
            }

            function clearErrors(e) {
                const field = e.target;
                field.classList.remove('error');
                field.style.borderColor = '';
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function showMessage(message, type) {
                messageContainer.innerHTML = `
                    <div class="message ${type} show">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        ${message}
                    </div>
                `;
                
                // Auto-hide message after 5 seconds
                setTimeout(() => {
                    const messageEl = messageContainer.querySelector('.message');
                    if (messageEl) {
                        messageEl.classList.remove('show');
                        setTimeout(() => {
                            messageContainer.innerHTML = '';
                        }, 300);
                    }
                }, 5000);
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate all fields
                let isValid = true;
                inputs.forEach(input => {
                    if (!validateField({ target: input })) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    showMessage('Please fix the errors above before submitting.', 'error');
                    return;
                }
                
                // Show loading state
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span style="opacity: 0;">Sending...</span>';
                
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: form.method,
                    body: formData,
                    headers: { Accept: "application/json" }
                })
                .then(response => {
                    if (response.ok) {
                        showMessage('Thank you! Your message has been sent successfully. We\'ll get back to you soon!', 'success');
                        form.reset();
                        
                        // Add success animation to form
                        form.style.transform = 'scale(0.98)';
                        setTimeout(() => {
                            form.style.transform = '';
                        }, 200);
                    } else {
                        throw new Error('Network response was not ok');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Oops! Something went wrong. Please try again or contact us directly.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
                });
            });

            // Add floating label effect
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
                
                // Check if field has value on load
                if (input.value) {
                    input.parentElement.classList.add('focused');
                }
            });

            console.log('✨ Contact form loaded successfully!');
        });
    </script>
</body>
</html>