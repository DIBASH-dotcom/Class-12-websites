<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../Admin_Dashboard/adminlogin.php");
    exit();
}
require '../database.php'; // Adjust the path if needed

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];
    $allowedTypes = ['notes', 'questions', 'solutions'];

    if (!in_array($type, $allowedTypes)) {
        die("❌ Invalid type provided.");
    }

    // Fetch file path
    $sql = "SELECT file_path FROM `$type` WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($file_path);
        $stmt->fetch();
        $stmt->close();

        // Delete file if exists
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete DB record
        $delete_sql = "DELETE FROM `$type` WHERE id = ?";
        $delete_stmt = $con->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        $delete_stmt->execute();
        $delete_stmt->close();

        // Redirect immediately to prevent resubmission
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    } else {
        $stmt->close();
        die("❌ Record not found.");
    }
}

// Fetch all data for display
function fetchUploads($con, $table) {
    $sql = "SELECT * FROM `$table` ORDER BY uploaded_at DESC";
    return $con->query($sql);
}

$notes = fetchUploads($con, "notes");
$questions = fetchUploads($con, "questions");
$solutions = fetchUploads($con, "solutions");

$notesCount = $notes->num_rows;
$questionsCount = $questions->num_rows;
$solutionsCount = $solutions->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Portal</title>
    <meta name="robots" content="noindex, nofollow">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-glass: rgba(255, 255, 255, 0.1);
            --bg-glass-hover: rgba(255, 255, 255, 0.15);
            
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            
            --border-color: rgba(255, 255, 255, 0.1);
            --border-hover: rgba(255, 255, 255, 0.2);
            
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-glow: 0 0 20px rgba(102, 126, 234, 0.3);
            
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            
            --transition-fast: 0.15s ease;
            --transition-normal: 0.3s ease;
            --transition-slow: 0.5s ease;
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
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            opacity: 0.1;
            z-index: -2;
            animation: gradientShift 10s ease infinite;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
            z-index: -1;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { transform: translateX(0%) rotate(0deg); }
            50% { transform: translateX(100%) rotate(180deg); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-30px) rotate(120deg); }
            66% { transform: translateY(30px) rotate(240deg); }
        }

        /* Header Styles */
        .header {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
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
            box-shadow: var(--shadow-md);
        }

        .logo-text {
            font-size: 1.75rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-toggle {
            display: none;
            background: var(--bg-glass);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            cursor: pointer;
            padding: 0.75rem;
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            backdrop-filter: blur(10px);
        }

        .nav-toggle:hover {
            background: var(--bg-glass-hover);
            border-color: var(--border-hover);
            transform: translateY(-2px);
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
            transition: all var(--transition-normal);
            position: relative;
            background: var(--bg-glass);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links a:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--shadow-glow);
            border-color: transparent;
        }

        .nav-links a i {
            font-size: 1rem;
        }

        /* Main Content */
        .main-content {
            padding: 3rem 0;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }

        .dashboard-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 0.8s ease-out;
            position: relative;
        }

        .dashboard-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            animation: fadeInUp 0.8s ease-out 0.2s both;
            margin-bottom: 2rem;
        }

        .dashboard-stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .quick-stat {
            text-align: center;
            padding: 1rem 2rem;
            background: var(--bg-glass);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            backdrop-filter: blur(10px);
            transition: all var(--transition-normal);
        }

        .quick-stat:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            background: var(--bg-glass-hover);
        }

        .quick-stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .quick-stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .stat-card {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 2.5rem;
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: transform var(--transition-normal);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
            background: var(--bg-glass-hover);
            border-color: var(--border-hover);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-icon.notes {
            background: var(--primary-gradient);
        }

        .stat-icon.questions {
            background: var(--secondary-gradient);
        }

        .stat-icon.solutions {
            background: var(--success-gradient);
        }

        .stat-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .stat-card:hover .stat-icon::before {
            left: 100%;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Data Sections */
        .data-section {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            margin-bottom: 3rem;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            animation: fadeInUp 0.8s ease-out;
            transition: all var(--transition-normal);
        }

        .data-section:hover {
            box-shadow: var(--shadow-xl);
            border-color: var(--border-hover);
        }

        .section-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem 2.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .section-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.8s;
        }

        .data-section:hover .section-header::before {
            left: 100%;
        }

        .section-header i {
            font-size: 1.75rem;
        }

        .table-container {
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 1.5rem 2rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            transition: all var(--transition-fast);
        }

        .data-table th {
            background: var(--bg-secondary);
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .data-table tr {
            transition: all var(--transition-fast);
        }

        .data-table tbody tr:hover {
            background: var(--bg-glass);
            transform: scale(1.01);
        }

        .data-table td {
            color: var(--text-secondary);
        }

        .delete-btn {
            background: var(--danger-gradient);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all var(--transition-normal);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .delete-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .delete-btn:hover::before {
            left: 100%;
        }

        .delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            filter: brightness(1.1);
        }

        .delete-btn:active {
            transform: translateY(0);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
            animation: pulse 2s ease-in-out infinite;
        }

        .empty-message {
            font-size: 1.125rem;
            font-weight: 500;
        }

        /* Loading States */
        .loading-skeleton {
            background: linear-gradient(90deg, var(--bg-glass) 25%, var(--bg-glass-hover) 50%, var(--bg-glass) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 0.8; }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .container {
                padding: 0 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
                background: rgba(15, 23, 42, 0.95);
                backdrop-filter: blur(20px);
                flex-direction: column;
                padding: 2rem;
                border-top: 1px solid var(--border-color);
                box-shadow: var(--shadow-xl);
                gap: 1rem;
            }

            .nav-links.active {
                display: flex;
                animation: slideInRight 0.3s ease-out;
            }

            .nav-links a {
                width: 100%;
                justify-content: center;
            }

            .dashboard-stats {
                flex-direction: column;
                gap: 1rem;
            }

            .quick-stat {
                padding: 0.75rem 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .stat-card {
                padding: 2rem;
            }

            .data-table {
                font-size: 0.875rem;
            }

            .data-table th,
            .data-table td {
                padding: 1rem 1.5rem;
            }

            .section-header {
                padding: 1.5rem 2rem;
                font-size: 1.25rem;
            }

            .container {
                padding: 0 1rem;
            }
        }

        @media (max-width: 480px) {
            .dashboard-title {
                font-size: 2rem;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .data-table th,
            .data-table td {
                padding: 0.75rem 1rem;
            }

            .section-header {
                padding: 1.25rem 1.5rem;
            }
        }

        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus styles */
        .nav-links a:focus,
        .delete-btn:focus,
        .nav-toggle:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Custom scrollbar */
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: var(--bg-tertiary);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 600ms linear;
            background-color: rgba(255, 255, 255, 0.6);
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: var(--success-gradient);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 9999;
            transform: translateX(100%);
            transition: transform var(--transition-normal);
        }

        .notification.show {
            transform: translateX(0);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo-container">
                    <img src="../images/hello.png" alt="Student Portal Logo">
                    <span class="logo-text">Admin Dashboard</span>
                </div>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="nav-links" id="navLinks">
                   
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
        <div class="container">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Admin Dashboard</h1>
                <p class="dashboard-subtitle">Manage your educational content with powerful tools and insights</p>
                
                <div class="dashboard-stats">
                    <div class="quick-stat">
                        <div class="quick-stat-number"><?= $notesCount + $questionsCount + $solutionsCount ?></div>
                        <div class="quick-stat-label">Total Files</div>
                    </div>
                    <div class="quick-stat">
                        <div class="quick-stat-number"><?= date('Y') ?></div>
                        <div class="quick-stat-label">Current Year</div>
                    </div>
                    <div class="quick-stat">
                        <div class="quick-stat-number"><?= date('M') ?></div>
                        <div class="quick-stat-label">Current Month</div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon notes">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?= $notesCount ?></div>
                    <div class="stat-label">Notes Uploaded</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                        <span>Educational resources</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon questions">
                            <i class="fas fa-question"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?= $questionsCount ?></div>
                    <div class="stat-label">Questions Uploaded</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                        <span>Practice materials</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon solutions">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-number"><?= $solutionsCount ?></div>
                    <div class="stat-label">Solutions Uploaded</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                        <span>Answer guides</span>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <section class="data-section">
                <div class="section-header">
                    <i class="fas fa-book"></i>
                    <span>Uploaded Notes</span>
                </div>
                <div class="table-container">
                    <?php if ($notes->num_rows > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-file-alt"></i> Name</th>
                                    <th><i class="fas fa-graduation-cap"></i> Subject</th>
                                    <th><i class="fas fa-calendar"></i> Year</th>
                                    <th><i class="fas fa-clock"></i> Uploaded At</th>
                                    <th><i class="fas fa-cog"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $notes->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['subject']) ?></td>
                                    <td><?= $row['year'] ?></td>
                                    <td><?= date('M j, Y g:i A', strtotime($row['uploaded_at'])) ?></td>
                                    <td>
                                        <button class="delete-btn" onclick="confirmDelete('<?= $row['id'] ?>', 'notes', 'note')">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <p class="empty-message">No notes uploaded yet. Start by uploading your first note!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Questions Section -->
            <section class="data-section">
                <div class="section-header">
                    <i class="fas fa-question"></i>
                    <span>Uploaded Questions</span>
                </div>
                <div class="table-container">
                    <?php if ($questions->num_rows > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-file-alt"></i> Name</th>
                                    <th><i class="fas fa-graduation-cap"></i> Subject</th>
                                    <th><i class="fas fa-calendar"></i> Year</th>
                                    <th><i class="fas fa-clock"></i> Uploaded At</th>
                                    <th><i class="fas fa-cog"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $questions->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['subject']) ?></td>
                                    <td><?= $row['year'] ?></td>
                                    <td><?= date('M j, Y g:i A', strtotime($row['uploaded_at'])) ?></td>
                                    <td>
                                        <button class="delete-btn" onclick="confirmDelete('<?= $row['id'] ?>', 'questions', 'question')">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-question"></i>
                            </div>
                            <p class="empty-message">No questions uploaded yet. Start by uploading your first question!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Solutions Section -->
            <section class="data-section">
                <div class="section-header">
                    <i class="fas fa-check-circle"></i>
                    <span>Uploaded Solutions</span>
                </div>
                <div class="table-container">
                    <?php if ($solutions->num_rows > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-file-alt"></i> Name</th>
                                    <th><i class="fas fa-graduation-cap"></i> Subject</th>
                                    <th><i class="fas fa-calendar"></i> Year</th>
                                    <th><i class="fas fa-clock"></i> Uploaded At</th>
                                    <th><i class="fas fa-cog"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $solutions->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['subject']) ?></td>
                                    <td><?= $row['year'] ?></td>
                                    <td><?= date('M j, Y g:i A', strtotime($row['uploaded_at'])) ?></td>
                                    <td>
                                        <button class="delete-btn" onclick="confirmDelete('<?= $row['id'] ?>', 'solutions', 'solution')">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="empty-message">No solutions uploaded yet. Start by uploading your first solution!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
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
            
            // Animate hamburger icon
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

        // Enhanced delete confirmation with better UX
        function confirmDelete(id, type, itemName) {
            const modal = createConfirmModal(
                `Delete ${itemName}?`,
                `Are you sure you want to delete this ${itemName}? This action cannot be undone.`,
                () => {
                    // Show loading state
                    showNotification('Deleting...', 'info');
                    window.location.href = `?id=${id}&type=${type}`;
                }
            );
            document.body.appendChild(modal);
        }

        // Create custom confirmation modal
        function createConfirmModal(title, message, onConfirm) {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                backdrop-filter: blur(5px);
            `;

            modal.innerHTML = `
                <div style="
                    background: var(--bg-secondary);
                    border: 1px solid var(--border-color);
                    border-radius: var(--radius-xl);
                    padding: 2rem;
                    max-width: 400px;
                    width: 90%;
                    text-align: center;
                    box-shadow: var(--shadow-xl);
                ">
                    <div style="
                        width: 64px;
                        height: 64px;
                        background: var(--danger-gradient);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 1.5rem;
                        font-size: 1.5rem;
                        color: white;
                    ">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 style="
                        color: var(--text-primary);
                        margin-bottom: 1rem;
                        font-size: 1.25rem;
                        font-weight: 600;
                    ">${title}</h3>
                    <p style="
                        color: var(--text-secondary);
                        margin-bottom: 2rem;
                        line-height: 1.5;
                    ">${message}</p>
                    <div style="
                        display: flex;
                        gap: 1rem;
                        justify-content: center;
                    ">
                        <button class="cancel-btn" style="
                            background: var(--bg-glass);
                            border: 1px solid var(--border-color);
                            color: var(--text-secondary);
                            padding: 0.75rem 1.5rem;
                            border-radius: var(--radius-md);
                            cursor: pointer;
                            font-weight: 500;
                            transition: all var(--transition-fast);
                        ">Cancel</button>
                        <button class="confirm-btn" style="
                            background: var(--danger-gradient);
                            border: none;
                            color: white;
                            padding: 0.75rem 1.5rem;
                            border-radius: var(--radius-md);
                            cursor: pointer;
                            font-weight: 500;
                            transition: all var(--transition-fast);
                        ">Delete</button>
                    </div>
                </div>
            `;

            // Add event listeners
            modal.querySelector('.cancel-btn').addEventListener('click', () => {
                modal.remove();
            });

            modal.querySelector('.confirm-btn').addEventListener('click', () => {
                modal.remove();
                onConfirm();
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });

            return modal;
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Ripple effect for buttons
        function createRipple(event) {
            const button = event.currentTarget;
            const circle = document.createElement('span');
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
            circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
            circle.classList.add('ripple');

            const ripple = button.getElementsByClassName('ripple')[0];
            if (ripple) {
                ripple.remove();
            }

            button.appendChild(circle);
        }

        // Apply ripple effect to interactive elements
        document.querySelectorAll('.delete-btn, .nav-links a, .stat-card').forEach(element => {
            element.addEventListener('click', createRipple);
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe animated elements
        document.querySelectorAll('.data-section, .stat-card').forEach(el => {
            observer.observe(el);
        });

        // Keyboard navigation support
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                // Close mobile menu
                navLinks.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                
                // Close any open modals
                document.querySelectorAll('.notification').forEach(modal => {
                    modal.remove();
                });
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading states to navigation links
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.href.includes('#')) {
                    this.style.opacity = '0.7';
                    this.style.pointerEvents = 'none';
                    
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    
                    // Reset after 3 seconds if page doesn't load
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.opacity = '1';
                        this.style.pointerEvents = 'auto';
                    }, 3000);
                }
            });
        });

        // Auto-refresh data every 30 seconds
        setInterval(() => {
            // Only refresh if user is active (has interacted in last 5 minutes)
            if (Date.now() - lastActivity < 300000) {
                location.reload();
            }
        }, 30000);

        // Track user activity
        let lastActivity = Date.now();
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, () => {
                lastActivity = Date.now();
            }, true);
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', () => {
            // Add entrance animations
            document.querySelectorAll('.stat-card').forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Show success message if redirected after deletion
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('deleted')) {
                showNotification('Item deleted successfully!', 'success');
            }
        });
    </script>
</body>
</html>