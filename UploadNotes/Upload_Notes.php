<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../Admin_Dashboard/adminlogin.php");
    exit();
}


require '../database.php'; // Adjust the path if needed

// Handle upload
$message = "";
if (isset($_POST['upload'])) {
    // Collect form data
    $name        = $_POST['name'];
    $faculty     = $_POST['faculty'];
    $subject     = $_POST['subject'];
    $course_type = $_POST['course_type'];
    $province    = $_POST['province'];
    $year        = $_POST['year'];
    $description = $_POST['description'];

    // File data
    $file_name = $_FILES['note_file']['name'];
    $file_tmp  = $_FILES['note_file']['tmp_name'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_path = "uploads/" . basename($file_name);

    // Allowed extensions
    $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];
    if (!in_array($file_ext, $allowed_extensions)) {
        $message = "❌ Only PDF, DOC, DOCX, PPT, PPTX or TXT files are allowed.";
    } else {
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert into DB
            $sql = "INSERT INTO notes (name, faculty, subject, course_type, province, year, file_path, description)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssssssss", $name, $faculty, $subject, $course_type, $province, $year, $file_path, $description);

            if ($stmt->execute()) {
                $message = "✅ Note uploaded and saved successfully!";
            } else {
                $message = "❌ Database error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "❌ Failed to upload the file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">

    <title>Upload Notes - Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Header Styles */
        #header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-container img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }

        .nav-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            color: #4a5568;
        }

        .nav-toggle:hover {
            background: #f7fafc;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .nav-links a:hover {
            background: #edf2f7;
            color: #2d3748;
        }

        /* Main Content */
        .main-content {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .page-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }

        /* Form Container */
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .upload-form {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-grid {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            background: #f8fafc;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            position: absolute;
            left: -9999px;
            opacity: 0;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px;
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            font-weight: 500;
            color: #4a5568;
        }

        .file-input-label:hover {
            border-color: #667eea;
            background: #edf2f7;
        }

        .file-icon {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            fill: none;
        }

        .file-types {
            font-size: 0.8rem;
            color: #718096;
            margin-top: 0.5rem;
            text-align: center;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.2s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: 500;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .alert-success {
            background: #f0fff4;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }

        /* Two Column Layout for larger screens */
        @media (min-width: 768px) {
            .form-grid-two {
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }
        }

        /* Mobile Responsive */
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
                background: white;
                flex-direction: column;
                padding: 1rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                gap: 0;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                padding: 12px 16px;
                width: 100%;
                text-align: center;
                border-bottom: 1px solid #e2e8f0;
            }

            .nav-links a:last-child {
                border-bottom: none;
            }

            .page-title {
                font-size: 2rem;
            }

            .upload-form {
                padding: 1.5rem;
                margin: 0 10px;
            }

            .container {
                padding: 0 15px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.75rem;
            }

            .upload-form {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <header id="header">
        <div class="container">
            <nav>
                <div class="logo-container">
                    <img src="../images/hello.png" alt="Student Portal Logo">
                    <span class="logo-text">Admin Dashboard</span>
                </div>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                    <svg class="file-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                
                <div class="nav-links" id="navLinks">
                    <a href="../Admin_Dashboard/Admin_Dashboard.php">Home</a>
                    <a href="../UploadsQuestion/Upload.php">Upload Questions</a>
                    <a href="../UploadsSolution/UploadsSolution.php">Upload Solutions</a>
                    <a href="../UploadNotes/Upload_Notes.php">Upload Notes</a>
                    
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">Upload Notes</h1>
                <p class="page-subtitle">Share educational notes and study materials</p>
            </div>

            <div class="form-container">
                <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
                    <?php if ($message): ?>
                        <div class="alert <?php echo (strpos($message, '✅') !== false) ? 'alert-success' : 'alert-error'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">Note Title</label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="Enter note title" required>
                        </div>

                        <div class="form-grid form-grid-two">
                            <div class="form-group">
                                <label class="form-label" for="faculty">Faculty</label>
                                <input type="text" id="faculty" name="faculty" class="form-input" placeholder="e.g., Science, Management" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-input" placeholder="Enter subject name" required>
                            </div>
                        </div>

                        <div class="form-grid form-grid-two">
                            <div class="form-group">
                                <label class="form-label" for="course_type">Course Type</label>
                                <select id="course_type" name="course_type" class="form-select" required>
                                    <option value="">Select Course Type</option>
                                    <option value="Technical">Technical</option>
                                    <option value="Non-Technical">Non-Technical</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="province">Province</label>
                                <input type="text" id="province" name="province" class="form-input" placeholder="Enter province" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="year">Year</label>
                            <input type="number" id="year" name="year" min="1" class="form-input" placeholder="e.g., 2081" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Note File</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="note_file" name="note_file" class="file-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required>
                                <label for="note_file" class="file-input-label">
                                    <svg class="file-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14,2 14,8 20,8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10,9 9,9 8,9"></polyline>
                                    </svg>
                                    <span id="file-label">Choose file or drag and drop</span>
                                </label>
                            </div>
                            <div class="file-types">
                                Supported formats: PDF, DOC, DOCX, PPT, PPTX, TXT
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" class="form-textarea" placeholder="Optional description about the notes" rows="4"></textarea>
                        </div>

                        <button type="submit" name="upload" class="submit-btn">
                            Upload Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Mobile navigation toggle
        document.getElementById('navToggle').addEventListener('click', function() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        });

        // File input enhancement
        const fileInput = document.getElementById('note_file');
        const fileLabel = document.getElementById('file-label');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                const fileName = this.files[0].name;
                const fileExt = fileName.split('.').pop().toLowerCase();
                
                // Show file type icon based on extension
                let fileTypeText = '';
                switch(fileExt) {
                    case 'pdf':
                        fileTypeText = '📄 ';
                        break;
                    case 'doc':
                    case 'docx':
                        fileTypeText = '📝 ';
                        break;
                    case 'ppt':
                    case 'pptx':
                        fileTypeText = '📊 ';
                        break;
                    case 'txt':
                        fileTypeText = '📃 ';
                        break;
                    default:
                        fileTypeText = '📁 ';
                }
                
                fileLabel.textContent = fileTypeText + fileName;
            } else {
                fileLabel.textContent = 'Choose file or drag and drop';
            }
        });

        // Form validation enhancement
        const form = document.querySelector('.upload-form');
        const inputs = form.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.style.borderColor = '#e53e3e';
                } else {
                    this.style.borderColor = '#e2e8f0';
                }
            });

            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.style.borderColor = '#48bb78';
                }
            });
        });

        // File type validation
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileName = file.name;
                const fileExt = fileName.split('.').pop().toLowerCase();
                const allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];
                
                if (!allowedExtensions.includes(fileExt)) {
                    this.style.borderColor = '#e53e3e';
                    alert('Please select a valid file type (PDF, DOC, DOCX, PPT, PPTX, or TXT)');
                    this.value = '';
                    fileLabel.textContent = 'Choose file or drag and drop';
                } else {
                    this.style.borderColor = '#48bb78';
                }
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.querySelector('nav');
            const navLinks = document.getElementById('navLinks');
            const navToggle = document.getElementById('navToggle');

            if (!nav.contains(event.target) && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
            }
        });

        // Prevent form submission with empty required fields
        form.addEventListener('submit', function(e) {
            let isValid = true;
            inputs.forEach(input => {
                if (input.hasAttribute('required') && input.value.trim() === '') {
                    input.style.borderColor = '#e53e3e';
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });

        // Auto-hide success messages after 5 seconds
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = '0';
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>