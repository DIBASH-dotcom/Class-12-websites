<?php
require '../database.php'; 

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$allowed_filters = ['all', 'technical', 'non-technical'];
if (!in_array($filter, $allowed_filters)) $filter = 'all';

if ($filter === 'technical') {
    $sql = "SELECT * FROM notes WHERE course_type = 'Technical' ORDER BY subject, faculty, id DESC";
} elseif ($filter === 'non-technical') {
    $sql = "SELECT * FROM notes WHERE course_type = 'Non-Technical' ORDER BY subject, faculty, id DESC";
} else {
    $sql = "SELECT * FROM notes ORDER BY subject, faculty, id DESC";
}

$result = $con->query($sql);

// Group notes by subject+faculty
$grouped_notes = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $key = $row['subject'] . '||' . $row['faculty'];  // Unique key per subject+faculty
        if (!isset($grouped_notes[$key])) {
            $grouped_notes[$key] = [
                'subject' => $row['subject'],
                'faculty' => $row['faculty'],
                'notes' => []
            ];
        }
        $grouped_notes[$key]['notes'][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Dashboard | Student Portal</title>
    <meta name="description" content="Browse and access notes organized by subject and faculty with technical/non-technical filtering">
    <link rel="canonical" href="https://dibashmagar123.com.np/UploadNotes/View_Notes.php" />

    
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
        }

        .group-div {
            background: var(--surface-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .group-div.hidden {
            display: none;
        }

        .group-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .group-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .subject-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .faculty-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .group-stats {
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

        /* Notes Grid */
        .notes-container {
            padding: 2rem;
        }

        .notes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .note-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            transition: all 0.2s;
        }

        .note-card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .note-header {
            margin-bottom: 1rem;
        }

        .note-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .note-meta {
            display: flex;
            gap: 0.75rem;
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

        .note-description {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.5;
            font-size: 0.875rem;
            white-space: pre-line;
        }

        .note-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        /* Action Buttons */
        .action-btn {
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

        .view-btn {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .view-btn:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .download-btn {
            background-color: var(--success-color);
            color: white;
            border-color: var(--success-color);
        }

        .download-btn:hover {
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

            .notes-container {
                padding: 1rem;
            }

            .group-header {
                padding: 1rem;
            }

            .group-title {
                font-size: 1.25rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .notes-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .note-actions {
                flex-direction: column;
            }

            .action-btn {
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

            .note-card {
                padding: 1rem;
            }
        }

        /* Focus Styles for Accessibility */
        .action-btn:focus,
        .nav-links a:focus,
        .nav-toggle:focus,
        .filter-btn:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Print Styles */
        @media print {
            #header,
            .filter-controls {
                display: none;
            }

            .group-div {
                box-shadow: none;
                border: 1px solid var(--border-color);
                break-inside: avoid;
            }

            .action-btn {
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
                    <a href="../index.php"><i class="fas fa-home"></i> Home</a>
    <a href="../UploadsQuestion/province.php"><i class="fas fa-question-circle"></i> Questions Province Wise</a>
    <a href="../UploadsSolution/province.php"><i class="fas fa-check-circle"></i> Solutions Province Wise</a>
    <a href="../UploadNotes/View_Notes.php"><i class="fas fa-book"></i> Notes</a>
    <a href="../Pages/About.php"><i class="fas fa-info-circle"></i> About Us</a>
    <a href="../Pages/Contact.php"><i class="fas fa-phone"></i> Contact Us</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="page-header">
            <h2 class="page-title">Notes Dashboard</h2>
            <p class="page-subtitle">
                Access comprehensive study notes organized by subject and faculty with technical and non-technical filtering options.
            </p>
        </div>

        <div class="filter-controls">
            <button class="filter-btn active" data-filter="all" id="filterAll">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 6v6l4 2"></path>
                </svg>
                All Notes
            </button>
            <button class="filter-btn technical" data-filter="Technical" id="filterTechnical">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                Technical Notes
            </button>
            <button class="filter-btn non-technical" data-filter="Non-Technical" id="filterNonTechnical">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                Non-Technical Notes
            </button>
        </div>

        <div class="results-summary" id="resultsSummary"></div>

        <?php if (!empty($grouped_notes)): ?>
            <div class="subjects-container">
                <?php foreach ($grouped_notes as $group): ?>
                    <div class="group-div" data-subject="<?= htmlspecialchars($group['subject']) ?>" data-faculty="<?= htmlspecialchars($group['faculty']) ?>">
                        <div class="group-header">
                            <div class="group-title">
                                <div class="subject-name">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                    <?= htmlspecialchars($group['subject']) ?>
                                </div>
                                <span class="faculty-badge"><?= htmlspecialchars($group['faculty']) ?> Faculty</span>
                            </div>
                            <div class="group-stats">
                                <?php 
                                $techCount = 0;
                                $nonTechCount = 0;
                                foreach ($group['notes'] as $note) {
                                    if ($note['course_type'] === 'Technical') {
                                        $techCount++;
                                    } else {
                                        $nonTechCount++;
                                    }
                                }
                                ?>
                                <?php if ($techCount > 0): ?>
                                    <span class="stat-badge"><?= $techCount ?> Technical</span>
                                <?php endif; ?>
                                <?php if ($nonTechCount > 0): ?>
                                    <span class="stat-badge"><?= $nonTechCount ?> Non-Technical</span>
                                <?php endif; ?>
                                <span class="resource-count"><?= count($group['notes']) ?> total notes</span>
                            </div>
                        </div>

                        <div class="notes-container">
                            <div class="notes-grid">
                                <?php foreach ($group['notes'] as $note): ?>
                                    <div class="note-card" data-course-type="<?= htmlspecialchars($note['course_type']) ?>">
                                        <div class="note-header">
                                            <div class="note-title"><?= htmlspecialchars($note['name']) ?></div>
                                            <div class="note-meta">
                                                <span class="course-type-badge <?= strtolower(str_replace('-', '-', $note['course_type'])) ?>">
                                                    <?= htmlspecialchars($note['course_type']) ?>
                                                </span>
                                                <span class="meta-item">Province: <?= htmlspecialchars($note['province']) ?></span>
                                                <span class="meta-item">Year: <?= htmlspecialchars($note['year']) ?></span>
                                                <span class="meta-item">ID: <?= $note['id'] ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($note['description'])): ?>
                                            <div class="note-description"><?= nl2br(htmlspecialchars($note['description'])) ?></div>
                                        <?php endif; ?>

                                        <div class="note-actions">
                                            <a class="action-btn view-btn" href="<?= htmlspecialchars($note['file_path']) ?>" target="_blank" rel="noopener noreferrer" aria-label="View <?= htmlspecialchars($note['name']) ?>">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                View
                                            </a>
                                            <a class="action-btn download-btn" href="?download=<?= urlencode(basename($note['file_path'])) ?>" aria-label="Download <?= htmlspecialchars($note['name']) ?>">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7,10 12,15 17,10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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
                <p>❌ No notes uploaded yet.</p>
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

            // Filter Functionality
            const filterButtons = document.querySelectorAll('.filter-btn');
            const noteCards = document.querySelectorAll('.note-card');
            const subjectGroups = document.querySelectorAll('.group-div');
            const resultsSummary = document.getElementById('resultsSummary');

            function updateResultsSummary(filterType) {
                let visibleNotes = 0;
                let visibleSubjects = 0;

                subjectGroups.forEach(group => {
                    if (!group.classList.contains('hidden')) {
                        visibleSubjects++;
                        const visibleCards = group.querySelectorAll('.note-card:not(.hidden)');
                        visibleNotes += visibleCards.length;
                    }
                });

                if (visibleNotes > 0) {
                    const filterText = filterType === 'all' ? 'all' : filterType.toLowerCase();
                    resultsSummary.innerHTML = `
                        Showing <strong>${visibleNotes}</strong> ${filterText} notes across 
                        <strong>${visibleSubjects}</strong> subject groups
                    `;
                    resultsSummary.style.display = 'block';
                } else {
                    resultsSummary.style.display = 'none';
                }
            }

            function filterNotes(filterType) {
                // Filter individual note cards
                noteCards.forEach(card => {
                    const courseType = card.getAttribute('data-course-type');
                    
                    if (filterType === 'all') {
                        card.classList.remove('hidden');
                    } else if (filterType === courseType) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Hide subject groups that have no visible notes
                subjectGroups.forEach(group => {
                    const visibleCards = group.querySelectorAll('.note-card:not(.hidden)');
                    if (visibleCards.length === 0) {
                        group.classList.add('hidden');
                    } else {
                        group.classList.remove('hidden');
                    }
                });

                updateResultsSummary(filterType);

                // Show no results message if nothing is visible
                const visibleGroups = document.querySelectorAll('.group-div:not(.hidden)');
                const existingNoResults = document.querySelector('.no-results.filter-message');
                
                if (visibleGroups.length === 0 && !existingNoResults) {
                    const noResultsDiv = document.createElement('div');
                    noResultsDiv.className = 'no-results filter-message';
                    noResultsDiv.innerHTML = `
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <p>No ${filterType === 'all' ? '' : filterType.toLowerCase() + ' '}notes found.</p>
                    `;
                    document.querySelector('main').appendChild(noResultsDiv);
                } else if (visibleGroups.length > 0 && existingNoResults) {
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
                    filterNotes(filterType);
                    
                    // Announce to screen readers
                    const announcement = document.createElement('div');
                    announcement.setAttribute('aria-live', 'polite');
                    announcement.setAttribute('aria-atomic', 'true');
                    announcement.className = 'sr-only';
                    announcement.textContent = `Filtered to show ${filterType === 'all' ? 'all' : filterType} notes`;
                    document.body.appendChild(announcement);
                    
                    setTimeout(() => {
                        document.body.removeChild(announcement);
                    }, 1000);
                });
            });

            // Initialize results summary
            updateResultsSummary('all');

            // Add loading state for buttons
            document.querySelectorAll('.action-btn').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.classList.contains('download-btn')) {
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
                hseight: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }
            .note-card.hidden {
                display: none;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>