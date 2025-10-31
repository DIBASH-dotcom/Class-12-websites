<?php
require '../database.php';

// Fetch all questions, ordered by subject, course_type, and latest first
$sql = "SELECT * FROM solutions ORDER BY subject, course_type, id DESC";
$result = $con->query($sql);

// Group questions by subject and course_type
$questionsBySubject = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subject = $row['subject'];
        $courseType = $row['course_type'];

        if (!isset($questionsBySubject[$subject])) {
            $questionsBySubject[$subject] = [
                'Technical' => [],
                'Non-Technical' => []
            ];
        }

        $questionsBySubject[$subject][$courseType][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solutions Dashboard | Student Portal</title>
    <meta name="description" content="Browse and access solutions organized by subject with technical/non-technical filtering and AI assistance">
    <link rel="canonical" href="https://dibashmagar123.com.np/UploadsSolution/view_solutions.php" />

    
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

        /* Filter Controls */
        .filter-controls {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            border: 2px solid var(--border-color);
            background: var(--surface-color);
            color: var(--text-secondary);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
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

        /* Subject Groups */
        .subjects-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .subject-box {
            background: var(--surface-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .subject-box.hidden {
            display: none;
        }

        .subject-header {
            background: linear-gradient(135deg, var(--success-color), var(--success-hover));
            color: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .subject-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .subject-stats {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .stat-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .resource-count {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .subject-content {
            padding: 2rem;
        }

        /* Course Type Sections */
        .course-type {
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .course-type:last-child {
            margin-bottom: 0;
        }

        .course-type.hidden {
            display: none;
        }

        .course-type-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-color);
        }

        .course-type-title {
            font-size: 1.25rem;
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

        /* Solution Items */
        .solutions-grid {
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

        .question-desc {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.5;
            font-size: 0.875rem;
            white-space: pre-line;
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

        .btn:nth-child(1) {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .btn:nth-child(1):hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn:nth-child(2) {
            background-color: var(--success-color);
            color: white;
            border-color: var(--success-color);
        }

        .btn:nth-child(2):hover {
            background-color: var(--success-hover);
            border-color: var(--success-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* AI Chatbot Section */
        .ai-section {
            background: var(--surface-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            margin-top: 3rem;
        }

        .ai-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .ai-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .ai-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .ai-input-container {
            margin-bottom: 1.5rem;
        }

        .ai-textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 1rem;
            font-family: inherit;
            resize: vertical;
            min-height: 120px;
            transition: border-color 0.2s;
        }

        .ai-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .ai-button {
            background: linear-gradient(135deg, var(--success-color), var(--success-hover));
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0 auto;
        }

        .ai-button:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .ai-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .ai-response {
            margin-top: 1.5rem;
            background: var(--background-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            display: none;
        }

        .ai-response.show {
            display: block;
        }

        .ai-response-header {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ai-response-content {
            white-space: pre-wrap;
            line-height: 1.6;
            color: var(--text-primary);
        }

        .download-pdf-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1rem;
            display: none;
        }

        .download-pdf-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .download-pdf-btn.show {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

            .subject-content {
                padding: 1rem;
            }

            .subject-header {
                padding: 1rem;
            }

            .subject-title {
                font-size: 1.25rem;
            }

            .solutions-grid {
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

            .filter-controls {
                flex-direction: column;
                align-items: center;
            }

            .filter-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .ai-section {
                padding: 1rem;
            }

            .ai-title {
                font-size: 1.5rem;
                flex-direction: column;
                gap: 0.25rem;
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

            .question-item {
                padding: 1rem;
            }
        }

        /* Focus Styles for Accessibility */
        .btn:focus,
        .nav-links a:focus,
        .nav-toggle:focus,
        .filter-btn:focus,
        .ai-button:focus,
        .ai-textarea:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Print Styles */
        @media print {
            #header,
            .filter-controls,
            .ai-section {
                display: none;
            }

            .subject-box {
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
                   <a href="https://dibashmagar123.com.np/"><i class="fa fa-home"></i> Home</a>
<a href="https://dibashmagar123.com.np/UploadsQuestion/province.php"><i class="fa fa-question-circle"></i> Questions Province Wise</a>
<a href="https://dibashmagar123.com.np/UploadsSolution/province.php"><i class="fa fa-check-circle"></i> Solutions Province Wise</a>
<a href="https://dibashmagar123.com.np/UploadNotes/View_Notes.php"><i class="fa fa-book"></i> Notes</a>
<a href="https://dibashmagar123.com.np/Pages/About.php"><i class="fa fa-info-circle"></i> About Us</a>
<a href="https://dibashmagar123.com.np/Pages/Contact.php"><i class="fa fa-phone"></i> Contact Us</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="page-header">
            <h1 class="page-title">Solutions Dashboard</h1>
            <p class="page-subtitle">
                Access comprehensive solutions organized by subject with technical and non-technical filtering options, plus AI assistance.
            </p>
        </div>

        <div class="filter-controls">
            <button class="filter-btn active" data-filter="all" id="filterAll">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 6v6l4 2"></path>
                </svg>
                All Solutions
            </button>
            <button class="filter-btn technical" data-filter="Technical" id="filterTechnical">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                Technical Solutions
            </button>
            <button class="filter-btn non-technical" data-filter="Non-Technical" id="filterNonTechnical">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                Non-Technical Solutions
            </button>
        </div>

        <div class="results-summary" id="resultsSummary"></div>

        <?php if (!empty($questionsBySubject)): ?>
            <div class="subjects-container">
                <?php foreach ($questionsBySubject as $subject => $courseTypes): ?>
                    <div class="subject-box" data-subject="<?= htmlspecialchars($subject) ?>">
                        <div class="subject-header">
                            <h2 class="subject-title">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <?= htmlspecialchars($subject) ?>
                            </h2>
                            <div class="subject-stats">
                                <?php 
                                $techCount = count($courseTypes['Technical']);
                                $nonTechCount = count($courseTypes['Non-Technical']);
                                ?>
                                <?php if ($techCount > 0): ?>
                                    <span class="stat-badge"><?= $techCount ?> Technical</span>
                                <?php endif; ?>
                                <?php if ($nonTechCount > 0): ?>
                                    <span class="stat-badge"><?= $nonTechCount ?> Non-Technical</span>
                                <?php endif; ?>
                                <span class="resource-count"><?= ($techCount + $nonTechCount) ?> total solutions</span>
                            </div>
                        </div>

                        <div class="subject-content">
                            <?php foreach (['Technical', 'Non-Technical'] as $type): ?>
                                <?php if (!empty($courseTypes[$type])): ?>
                                    <div class="course-type" data-course-type="<?= $type ?>">
                                        <div class="course-type-header">
                                            <h3 class="course-type-title"><?= $type ?> Solutions</h3>
                                            <span class="course-type-badge <?= strtolower(str_replace('-', '-', $type)) ?>">
                                                <?= count($courseTypes[$type]) ?> solutions
                                            </span>
                                        </div>
                                        
                                        <div class="solutions-grid">
                                            <?php foreach ($courseTypes[$type] as $question): ?>
                                                <div class="question-item">
                                                    <div class="question-title"><?= htmlspecialchars($question['name']) ?></div>
                                                    <?php if (!empty($question['description'])): ?>
                                                        <div class="question-desc"><?= nl2br(htmlspecialchars($question['description'])) ?></div>
                                                    <?php endif; ?>

                                                    <div class="question-actions">
                                                        <?php 
                                                        $filePath = '../UploadsSolution/uploads/' . rawurlencode(basename($question['file_path']));
                                                        ?>
                                                        <a class="btn" href="<?= $filePath ?>" target="_blank" rel="noopener noreferrer" aria-label="View <?= htmlspecialchars($question['name']) ?>">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg>
                                                            View PDF
                                                        </a>
                                                        <a class="btn" href="<?= $filePath ?>" download aria-label="Download <?= htmlspecialchars($question['name']) ?>">
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
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <p>No solutions uploaded yet.</p>
            </div>
        <?php endif; ?>

        <!-- AI Chatbot Section -->
        <div class="ai-section">
            <div class="ai-header">
                <h2 class="ai-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4"></path>
                        <path d="M21 12c.552 0 1-.448 1-1V8c0-.552-.448-1-1-1h-1V6c0-.552-.448-1-1-1H4c-.552 0-1 .448-1 1v1H2c-.552 0-1 .448-1 1v3c0 .552.448 1 1 1h1v1c0 .552.448 1 1 1h15c.552 0 1-.448 1-1v-1h1z"></path>
                    </svg>
                    ✍️ तपाईंको प्रश्न टाइप गर्नुहोस्
                </h2>
                <p class="ai-subtitle">AI बाट उत्तर पाउन</p>
            </div>

            <div class="ai-input-container">
                <textarea 
                    id="userQuestion" 
                    class="ai-textarea" 
                    placeholder="यहाँ प्रश्न टाइप गर्नुहोस्..."
                    aria-label="Enter your question here"
                ></textarea>
            </div>

            <button class="ai-button" onclick="getAISolution()" id="aiSubmitBtn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4"></path>
                    <path d="M21 12c.552 0 1-.448 1-1V8c0-.552-.448-1-1-1h-1V6c0-.552-.448-1-1-1H4c-.552 0-1 .448-1 1v1H2c-.552 0-1 .448-1 1v3c0 .552.448 1 1 1h1v1c0 .552.448 1 1 1h15c.552 0 1-.448 1-1v-1h1z"></path>
                </svg>
                AI बाट समाधान हेर्नुहोस्
            </button>

            <div id="aiResponse" class="ai-response">
                <div class="ai-response-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4"></path>
                        <path d="M21 12c.552 0 1-.448 1-1V8c0-.552-.448-1-1-1h-1V6c0-.552-.448-1-1-1H4c-.552 0-1 .448-1 1v1H2c-.552 0-1 .448-1 1v3c0 .552.448 1 1 1h1v1c0 .552.448 1 1 1h15c.552 0 1-.448 1-1v-1h1z"></path>
                    </svg>
                    AI उत्तर:
                </div>
                <div id="aiResponseContent" class="ai-response-content"></div>
            </div>

            <button id="downloadBtn" class="download-pdf-btn" onclick="downloadPDF()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7,10 12,15 17,10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                PDF मा डाउनलोड गर्नुहोस्
            </button>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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

            // Filter Functionality
            const filterButtons = document.querySelectorAll('.filter-btn');
            const courseTypeSections = document.querySelectorAll('.course-type');
            const subjectBoxes = document.querySelectorAll('.subject-box');
            const resultsSummary = document.getElementById('resultsSummary');

            function updateResultsSummary(filterType) {
                let visibleSolutions = 0;
                let visibleSubjects = 0;

                subjectBoxes.forEach(subjectBox => {
                    if (!subjectBox.classList.contains('hidden')) {
                        visibleSubjects++;
                        const visibleCourseTypes = subjectBox.querySelectorAll('.course-type:not(.hidden)');
                        visibleCourseTypes.forEach(courseType => {
                            const solutions = courseType.querySelectorAll('.question-item');
                            visibleSolutions += solutions.length;
                        });
                    }
                });

                if (visibleSolutions > 0) {
                    const filterText = filterType === 'all' ? 'all' : filterType.toLowerCase();
                    resultsSummary.innerHTML = `
                        Showing <strong>${visibleSolutions}</strong> ${filterText} solutions across 
                        <strong>${visibleSubjects}</strong> subjects
                    `;
                    resultsSummary.style.display = 'block';
                } else {
                    resultsSummary.style.display = 'none';
                }
            }

            function filterSolutions(filterType) {
                courseTypeSections.forEach(section => {
                    const courseType = section.getAttribute('data-course-type');
                    
                    if (filterType === 'all') {
                        section.classList.remove('hidden');
                    } else if (filterType === courseType) {
                        section.classList.remove('hidden');
                    } else {
                        section.classList.add('hidden');
                    }
                });

                // Hide subject boxes that have no visible course types
                subjectBoxes.forEach(subjectBox => {
                    const visibleCourseTypes = subjectBox.querySelectorAll('.course-type:not(.hidden)');
                    if (visibleCourseTypes.length === 0) {
                        subjectBox.classList.add('hidden');
                    } else {
                        subjectBox.classList.remove('hidden');
                    }
                });

                updateResultsSummary(filterType);

                // Show no results message if nothing is visible
                const visibleSubjects = document.querySelectorAll('.subject-box:not(.hidden)');
                const existingNoResults = document.querySelector('.no-results.filter-message');
                
                if (visibleSubjects.length === 0 && !existingNoResults) {
                    const noResultsDiv = document.createElement('div');
                    noResultsDiv.className = 'no-results filter-message';
                    noResultsDiv.innerHTML = `
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <p>No ${filterType === 'all' ? '' : filterType.toLowerCase() + ' '}solutions found.</p>
                    `;
                    document.querySelector('main').appendChild(noResultsDiv);
                } else if (visibleSubjects.length > 0 && existingNoResults) {
                    existingNoResults.remove();
                }
            }

            // Add event listeners to filter buttons
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filterType = this.getAttribute('data-filter');
                    
                    // Update active state
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Apply filter
                    filterSolutions(filterType);
                    
                    // Announce to screen readers
                    const announcement = document.createElement('div');
                    announcement.setAttribute('aria-live', 'polite');
                    announcement.setAttribute('aria-atomic', 'true');
                    announcement.className = 'sr-only';
                    announcement.textContent = `Filtered to show ${filterType === 'all' ? 'all' : filterType} solutions`;
                    document.body.appendChild(announcement);
                    
                    setTimeout(() => {
                        document.body.removeChild(announcement);
                    }, 1000);
                });
            });

            // Initialize results summary
            updateResultsSummary('all');

            // Add loading state for buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.getAttribute('download') !== null) {
                        const originalText = this.innerHTML;
                        this.innerHTML = `
                            <div class="loading"></div>
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

        // AI Chatbot Functions
        function getAISolution() {
            const question = document.getElementById("userQuestion").value;
            const responseBox = document.getElementById("aiResponse");
            const responseContent = document.getElementById("aiResponseContent");
            const downloadBtn = document.getElementById("downloadBtn");
            const submitBtn = document.getElementById("aiSubmitBtn");

            if (!question.trim()) {
                alert("कृपया प्रश्न टाइप गर्नुहोस्।");
                return;
            }

            // Show loading state
            responseBox.classList.add('show');
            responseContent.innerHTML = "कृपया पर्खनुहोस्... AI बाट समाधान ल्याउँदैछौं...";
            downloadBtn.classList.remove('show');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="loading"></div>
                Processing...
            `;

            fetch('../UploadsSolution/get_ai_solution.php', {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "question=" + encodeURIComponent(question)
            })
            .then(response => response.text())
            .then(data => {
                responseContent.innerHTML = data;
                downloadBtn.classList.add('show');
            })
            .catch(error => {
                responseContent.innerHTML = "AI उत्तर ल्याउन सकिएन। कृपया पछि फेरि प्रयास गर्नुहोस्।";
                console.error(error);
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4"></path>
                        <path d="M21 12c.552 0 1-.448 1-1V8c0-.552-.448-1-1-1h-1V6c0-.552-.448-1-1-1H4c-.552 0-1 .448-1 1v1H2c-.552 0-1 .448-1 1v3c0 .552.448 1 1 1h1v1c0 .552.448 1 1 1h15c.552 0 1-.448 1-1v-1h1z"></path>
                    </svg>
                    AI बाट समाधान हेर्नुहोस्
                `;
            });
        }

        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const text = document.getElementById("aiResponseContent").innerText;
            
            // Add some basic formatting
            const splitText = doc.splitTextToSize(text, 180);
            doc.text(splitText, 10, 10);
            doc.save("AI_Solution.pdf");
        }

        // Add CSS for screen reader only content
        const style = document.createElement('style');
        style.textContent = `
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