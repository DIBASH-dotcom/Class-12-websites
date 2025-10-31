<?php
require '../database.php';

// Fetch all provinces
$provinceResult = $con->query("SELECT DISTINCT province FROM solutions ORDER BY province ASC");
$provinces = [];
if ($provinceResult && $provinceResult->num_rows > 0) {
    while ($row = $provinceResult->fetch_assoc()) {
        $provinces[] = $row['province'];
    }
}

// Selected province
$selectedProvince = isset($_GET['province']) ? $_GET['province'] : '';

// Fetch all solutions
$result = $con->query("SELECT * FROM solutions ORDER BY subject, course_type, year DESC, id DESC");

// Group solutions by subject and course_type
$solutionsBySubject = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subject = $row['subject'];
        $courseType = $row['course_type'];
        if (!isset($solutionsBySubject[$subject])) {
            $solutionsBySubject[$subject] = [
                'Technical' => [],
                'Non-Technical' => []
            ];
        }
        $solutionsBySubject[$subject][$courseType][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solutions Dashboard | Student Portal</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="canonical" href="https://dibashmagar123.com.np/UploadsSolution/province.php" />

<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --secondary: #64748b;
    --success: #059669;
    --success-dark: #047857;
    --technical: #7c3aed;
    --non-technical: #059669;
    --light: #f8fafc;
    --dark: #1e293b;
    --gray: #64748b;
    --light-gray: #e2e8f0;
    --white: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --radius: 8px;
    --radius-lg: 12px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--dark);
    background-color: #f1f5f9;
    min-height: 100vh;
}

/* Header & Navigation */
.header {
    background: var(--white);
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 70px;
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
    font-size: 1.4rem;
    color: var(--primary);
}

.logo img {
    width: 40px;
    height: 40px;
    border-radius: var(--radius);
}

.mobile-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--dark);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    transition: background 0.2s;
}

.mobile-toggle:hover {
    background: var(--light-gray);
}

.nav {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.nav a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    text-decoration: none;
    color: var(--gray);
    font-weight: 500;
    border-radius: var(--radius);
    transition: all 0.2s;
}

.nav a:hover {
    color: var(--primary);
    background: var(--light);
}

.mobile-close-btn {
    display: none;
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--gray);
    cursor: pointer;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    align-items: center;
    justify-content: center;
}

.mobile-close-btn:hover {
    background: var(--light-gray);
}

.nav-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99;
}

/* Main Content */
.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 1.5rem;
    text-align: center;
}

/* Search and Filters */
.search-filters {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.search-container {
    margin-bottom: 1.5rem;
    position: relative;
}

.search-container i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
}

#solutionSearch {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 1px solid var(--light-gray);
    border-radius: var(--radius);
    font-size: 1rem;
    transition: border 0.2s;
}

#solutionSearch:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.filter-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--dark);
}

.filter-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--light-gray);
    border-radius: var(--radius);
    font-size: 1rem;
    background: var(--white);
    cursor: pointer;
}

.filter-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.filter-controls {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1.5rem;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border: 2px solid var(--light-gray);
    background: var(--white);
    color: var(--gray);
    border-radius: var(--radius);
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.filter-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.filter-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--white);
}

.filter-btn.technical.active {
    background: var(--technical);
    border-color: var(--technical);
}

.filter-btn.non-technical.active {
    background: var(--non-technical);
    border-color: var(--non-technical);
}

/* Results Summary */
.results-summary {
    background: var(--white);
    padding: 1rem 1.5rem;
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    font-weight: 500;
    color: var(--gray);
}

/* Subjects Container */
.subjects-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.subject-box {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.subject-header {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white);
    padding: 1.5rem;
}

.subject-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.subject-stats {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.stat-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.2);
}

.subject-content {
    padding: 1.5rem;
}

.course-type {
    margin-bottom: 2rem;
}

.course-type:last-child {
    margin-bottom: 0;
}

.course-type-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--light-gray);
}

.course-type-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--dark);
}

.course-type-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--white);
}

.course-type-badge.technical {
    background: var(--technical);
}

.course-type-badge.non-technical {
    background: var(--non-technical);
}

.solutions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}

.question-item {
    background: var(--white);
    border: 1px solid var(--light-gray);
    border-radius: var(--radius);
    padding: 1.5rem;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
}

.question-item:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.question-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.question-desc {
    color: var(--gray);
    margin-bottom: 1.25rem;
    line-height: 1.5;
    font-size: 0.9rem;
    flex-grow: 1;
}

.question-actions {
    display: flex;
    gap: 0.75rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: var(--radius);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s;
    flex: 1;
    justify-content: center;
}

.btn-view {
    background: var(--primary);
    color: var(--white);
    border: 1px solid var(--primary);
}

.btn-view:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-download {
    background: var(--success);
    color: var(--white);
    border: 1px solid var(--success);
}

.btn-download:hover {
    background: var(--success-dark);
    border-color: var(--success-dark);
    transform: translateY(-1px);
}

.no-results {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--gray);
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
}

.no-results i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-results p {
    font-size: 1.125rem;
}

.hidden {
    display: none !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-content {
        padding: 0 1rem;
    }
    
    .mobile-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .nav {
        position: fixed;
        top: 0;
        right: -300px;
        width: 300px;
        height: 100vh;
        background: var(--white);
        flex-direction: column;
        align-items: flex-start;
        padding: 4rem 1.5rem 1.5rem;
        gap: 0;
        box-shadow: var(--shadow-lg);
        transition: right 0.3s ease;
        z-index: 100;
    }
    
    .nav.active {
        right: 0;
    }
    
    .nav a {
        width: 100%;
        padding: 1rem;
        border-radius: var(--radius);
        margin-bottom: 0.5rem;
    }
    
    .mobile-close-btn {
        display: flex;
    }
    
    .nav-overlay.active {
        display: block;
    }
    
    .page-title {
        font-size: 1.8rem;
    }
    
    .filter-row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-controls {
        flex-direction: column;
    }
    
    .filter-btn {
        justify-content: center;
    }
    
    .solutions-grid {
        grid-template-columns: 1fr;
    }
    
    .question-actions {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .header-content {
        padding: 0 0.75rem;
    }
    
    .logo {
        font-size: 1.2rem;
    }
    
    .logo img {
        width: 35px;
        height: 35px;
    }
    
    .container {
        padding: 0 0.75rem;
    }
    
    .page-title {
        font-size: 1.6rem;
    }
    
    .search-filters {
        padding: 1.25rem;
    }
    
    .subject-header {
        padding: 1.25rem;
    }
    
    .subject-content {
        padding: 1.25rem;
    }
    
    .question-item {
        padding: 1.25rem;
    }
}
</style>
</head>
<body>
<div class="header">
    <div class="header-content">
        <div class="logo">
            <img src="../images/hello.png" alt="Student Portal Logo">
            <span>Student Portal</span>
        </div>
        
        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation menu">
            <i class="fas fa-bars"></i>
        </button>
        
        <nav class="nav" id="mainNav">
            <button class="mobile-close-btn" id="mobileClose" aria-label="Close navigation menu">
                <i class="fas fa-times"></i>
            </button>
            <a href="https://dibashmagar123.com.np/"><i class="fa fa-home"></i> Home</a>
<a href="https://dibashmagar123.com.np/UploadsQuestion/province.php"><i class="fa fa-question-circle"></i> Questions Province Wise</a>
<a href="https://dibashmagar123.com.np/UploadsSolution/province.php"><i class="fa fa-check-circle"></i> Solutions Province Wise</a>
<a href="https://dibashmagar123.com.np/UploadNotes/View_Notes.php"><i class="fa fa-book"></i> Notes</a>
<a href="https://dibashmagar123.com.np/Pages/About.php"><i class="fa fa-info-circle"></i> About Us</a>
<a href="https://dibashmagar123.com.np/Pages/Contact.php"><i class="fa fa-phone"></i> Contact Us</a>

        </nav>
        
        <div class="nav-overlay" id="navOverlay"></div>
    </div>
</div>

<div class="container">
    <h1 class="page-title">Solutions Dashboard</h1>
    
    <div class="search-filters">
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="solutionSearch" placeholder="Search solutions by name..." aria-label="Search solutions">
        </div>
        
        <div class="filter-row">
            <div class="filter-group">
                <label for="provinceSelect">Filter by Province:</label>
                <select id="provinceSelect">
                    <option value="all">All Provinces</option>
                    <?php foreach($provinces as $province): ?>
                        <option value="<?php echo htmlspecialchars($province); ?>">
                            <?php echo htmlspecialchars($province); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="yearSelect">Filter by Year:</label>
                <select id="yearSelect">
                    <option value="all">All Years</option>
                </select>
            </div>
        </div>
        
        <div class="filter-controls">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-layer-group"></i> All Solutions
            </button>
            <button class="filter-btn" data-filter="Technical">
                <i class="fas fa-laptop-code"></i> Technical Solutions
            </button>
            <button class="filter-btn" data-filter="Non-Technical">
                <i class="fas fa-book-open"></i> Non-Technical Solutions
            </button>
        </div>
    </div>

    <div class="results-summary" id="resultsSummary"></div>

    <?php if (!empty($solutionsBySubject)): ?>
        <div class="subjects-container">
            <?php foreach ($solutionsBySubject as $subject => $courseTypes): ?>
                <div class="subject-box" data-subject="<?php echo htmlspecialchars($subject); ?>">
                    <div class="subject-header">
                        <h2 class="subject-title">
                            <i class="fas fa-folder-open"></i> <?php echo htmlspecialchars($subject); ?>
                        </h2>
                        <div class="subject-stats">
                            <?php 
                            $techCount = count($courseTypes['Technical']);
                            $nonTechCount = count($courseTypes['Non-Technical']);
                            $totalCount = $techCount + $nonTechCount;
                            ?>
                            <?php if ($techCount > 0): ?>
                                <span class="stat-badge">
                                    <i class="fas fa-laptop-code"></i> <?php echo $techCount; ?> Technical
                                </span>
                            <?php endif; ?>
                            <?php if ($nonTechCount > 0): ?>
                                <span class="stat-badge">
                                    <i class="fas fa-book-open"></i> <?php echo $nonTechCount; ?> Non-Technical
                                </span>
                            <?php endif; ?>
                            <span class="stat-badge">
                                <i class="fas fa-file-alt"></i> <?php echo $totalCount; ?> Total
                            </span>
                        </div>
                    </div>

                    <div class="subject-content">
                        <?php foreach (['Technical', 'Non-Technical'] as $type): ?>
                            <?php if (!empty($courseTypes[$type])): ?>
                                <div class="course-type" data-course-type="<?php echo $type; ?>">
                                    <div class="course-type-header">
                                        <h3 class="course-type-title"><?php echo $type; ?> Solutions</h3>
                                        <span class="course-type-badge <?php echo strtolower($type); ?>">
                                            <?php echo count($courseTypes[$type]); ?> Files
                                        </span>
                                    </div>
                                    <div class="solutions-grid">
                                        <?php foreach ($courseTypes[$type] as $solution): ?>
                                            <div class="question-item" 
                                                 data-province="<?php echo htmlspecialchars($solution['province']); ?>" 
                                                 data-year="<?php echo htmlspecialchars($solution['year']); ?>">
                                                <h3 class="question-title">
                                                    <i class="fas fa-file-pdf"></i> <?php echo htmlspecialchars($solution['name']); ?>
                                                </h3>
                                                <?php if (!empty($solution['description'])): ?>
                                                    <div class="question-desc"><?php echo nl2br(htmlspecialchars($solution['description'])); ?></div>
                                                <?php endif; ?>
                                                <div class="question-actions">
                                                    <?php $filePath = '../UploadsSolution/uploads/' . rawurlencode(basename($solution['file_path'])); ?>
                                                    <a class="btn btn-view" href="<?php echo $filePath; ?>" target="_blank">
                                                        <i class="fas fa-eye"></i> View PDF
                                                    </a>
                                                    <a class="btn btn-download" href="<?php echo $filePath; ?>" download>
                                                        <i class="fas fa-download"></i> Download
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
            <i class="fas fa-inbox"></i>
            <p>No solutions found.</p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile navigation toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const mainNav = document.getElementById('mainNav');
    const mobileClose = document.getElementById('mobileClose');
    const navOverlay = document.getElementById('navOverlay');
    
    function toggleNav() {
        mainNav.classList.toggle('active');
        navOverlay.classList.toggle('active');
        document.body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
    }
    
    mobileToggle.addEventListener('click', toggleNav);
    mobileClose.addEventListener('click', toggleNav);
    navOverlay.addEventListener('click', toggleNav);
    
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const subjectBoxes = document.querySelectorAll('.subject-box');
    const resultsSummary = document.getElementById('resultsSummary');
    const searchInput = document.getElementById('solutionSearch');
    const provinceSelect = document.getElementById('provinceSelect');
    const yearSelect = document.getElementById('yearSelect');

    function populateYearDropdown() {
        const yearsSet = new Set();
        const activeSubjects = document.querySelectorAll('.subject-box:not(.hidden)');
        activeSubjects.forEach(subjectBox => {
            const items = subjectBox.querySelectorAll('.question-item');
            items.forEach(item => {
                if(item.style.display !== 'none') {
                    const year = item.getAttribute('data-year');
                    if(year) yearsSet.add(year);
                }
            });
        });
        yearSelect.innerHTML = '<option value="all">All Years</option>';
        Array.from(yearsSet).sort().forEach(year => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        });
    }

    function filterSolutions() {
        const searchQuery = searchInput.value.trim().toLowerCase();
        const province = provinceSelect.value;
        const year = yearSelect.value;
        const filterType = document.querySelector('.filter-btn.active').getAttribute('data-filter');

        let visibleSolutions = 0;

        subjectBoxes.forEach(subjectBox => {
            let subjectVisible = false;
            const courseTypeSections = subjectBox.querySelectorAll('.course-type');
            courseTypeSections.forEach(section => {
                const sectionType = section.getAttribute('data-course-type');
                const solutionItems = section.querySelectorAll('.question-item');

                solutionItems.forEach(item => {
                    const name = item.querySelector('.question-title').textContent.toLowerCase();
                    const solutionProvince = item.getAttribute('data-province');
                    const solutionYear = item.getAttribute('data-year');
                    const matchesFilter = (filterType === 'all' || filterType === sectionType);
                    const matchesSearch = name.includes(searchQuery);
                    const matchesProvince = (province === 'all' || province === solutionProvince);
                    const matchesYear = (year === 'all' || year === solutionYear);

                    if(matchesFilter && matchesSearch && matchesProvince && matchesYear){
                        item.style.display = 'flex';
                        subjectVisible = true;
                        visibleSolutions++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                section.style.display = Array.from(solutionItems).some(i => i.style.display !== 'none') ? 'block' : 'none';
            });
            subjectBox.style.display = subjectVisible ? 'block' : 'none';
        });

        // Populate year dropdown after search & province
        populateYearDropdown();

        resultsSummary.innerHTML = visibleSolutions > 0 ? 
            `Showing <strong>${visibleSolutions}</strong> solution${visibleSolutions !== 1 ? 's' : ''}` : 
            'No solutions match your search criteria';
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterSolutions();
        });
    });

    searchInput.addEventListener('input', filterSolutions);
    provinceSelect.addEventListener('change', filterSolutions);
    yearSelect.addEventListener('change', filterSolutions);

    filterSolutions(); // initialize
});
</script>
</body>
</html>