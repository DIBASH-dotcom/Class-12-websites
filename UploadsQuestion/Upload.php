<?php 
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../Admin_Dashboard/adminlogin.php");
    exit();
}

require '../database.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $faculty = $_POST['faculty'];
    $subject = $_POST['subject'];
    $course_type = $_POST['course_type'];
    $province = $_POST['province'];
    $year = $_POST['year'];
    $description = $_POST['description'];

    $file = $_FILES['pdf_file'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $targetDir = "./uploads/";
    
    // Create uploads directory if not exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filePath = $targetDir . basename($fileName);

    if (move_uploaded_file($fileTmp, $filePath)) {
        $stmt = $con->prepare("INSERT INTO questions (name, faculty, subject, course_type, province, year, file_path, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $name, $faculty, $subject, $course_type, $province, $year, $filePath, $description);
        $stmt->execute();
        echo "<div class='success-message'><div class='message-content'><span class='success-icon'>✅</span><p>File uploaded successfully!</p></div></div>";
    } else {
        echo "<div class='error-message'><div class='message-content'><span class='error-icon'>❌</span><p>Failed to upload file. Please try again.</p></div></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Questions - Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="robots" content="noindex, nofollow">

    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            
            --border-color: rgba(255, 255, 255, 0.1);
            
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 0;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-container img {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            object-fit: cover;
        }

        .logo-text {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary-color);
        }

        .nav-toggle {
            display: none;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            cursor: pointer;
            padding: 0.75rem;
            border-radius: var(--radius-md);
        }

        .nav-links {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links a:hover {
            background: var(--primary-color);
            color: white;
        }

        .nav-links a i {
            font-size: 1rem;
        }

        /* Main Content */
        .main-content {
            padding: 3rem 0;
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .page-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        /* Progress Steps */
        .progress-container {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .progress-steps {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--bg-secondary);
            padding: 1.5rem 2rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
        }

        .progress-step {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-lg);
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
        }

        .progress-step.active {
            background: var(--primary-color);
            color: white;
        }

        .progress-step.completed {
            background: var(--accent-color);
            color: white;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .step-label {
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* Form Styles */
        .upload-form {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 3rem;
            box-shadow: var(--shadow-lg);
            position: relative;
        }

        .upload-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-color);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .form-section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--text-muted);
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 1.25rem 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            font-size: 1rem;
            font-family: inherit;
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: var(--text-muted);
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-select {
            cursor: pointer;
        }

        /* File Upload Area */
        .file-upload-section {
            margin-top: 2rem;
        }

        .file-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: var(--radius-lg);
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            position: relative;
            background: var(--bg-tertiary);
        }

        .file-upload-area:hover {
            border-color: var(--primary-color);
        }

        .file-upload-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
        }

        .file-upload-text {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 1.125rem;
        }

        .file-upload-subtext {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .file-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-selected {
            display: none;
            background: var(--accent-color);
            color: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            margin-top: 1.5rem;
        }

        .file-selected-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .file-selected-icon {
            font-size: 1.5rem;
        }

        .file-selected-info h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .file-selected-info p {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        /* Submit Button */
        .submit-section {
            margin-top: 3rem;
            text-align: center;
        }

        .submit-btn {
            background: var(--primary-color);
            color: white;
            padding: 1.25rem 3rem;
            border: none;
            border-radius: var(--radius-lg);
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 200px;
            justify-content: center;
        }

        .submit-btn:hover {
            background: var(--secondary-color);
        }

        /* Message Styles */
        .success-message,
        .error-message {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 9999;
            max-width: 400px;
        }

        .message-content {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .success-message .message-content {
            border-left: 4px solid var(--accent-color);
        }

        .error-message .message-content {
            border-left: 4px solid var(--danger-color);
        }

        .success-icon,
        .error-icon {
            font-size: 1.5rem;
        }

        .message-content p {
            color: var(--text-primary);
            font-weight: 500;
        }

        /* Form Validation */
        .error-text {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .container {
                padding: 0 1.5rem;
            }
            
            .upload-container {
                padding: 0 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .nav-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--bg-secondary);
                flex-direction: column;
                padding: 2rem;
                border-top: 1px solid var(--border-color);
                box-shadow: var(--shadow-lg);
                gap: 1rem;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                width: 100%;
                justify-content: center;
            }

            .progress-steps {
                flex-direction: column;
                gap: 0.75rem;
                padding: 1.25rem 1.5rem;
            }

            .progress-step {
                width: 100%;
                justify-content: center;
            }

            .upload-form {
                padding: 2rem;
                margin: 1rem 0;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }

            .file-upload-area {
                padding: 2rem 1.5rem;
            }

            .file-upload-icon {
                font-size: 3rem;
            }

            .submit-btn {
                width: 100%;
                padding: 1.25rem 2rem;
            }

            .success-message,
            .error-message {
                top: 1rem;
                right: 1rem;
                left: 1rem;
                max-width: none;
            }
        }

        @media (max-width: 480px) {
            .upload-form {
                padding: 1.5rem;
                border-radius: var(--radius-lg);
            }

            .form-input,
            .form-select,
            .form-textarea {
                padding: 1rem 1.25rem;
            }

            .file-upload-area {
                padding: 1.5rem 1rem;
            }

            .section-title {
                font-size: 1.125rem;
            }
        }

        /* Focus styles */
        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus,
        .submit-btn:focus,
        .nav-links a:focus,
        .nav-toggle:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo-container">
                    <img src="../images/hello.png" alt="Student Portal Logo">
                    <span class="logo-text">Student Portal</span>
                </div>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="nav-links" id="navLinks">
                    <a href="../Admin_Dashboard/Admin_Dashboard.php">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    <a href="../UploadsQuestion/Upload.php">
                        <i class="fas fa-question-circle"></i>
                        <span>Upload Questions</span>
                    </a>
                    <a href="../UploadsSolution/UploadsSolution.php">
                        <i class="fas fa-lightbulb"></i>
                        <span>Upload Solutions</span>
                    </a>
                    <a href="../UploadNotes/Upload_Notes.php">
                        <i class="fas fa-sticky-note"></i>
                        <span>Upload Notes</span>
                    </a>
                    
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="upload-container">
            <div class="page-header">
                <h1 class="page-title">Upload Questions</h1>
                <p class="page-subtitle">Share educational content and help students excel in their studies</p>
            </div>

            <form class="upload-form" method="post" enctype="multipart/form-data" id="uploadForm">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">
                                <i class="fas fa-file-alt"></i>
                                Question Title
                            </label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="Enter descriptive title" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="faculty">
                                <i class="fas fa-graduation-cap"></i>
                                Faculty
                            </label>
                            <input type="text" id="faculty" name="faculty" class="form-input" placeholder="e.g., Science, Management, Engineering" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="subject">
                                <i class="fas fa-book"></i>
                                Subject
                            </label>
                            <input type="text" id="subject" name="subject" class="form-input" placeholder="Enter subject name" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="course_type">
                                <i class="fas fa-tags"></i>
                                Course Type
                            </label>
                            <select id="course_type" name="course_type" class="form-select" required>
                                <option value="">Select Course Type</option>
                                <option value="Technical">Technical</option>
                                <option value="Non-Technical">Non-Technical</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="province">
                                <i class="fas fa-map-marker-alt"></i>
                                Province
                            </label>
                            <input type="text" id="province" name="province" class="form-input" placeholder="Enter province" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="year">
                                <i class="fas fa-calendar"></i>
                                Year
                            </label>
                            <input type="number" id="year" name="year" class="form-input" placeholder="e.g., 2081" min="2070" max="2090" required>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label" for="description">
                                <i class="fas fa-align-left"></i>
                                Description (Optional)
                            </label>
                            <textarea id="description" name="description" class="form-textarea" placeholder="Add any additional details about the question paper..." rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="form-section file-upload-section">
                    <h2 class="section-title">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Upload PDF File
                    </h2>
                    <div class="file-upload-area" id="fileUploadArea">
                        <div class="file-upload-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="file-upload-text">Click to upload or drag and drop</div>
                        <div class="file-upload-subtext">PDF files only (Max 10MB)</div>
                        <input type="file" name="pdf_file" class="file-input" id="fileInput" accept="application/pdf" required>
                    </div>
                    <div class="file-selected" id="fileSelected">
                        <div class="file-selected-content">
                            <div class="file-selected-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="file-selected-info">
                                <h4 id="fileName"></h4>
                                <p id="fileSize"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="submit-section">
                    <button type="submit" name="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-upload"></i>
                        Upload Question
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Mobile Navigation Toggle
        const navToggle = document.getElementById('navToggle');
        const navLinks = document.getElementById('navLinks');

        navToggle.addEventListener('click', () => {
            const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', !isExpanded);
            navLinks.classList.toggle('active');
            
            // Toggle hamburger icon
            const icon = navToggle.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navToggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                const icon = navToggle.querySelector('i');
                icon.classList.add('fa-bars');
                icon.classList.remove('fa-times');
            }
        });

        // File Upload Handling
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('fileInput');
        const fileSelected = document.getElementById('fileSelected');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.style.borderColor = 'var(--primary-color)';
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.style.borderColor = 'var(--border-color)';
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.style.borderColor = 'var(--border-color)';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type === 'application/pdf') {
                    fileInput.files = files;
                    showSelectedFile(file);
                } else {
                    alert('Please select a PDF file only.');
                }
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                showSelectedFile(file);
            }
        });

        function showSelectedFile(file) {
            fileName.textContent = file.name;
            fileSize.textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`;
            fileSelected.style.display = 'block';
        }

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('.success-message, .error-message');
            messages.forEach(message => {
                message.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>