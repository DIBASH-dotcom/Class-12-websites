<?php
require '../database.php';

// Fetch all questions ordered by province, subject, and course_type
$sql = "SELECT * FROM questions ORDER BY province, subject, course_type, id DESC";
$result = $con->query($sql);

// Organize data by province → subject → course_type
$questionsByProvince = [];
$allProvinces = [];
$allSubjects = [];
$allYears = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $province = $row['province'];
        $subject = $row['subject'];
        $courseType = $row['course_type']; // Technical or Non-Technical
        $year = $row['year'];

        // Collect unique values for filters
        if (!in_array($province, $allProvinces)) {
            $allProvinces[] = $province;
        }
        if (!in_array($subject, $allSubjects)) {
            $allSubjects[] = $subject;
        }
        if (!in_array($year, $allYears)) {
            $allYears[] = $year;
        }

        // Create the structure if not already set
        if (!isset($questionsByProvince[$province])) {
            $questionsByProvince[$province] = [];
        }

        if (!isset($questionsByProvince[$province][$subject])) {
            $questionsByProvince[$province][$subject] = [
                'Technical' => [],
                'Non-Technical' => []
            ];
        }

        // Add question to the correct group
        $questionsByProvince[$province][$subject][$courseType][] = $row;
    }
}

// Sort arrays for consistent display
sort($allProvinces);
sort($allSubjects);
rsort($allYears); // Most recent years first
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>NEB Class 12 Exam Questions by Province | Technical & Non-Technical</title>
<meta name="description" content="Browse NEB class 12 exam questions by province, subject, year, and stream (technical & non-technical). Easy filtering for exam prep." />
<link rel="canonical" href="https://dibashmagar123.com.np/UploadsQuestion/province.php" />


<!-- Open Graph / Facebook -->
<meta property="og:type" content="website" />
<meta property="og:title" content="NEB Class 12 Exam Questions by Province | Technical & Non-Technical" />
<meta property="og:description" content="Browse NEB class 12 exam questions by province, subject, year, and stream (technical & non-technical). Easy filtering for exam prep." />
<meta property="og:url" content="https://yourwebsite.com/province-questions" />
<!-- <meta property="og:image" content="https://yourwebsite.com/images/neb-exam-questions-preview.jpg" /> -->

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="NEB Class 12 Exam Questions by Province | Technical & Non-Technical" />
<meta name="twitter:description" content="Browse NEB class 12 exam questions by province, subject, year, and stream (technical & non-technical). Easy filtering for exam prep." />
<meta name="twitter:url" content="https://yourwebsite.com/province-questions" />
<!-- <meta name="twitter:image" content="https://yourwebsite.com/images/neb-exam-questions-preview.jpg" /> -->

    <style>
        /* CSS Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #059669;
            --success-hover: #047857;
            --warning-color: #d97706;
            --warning-hover: #b45309;
            --technical-color: #7c3aed;
            --non-technical-color: #059669;
            --background-color: #f8fafc;
            --surface-color: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --border-hover: #cbd5e1;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background-color: var(--background-color);
            min-height: 100vh;
        }

        /* Header Styles */
        #header {
            background: var(--surface-color);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-container img {
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .nav-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius);
            color: var(--text-primary);
            transition: background-color 0.2s;
        }

        .nav-toggle:hover {
            background-color: var(--background-color);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            transition: all 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
            background-color: var(--background-color);
        }

        /* Main Content */
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Filter Section */
        .filter-section {
            background: var(--surface-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .filter-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .filter-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-toggle {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius);
            transition: background-color 0.2s;
        }

        .filter-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .filter-content {
            padding: 1.5rem;
            display: block;
        }

        .filter-content.collapsed {
            display: none;
        }

        /* Search Bar */
        .search-container {
            margin-bottom: 1.5rem;
        }

        .search-box {
            position: relative;
            max-width: 400px;
            margin: 0 auto;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            font-size: 1rem;
            transition: all 0.2s;
            background: var(--surface-color);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .clear-search {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 50%;
            display: none;
        }

        .clear-search:hover {
            background-color: var(--background-color);
        }

        /* Filter Controls */
        .filter-controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .filter-select {
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            background: var(--surface-color);
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Quick Filter Buttons */
        .quick-filters {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: 2px solid var(--border-color);
            background: var(--surface-color);
            color: var(--text-secondary);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-1px);
        }

        .filter-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .filter-btn.technical.active {
            background: var(--technical-color);
            border-color: var(--technical-color);
        }

        .filter-btn.non-technical.active {
            background: var(--non-technical-color);
            border-color: var(--non-technical-color);
        }

        /* Clear Filters */
        .filter-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .clear-filters-btn {
            padding: 0.75rem 1.5rem;
            background: var(--warning-color);
            color: white;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .clear-filters-btn:hover {
            background: var(--warning-hover);
            transform: translateY(-1px);
        }

        /* Province Sections */
        .province-box {
            background: var(--surface-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .province-box.hidden {
            display: none;
        }

        .province-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .province-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .province-content {
            padding: 2rem;
        }

        /* Subject Groups */
        .subject-box {
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .subject-box:last-child {
            margin-bottom: 0;
        }

        .subject-box.hidden {
            display: none;
        }

        .subject-header {
            background: var(--background-color);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .subject-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .subject-stats {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .stat-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-badge.technical {
            background: var(--technical-color);
            color: white;
        }

        .stat-badge.non-technical {
            background: var(--non-technical-color);
            color: white;
        }

        .resource-count {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Course Type Sections */
        .course-type {
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .course-type.hidden {
            display: none;
        }

        .course-type-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .course-type-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .course-type-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .course-type-badge.technical {
            background: var(--technical-color);
            color: white;
        }

        .course-type-badge.non-technical {
            background: var(--non-technical-color);
            color: white;
        }

        /* Question Items */
        .questions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .question-item {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            transition: all 0.2s;
        }

        .question-item.hidden {
            display: none;
        }

        .question-item:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .question-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .question-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .meta-item {
            font-size: 0.75rem;
            color: var(--text-secondary);
            background: var(--background-color);
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius);
            font-weight: 500;
        }

        .question-desc {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.5;
            font-size: 0.875rem;
        }

        .question-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        /* Action Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            border: 1px solid transparent;
            flex: 1;
            justify-content: center;
            min-width: 120px;
        }

        .btn-view {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .btn-view:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-download {
            background-color: var(--success-color);
            color: white;
            border-color: var(--success-color);
        }

        .btn-download:hover {
            background-color: var(--success-hover);
            border-color: var(--success-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-secondary);
        }

        .no-results svg {
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .no-results p {
            font-size: 1.125rem;
        }

        /* Results Summary */
        .results-summary {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--surface-color);
            border-radius: var(--radius);
            color: var(--text-secondary);
            box-shadow: var(--shadow-sm);
        }

        /* Responsive Design */
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
                background: var(--surface-color);
                border-top: 1px solid var(--border-color);
                flex-direction: column;
                padding: 1rem;
                gap: 0;
                box-shadow: var(--shadow-lg);
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                width: 100%;
                text-align: center;
                padding: 1rem;
                border-bottom: 1px solid var(--border-color);
            }

            .nav-links a:last-child {
                border-bottom: none;
            }

            .page-title {
                font-size: 2rem;
            }

            .filter-controls {
                grid-template-columns: 1fr;
            }

            .quick-filters {
                flex-direction: column;
                align-items: center;
            }

            .filter-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .province-content {
                padding: 1rem;
            }

            .subject-header {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .course-type {
                padding: 1rem;
            }

            .questions-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .question-actions {
                flex-direction: column;
            }

            .btn {
                flex: none;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 0.5rem;
            }

            main {
                padding: 1rem 0.5rem;
            }

            .page-title {
                font-size: 1.75rem;
            }

            .filter-content {
                padding: 1rem;
            }

            .province-header {
                padding: 1rem;
            }

            .province-title {
                font-size: 1.5rem;
            }

            .question-item {
                padding: 1rem;
            }
        }

        /* Focus Styles for Accessibility */
        .btn:focus,
        .nav-links a:focus,
        .nav-toggle:focus,
        .filter-btn:focus,
        .filter-select:focus,
        .search-input:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Print Styles */
        @media print {
            #header,
            .filter-section {
                display: none;
            }

            .province-box {
                box-shadow: none;
                border: 1px solid var(--border-color);
                break-inside: avoid;
            }

            .btn {
                display: none;
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
                    <span class="logo-text">Student Portal</span>
                </div>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                
                <div class="nav-links" id="navLinks">
                    <a href="https://dibashmagar123.com.np/">Home</a>
                    
                    <a href="https://dibashmagar123.com.np/UploadsSolution/province.php">Solutions Province Wise</a>
                    <a href="https://dibashmagar123.com.np/UploadNotes/View_Notes.php">Notes</a>
                    <a href="https://dibashmagar123.com.np/Pages/About.php">About Us</a>
                    <a href=".https://dibashmagar123.com.np/Pages/Contact.php">Contact Us</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="page-header">
            <h1 class="page-title">Province-Wise Questions Dashboard</h1>
            <p class="page-subtitle">
                Browse and access questions organized by province and subject with advanced filtering options.
            </p>
        </div>

        <!-- Advanced Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
                <h3 class="filter-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"></polygon>
                    </svg>
                    Advanced Filters
                </h3>
                <button class="filter-toggle" id="filterToggle" aria-label="Toggle filters">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
            </div>
            
            <div class="filter-content" id="filterContent">
                <!-- Search Bar -->
                <div class="search-container">
                    <div class="search-box">
                        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" class="search-input" id="searchInput" placeholder="Search questions by title or description...">
                        <button class="clear-search" id="clearSearch" aria-label="Clear search">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Quick Filter Buttons -->
                <div class="quick-filters">
                    <button class="filter-btn active" data-filter="all" id="filterAll">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                        All Questions
                    </button>
                    <button class="filter-btn technical" data-filter="Technical" id="filterTechnical">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                        Technical
                    </button>
                    <button class="filter-btn non-technical" data-filter="Non-Technical" id="filterNonTechnical">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        Non-Technical
                    </button>
                </div>

                <!-- Detailed Filters -->
                <div class="filter-controls">
                    <div class="filter-group">
                        <label class="filter-label" for="provinceFilter">Province</label>
                        <select class="filter-select" id="provinceFilter">
                            <option value="">All Provinces</option>
                            <?php foreach ($allProvinces as $province): ?>
                                <option value="<?php echo htmlspecialchars($province); ?>">
                                    <?php echo htmlspecialchars($province); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label" for="subjectFilter">Subject</label>
                        <select class="filter-select" id="subjectFilter">
                            <option value="">All Subjects</option>
                            <?php foreach ($allSubjects as $subject): ?>
                                <option value="<?php echo htmlspecialchars($subject); ?>">
                                    <?php echo htmlspecialchars($subject); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label" for="yearFilter">Year</label>
                        <select class="filter-select" id="yearFilter">
                            <option value="">All Years</option>
                            <?php foreach ($allYears as $year): ?>
                                <option value="<?php echo htmlspecialchars($year); ?>">
                                    <?php echo htmlspecialchars($year); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="filter-actions">
                    <button class="clear-filters-btn" id="clearFilters">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                        </svg>
                        Clear All Filters
                    </button>
                </div>
            </div>
        </div>

        <div class="results-summary" id="resultsSummary"></div>

        <?php if (!empty($questionsByProvince)): ?>
            <?php foreach ($questionsByProvince as $province => $subjects): ?>
                <div class="province-box" data-province="<?php echo htmlspecialchars($province); ?>">
                    <div class="province-header">
                        <h2 class="province-title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <?php echo htmlspecialchars($province); ?>
                        </h2>
                    </div>

                    <div class="province-content">
                        <?php foreach ($subjects as $subject => $courseTypes): ?>
                            <div class="subject-box" data-subject="<?php echo htmlspecialchars($subject); ?>">
                                <div class="subject-header">
                                    <h3 class="subject-title">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                        <?php echo htmlspecialchars($subject); ?>
                                    </h3>
                                    <div class="subject-stats">
                                        <?php 
                                        $techCount = count($courseTypes['Technical']);
                                        $nonTechCount = count($courseTypes['Non-Technical']);
                                        ?>
                                        <?php if ($techCount > 0): ?>
                                            <span class="stat-badge technical"><?php echo $techCount; ?> Technical</span>
                                        <?php endif; ?>
                                        <?php if ($nonTechCount > 0): ?>
                                            <span class="stat-badge non-technical"><?php echo $nonTechCount; ?> Non-Technical</span>
                                        <?php endif; ?>
                                        <span class="resource-count"><?php echo ($techCount + $nonTechCount); ?> total questions</span>
                                    </div>
                                </div>

                                <?php foreach (['Technical', 'Non-Technical'] as $type): ?>
                                    <?php if (!empty($courseTypes[$type])): ?>
                                        <div class="course-type" data-course-type="<?php echo $type; ?>">
                                            <div class="course-type-header">
                                                <h4 class="course-type-title"><?php echo $type; ?> Questions</h4>
                                                <span class="course-type-badge <?php echo strtolower(str_replace('-', '-', $type)); ?>">
                                                    <?php echo count($courseTypes[$type]); ?> questions
                                                </span>
                                            </div>
                                            
                                            <div class="questions-grid">
                                                <?php foreach ($courseTypes[$type] as $question): ?>
                                                    <div class="question-item" 
                                                         data-title="<?php echo htmlspecialchars(strtolower($question['name'])); ?>"
                                                         data-description="<?php echo htmlspecialchars(strtolower($question['description'])); ?>"
                                                         data-year="<?php echo htmlspecialchars($question['year']); ?>"
                                                         data-province="<?php echo htmlspecialchars($question['province']); ?>"
                                                         data-subject="<?php echo htmlspecialchars($question['subject']); ?>"
                                                         data-course-type="<?php echo htmlspecialchars($question['course_type']); ?>">
                                                        
                                                        <div class="question-title"><?php echo htmlspecialchars($question['name']); ?></div>
                                                        
                                                        <div class="question-meta">
                                                            <span class="meta-item">Year: <?php echo htmlspecialchars($question['year']); ?></span>
                                                            <span class="meta-item">Type: <?php echo htmlspecialchars($question['course_type']); ?></span>
                                                        </div>
                                                        
                                                        <?php if (!empty($question['description'])): ?>
                                                            <div class="question-desc"><?php echo nl2br(htmlspecialchars($question['description'])); ?></div>
                                                        <?php endif; ?>

                                                        <div class="question-actions">
                                                            <?php
                                                            $filePath = '../UploadsQuestion/uploads/' . rawurlencode(basename($question['file_path']));
                                                            ?>
                                                            <a class="btn btn-view" href="<?php echo $filePath; ?>" target="_blank" aria-label="View <?php echo htmlspecialchars($question['name']); ?>">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                                    <circle cx="12" cy="12" r="3"></circle>
                                                                </svg>
                                                                View PDF
                                                            </a>
                                                            <a class="btn btn-download" href="<?php echo $filePath; ?>" download aria-label="Download <?php echo htmlspecialchars($question['name']); ?>">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                    <polyline points="7,10 12,15 17,10"></polyline>
                                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                                </svg>
                                                                Download PDF
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <p>No questions uploaded yet.</p>
            </div>
        <?php endif; ?>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Navigation Toggle
            const navToggle = document.getElementById('navToggle');
            const navLinks = document.getElementById('navLinks');

            if (navToggle && navLinks) {
                navToggle.addEventListener('click', function() {
                    const isExpanded = navLinks.classList.contains('active');
                    
                    navLinks.classList.toggle('active');
                    navToggle.setAttribute('aria-expanded', !isExpanded);
                    
                    if (!isExpanded) {
                        document.addEventListener('click', closeMenuOnOutsideClick);
                    } else {
                        document.removeEventListener('click', closeMenuOnOutsideClick);
                    }
                });

                function closeMenuOnOutsideClick(event) {
                    if (!navToggle.contains(event.target) && !navLinks.contains(event.target)) {
                        navLinks.classList.remove('active');
                        navToggle.setAttribute('aria-expanded', 'false');
                        document.removeEventListener('click', closeMenuOnOutsideClick);
                    }
                }

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        navToggle.setAttribute('aria-expanded', 'false');
                        navToggle.focus();
                    }
                });
            }

            // Filter Toggle
            const filterToggle = document.getElementById('filterToggle');
            const filterContent = document.getElementById('filterContent');

            filterToggle.addEventListener('click', function() {
                filterContent.classList.toggle('collapsed');
                const isCollapsed = filterContent.classList.contains('collapsed');
                
                // Rotate the arrow
                this.style.transform = isCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)';
            });

            // Filter Elements
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');
            const provinceFilter = document.getElementById('provinceFilter');
            const subjectFilter = document.getElementById('subjectFilter');
            const yearFilter = document.getElementById('yearFilter');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const resultsSummary = document.getElementById('resultsSummary');

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                clearSearch.style.display = searchTerm ? 'block' : 'none';
                applyFilters();
            });

            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                this.style.display = 'none';
                applyFilters();
                searchInput.focus();
            });

            // Filter change handlers
            provinceFilter.addEventListener('change', applyFilters);
            subjectFilter.addEventListener('change', applyFilters);
            yearFilter.addEventListener('change', applyFilters);

            // Quick filter buttons
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filterType = this.getAttribute('data-filter');
                    
                    // Update active state
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Apply filter
                    applyFilters();
                });
            });

            // Clear all filters
            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                clearSearch.style.display = 'none';
                provinceFilter.value = '';
                subjectFilter.value = '';
                yearFilter.value = '';
                
                filterButtons.forEach(btn => btn.classList.remove('active'));
                document.getElementById('filterAll').classList.add('active');
                
                applyFilters();
                
                // Announce to screen readers
                announceToScreenReader('All filters cleared');
            });

            function getActiveQuickFilter() {
                const activeBtn = document.querySelector('.filter-btn.active');
                return activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
            }

            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedProvince = provinceFilter.value;
                const selectedSubject = subjectFilter.value;
                const selectedYear = yearFilter.value;
                const quickFilter = getActiveQuickFilter();

                let visibleQuestions = 0;
                let visibleSubjects = 0;
                let visibleProvinces = 0;

                // Filter questions
                document.querySelectorAll('.question-item').forEach(question => {
                    let isVisible = true;

                    // Search filter
                    if (searchTerm) {
                        const title = question.getAttribute('data-title') || '';
                        const description = question.getAttribute('data-description') || '';
                        if (!title.includes(searchTerm) && !description.includes(searchTerm)) {
                            isVisible = false;
                        }
                    }

                    // Province filter
                    if (selectedProvince && question.getAttribute('data-province') !== selectedProvince) {
                        isVisible = false;
                    }

                    // Subject filter
                    if (selectedSubject && question.getAttribute('data-subject') !== selectedSubject) {
                        isVisible = false;
                    }

                    // Year filter
                    if (selectedYear && question.getAttribute('data-year') !== selectedYear) {
                        isVisible = false;
                    }

                    // Quick filter (Technical/Non-Technical)
                    if (quickFilter !== 'all' && question.getAttribute('data-course-type') !== quickFilter) {
                        isVisible = false;
                    }

                    // Apply visibility
                    if (isVisible) {
                        question.classList.remove('hidden');
                        visibleQuestions++;
                    } else {
                        question.classList.add('hidden');
                    }
                });

                // Hide/show course type sections
                document.querySelectorAll('.course-type').forEach(courseType => {
                    const visibleQuestionsInType = courseType.querySelectorAll('.question-item:not(.hidden)');
                    if (visibleQuestionsInType.length === 0) {
                        courseType.classList.add('hidden');
                    } else {
                        courseType.classList.remove('hidden');
                    }
                });

                // Hide/show subject boxes
                document.querySelectorAll('.subject-box').forEach(subjectBox => {
                    const visibleCourseTypes = subjectBox.querySelectorAll('.course-type:not(.hidden)');
                    if (visibleCourseTypes.length === 0) {
                        subjectBox.classList.add('hidden');
                    } else {
                        subjectBox.classList.remove('hidden');
                        visibleSubjects++;
                    }
                });

                // Hide/show province boxes
                document.querySelectorAll('.province-box').forEach(provinceBox => {
                    const visibleSubjectsInProvince = provinceBox.querySelectorAll('.subject-box:not(.hidden)');
                    if (visibleSubjectsInProvince.length === 0) {
                        provinceBox.classList.add('hidden');
                    } else {
                        provinceBox.classList.remove('hidden');
                        visibleProvinces++;
                    }
                });

                // Update results summary
                updateResultsSummary(visibleQuestions, visibleSubjects, visibleProvinces, quickFilter);

                // Handle no results
                handleNoResults(visibleQuestions);
            }

            function updateResultsSummary(questions, subjects, provinces, filterType) {
                if (questions > 0) {
                    const filterText = filterType === 'all' ? 'all' : filterType.toLowerCase();
                    resultsSummary.innerHTML = `
                        Showing <strong>${questions}</strong> ${filterText} questions across 
                        <strong>${subjects}</strong> subjects in <strong>${provinces}</strong> provinces
                    `;
                    resultsSummary.style.display = 'block';
                } else {
                    resultsSummary.style.display = 'none';
                }
            }

            function handleNoResults(visibleQuestions) {
                const existingNoResults = document.querySelector('.no-results.filter-message');
                
                if (visibleQuestions === 0 && !existingNoResults) {
                    const noResultsDiv = document.createElement('div');
                    noResultsDiv.className = 'no-results filter-message';
                    noResultsDiv.innerHTML = `
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <p>No questions match your current filters. Try adjusting your search criteria.</p>
                    `;
                    document.querySelector('main').appendChild(noResultsDiv);
                } else if (visibleQuestions > 0 && existingNoResults) {
                    existingNoResults.remove();
                }
            }

            function announceToScreenReader(message) {
                const announcement = document.createElement('div');
                announcement.setAttribute('aria-live', 'polite');
                announcement.setAttribute('aria-atomic', 'true');
                announcement.className = 'sr-only';
                announcement.textContent = message;
                document.body.appendChild(announcement);
                
                setTimeout(() => {
                    document.body.removeChild(announcement);
                }, 1000);
            }

            // Initialize filters
            applyFilters();

            // Add loading state for buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.classList.contains('btn-download')) {
                        const originalText = this.innerHTML;
                        this.innerHTML = `
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="animate-spin">
                                <path d="M21 12a9 9 0 11-6.219-8.56"/>
                            </svg>
                            Downloading...
                        `;
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    }
                });
            });

            // Keyboard navigation for filter buttons
            filterButtons.forEach((button, index) => {
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                        e.preventDefault();
                        const nextIndex = e.key === 'ArrowRight' 
                            ? (index + 1) % filterButtons.length 
                            : (index - 1 + filterButtons.length) % filterButtons.length;
                        filterButtons[nextIndex].focus();
                    }
                });
            });
        });

        // Add CSS animation for spinning loader
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            .sr-only {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>