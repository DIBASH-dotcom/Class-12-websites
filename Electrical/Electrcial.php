<?php
require '../database.php'; // DB connection

$faculty = isset($_GET['faculty']) ? $_GET['faculty'] : 'Electrical ';

// Fetch data function
function fetchFilteredData($con, $table, $faculty) {
    $stmt = $con->prepare("SELECT * FROM $table WHERE faculty = ?");
    $stmt->bind_param("s", $faculty);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch distinct years
function fetchYears($con, $faculty) {
    $stmt = $con->prepare("SELECT DISTINCT year FROM notes WHERE faculty=? 
                           UNION 
                           SELECT DISTINCT year FROM questions WHERE faculty=? 
                           UNION 
                           SELECT DISTINCT year FROM solutions WHERE faculty=? 
                           ORDER BY year ASC");
    $stmt->bind_param("sss", $faculty, $faculty, $faculty);
    $stmt->execute();
    $result = $stmt->get_result();
    $years = [];
    while ($row = $result->fetch_assoc()) $years[] = $row['year'];
    $stmt->close();
    return $years;
}

// Fetch grouped resources
function displayGroupedResources($con, $faculty) {
    $notes = fetchFilteredData($con, 'notes', $faculty);
    $questions = fetchFilteredData($con, 'questions', $faculty);
    $solutions = fetchFilteredData($con, 'solutions', $faculty);

    $allResources = array_merge(
        array_map(fn($r)=>['type'=>'note']+$r, $notes),
        array_map(fn($r)=>['type'=>'question']+$r, $questions),
        array_map(fn($r)=>['type'=>'solution']+$r, $solutions)
    );

    $grouped = [];
    foreach($allResources as $r) $grouped[$r['subject']][] = $r;
    ksort($grouped);

    if(!empty($grouped)){
        echo '<div class="subjects-container">';
        foreach($grouped as $subject=>$resources){
            echo '<div class="subject-group">';
            echo '<div class="subject-header"><h3 class="subject-title">'.htmlspecialchars($subject).'</h3>';
            echo '<span class="resource-count">'.count($resources).' resource'.(count($resources)>1?'s':'').'</span></div>';
            echo '<div class="resources-grid">';
            foreach($resources as $r){
                $resourceType = strtolower($r['course_type']);
                $fileBase = $r['type']=='note'?'../UploadNotes/':($r['type']=='question'?'../UploadsQuestion/':'../UploadsSolution/');
                $filePath = $fileBase.htmlspecialchars($r['file_path']);
                echo '<div class="resource-card" data-type="'.$r['type'].'" data-year="'.$r['year'].'" data-course="'.htmlspecialchars($r['course_type']).'" data-subject-type="'.$resourceType.'">';
                echo '<div class="resource-header"><h4 class="resource-title">'.htmlspecialchars($r['name']).'</h4>';
                $badgeColor = $r['type']=='note'?'#3b82f6':($r['type']=='question'?'#f59e0b':'#10b981');
                echo '<span class="resource-badge" style="background:'.$badgeColor.';">'.ucfirst($r['type']).'</span></div>';
                echo '<div class="resource-meta">';
                echo '<span class="meta-item">Course: '.htmlspecialchars($r['course_type']).'</span>';
                echo '<span class="meta-item">Province: '.htmlspecialchars($r['province']).'</span>';
                echo '<span class="meta-item">Year: '.htmlspecialchars($r['year']).'</span></div>';
                if(!empty($r['description'])) echo '<div class="resource-description"><p>'.htmlspecialchars($r['description']).'</p></div>';
                echo '<div class="resource-actions">';
                echo '<a href="'.$filePath.'" target="_blank" class="btn btn-view"><i class="fas fa-eye"></i> View</a> ';
                echo '<a href="'.$filePath.'" download class="btn btn-download"><i class="fas fa-download"></i> Download</a></div></div>';
            }
            echo '</div></div>';
        }
        echo '</div>';
    } else echo '<div class="no-results">No resources found.</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($faculty); ?> Resources</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="canonical" href="https://dibashmagar123.com.np/Electrical/Electrical.php" />

<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-light: #dbeafe;
    --secondary: #7c3aed;
    --success: #059669;
    --warning: #d97706;
    --danger: #dc2626;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --white: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
    color: var(--gray-900);
    line-height: 1.6;
    overflow-x: hidden;
}

.container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Enhanced header with better mobile navigation */
.header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    padding: 1rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: var(--shadow-xl);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
    font-size: 1.5rem;
    z-index: 1002;
}

.logo img {
    height: 40px;
    width: auto;
}

/* Improved mobile toggle button */
.mobile-toggle {
    display: none;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.75rem;
    border-radius: var(--radius);
    transition: var(--transition);
    z-index: 1003;
    position: relative;
    backdrop-filter: blur(10px);
}

.mobile-toggle:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

.mobile-toggle:focus {
    outline: 2px solid rgba(255, 255, 255, 0.5);
    outline-offset: 2px;
}

/* Desktop navigation styling */
.nav {
    display: flex;
    gap: 0.25rem;
}

.nav a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    padding: 0.75rem 1rem;
    border-radius: var(--radius);
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.nav a:hover, .nav a.active {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Mobile navigation overlay */
.nav-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    z-index: 998;
    opacity: 0;
    transition: var(--transition);
    backdrop-filter: blur(4px);
}

.nav-overlay.active {
    display: block;
    opacity: 1;
}

/* Enhanced mobile close button inside sidebar */
.mobile-close-btn {
    display: none;
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: var(--gray-700);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.75rem;
    border-radius: var(--radius);
    transition: var(--transition);
    z-index: 1001;
    backdrop-filter: blur(10px);
}

.mobile-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

.mobile-close-btn:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* Main Content */
.main {
    padding: 2.5rem 0;
    min-height: calc(100vh - 200px);
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.025em;
}

.breadcrumb {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    color: var(--gray-500);
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
    flex-wrap: wrap;
}

.breadcrumb a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
}

.breadcrumb a:hover {
    color: var(--primary-dark);
    background: var(--primary-light);
}

/* Enhanced controls section with better mobile layout */
.controls {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
}

.search-container {
    display: grid;
    grid-template-columns: 1fr 200px 200px auto;
    gap: 1rem;
    margin-bottom: 2rem;
}

.search-box {
    position: relative;
}

.search-input {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius);
    font-size: 0.875rem;
    transition: var(--transition);
    background: var(--white);
    font-weight: 500;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-light);
    transform: translateY(-1px);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    font-size: 1rem;
}

.filter-controls {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    justify-content: center;
}

.filter-btn {
    padding: 0.875rem 1.5rem;
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius);
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-700);
}

.filter-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.filter-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.filter-btn.technical.active {
    background: var(--secondary);
    border-color: var(--secondary);
}

.filter-btn.non-technical.active {
    background: var(--success);
    border-color: var(--success);
}

/* Resources Section */
.resources-section {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
}

.section-header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.025em;
}

.results-count {
    font-size: 0.875rem;
    opacity: 0.9;
    background: rgba(255, 255, 255, 0.15);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-weight: 600;
}

.section-content {
    padding: 2rem;
}

/* Subjects Container */
.subjects-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.subject-group {
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: var(--transition);
    background: var(--white);
}

.subject-group:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-4px);
    border-color: var(--primary-light);
}

.subject-header {
    background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: var(--transition);
    border-bottom: 2px solid var(--gray-200);
    flex-wrap: wrap;
    gap: 1rem;
}

.subject-header:hover {
    background: linear-gradient(135deg, var(--primary-light), var(--primary-light));
}

.subject-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    letter-spacing: -0.025em;
}

.subject-title i {
    color: var(--primary);
    transition: var(--transition);
    font-size: 1.25rem;
}

.subject-header.collapsed .subject-title i {
    transform: rotate(-90deg);
}

.resource-count {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: var(--shadow-md);
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--gray-50), #ffffff);
}

/* Enhanced resource card styling */
.resource-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: var(--transition);
    border: 2px solid var(--gray-200);
}

.resource-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-light);
}

.resource-header {
    padding: 1.25rem;
    border-bottom: 2px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    background: linear-gradient(135deg, var(--gray-50), #ffffff);
    flex-wrap: wrap;
}

.resource-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    flex: 1;
    line-height: 1.4;
    letter-spacing: -0.025em;
    min-width: 0;
}

.resource-badge {
    padding: 0.5rem 0.875rem;
    border-radius: var(--radius);
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.resource-meta {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 500;
}

.meta-item i {
    width: 18px;
    color: var(--primary);
    text-align: center;
    font-size: 1rem;
}

.resource-description {
    padding: 1.25rem;
    border-top: 2px solid var(--gray-200);
    border-bottom: 2px solid var(--gray-200);
    font-size: 0.875rem;
    color: var(--gray-600);
    background: linear-gradient(135deg, var(--gray-50), #ffffff);
    line-height: 1.6;
    font-weight: 500;
}

.resource-description p {
    margin-top: 0.5rem;
}

.resource-actions {
    padding: 1.25rem;
    display: flex;
    gap: 0.75rem;
}

.btn {
    padding: 0.75rem 1rem;
    border-radius: var(--radius);
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: var(--transition);
    flex: 1;
    border: none;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.btn-view {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    box-shadow: var(--shadow-md);
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

.btn-download {
    background: linear-gradient(135deg, var(--success), #047857);
    color: white;
    box-shadow: var(--shadow-md);
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

/* No Results */
.no-results {
    text-align: center;
    padding: 3rem 2rem;
}

.no-results-content {
    max-width: 400px;
    margin: 0 auto;
}

.no-results i {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    color: var(--gray-300);
}

.no-results h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 0.75rem;
}

.no-results p {
    color: var(--gray-500);
    font-size: 1rem;
    font-weight: 500;
}

.no-resources {
    text-align: center;
    padding: 2rem;
    color: var(--gray-500);
    font-style: italic;
    font-weight: 500;
}

/* Footer */
.footer {
    background: linear-gradient(135deg, var(--gray-900), var(--gray-800));
    color: var(--gray-300);
    padding: 3rem 0 2rem;
    margin-top: 4rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
    color: var(--white);
    letter-spacing: -0.025em;
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: 0.75rem;
}

.footer-links a {
    color: var(--gray-400);
    text-decoration: none;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 0;
}

.footer-links a:hover {
    color: var(--white);
    transform: translateX(6px);
}

.copyright {
    text-align: center;
    padding-top: 2rem;
    border-top: 2px solid var(--gray-700);
    color: var(--gray-500);
    font-size: 0.875rem;
    font-weight: 500;
}

/* Enhanced responsive design with better mobile navigation */
@media (max-width: 1024px) {
    .resources-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
    
    .page-title {
        font-size: 2.25rem;
    }
    
    .search-container {
        grid-template-columns: 1fr 180px 180px auto;
    }
}

@media (max-width: 768px) {
    .mobile-toggle {
        display: block;
    }
    
    /* Enhanced mobile navigation sidebar */
    .nav {
        position: fixed;
        top: 0;
        right: -100%;
        width: 320px;
        height: 100vh;
        background: linear-gradient(135deg, var(--white), var(--gray-50));
        flex-direction: column;
        gap: 0;
        box-shadow: var(--shadow-xl);
        transition: var(--transition);
        padding: 5rem 0 2rem;
        z-index: 1000;
        border-left: 2px solid var(--gray-200);
        overflow-y: auto;
    }
    
    .nav.active {
        right: 0;
    }
    
    .nav a {
        color: var(--gray-700);
        padding: 1.25rem 2rem;
        border-radius: 0;
        border-bottom: 1px solid var(--gray-200);
        justify-content: flex-start;
        font-size: 1rem;
        font-weight: 600;
        transition: var(--transition);
    }
    
    .nav a:hover, .nav a.active {
        background: var(--primary-light);
        color: var(--primary);
        transform: none;
        padding-left: 2.5rem;
    }
    
    .mobile-close-btn {
        display: block;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .search-container {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filter-controls {
        justify-content: center;
    }
    
    .resource-actions {
        flex-direction: column;
    }
    
    .subject-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .resources-grid {
        grid-template-columns: 1fr;
        padding: 1.25rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .breadcrumb {
        justify-content: center;
        text-align: center;
    }
    
    .resource-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .resource-badge {
        align-self: flex-start;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 0.75rem;
    }
    
    .header {
        padding: 0.75rem 0;
    }
    
    .main {
        padding: 1.5rem 0;
    }
    
    .page-title {
        font-size: 1.75rem;
    }
    
    .controls {
        padding: 1.5rem;
    }
    
    .section-content {
        padding: 1.25rem;
    }
    
    .filter-btn {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }
    
    .nav {
        width: 280px;
    }
    
    .logo {
        font-size: 1.25rem;
    }
    
    .logo img {
        height: 32px;
    }
}

/* Enhanced animations and accessibility */
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

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.resource-card {
    animation: fadeInUp 0.5s ease forwards;
}

.resource-card:nth-child(2) { animation-delay: 0.1s; }
.resource-card:nth-child(3) { animation-delay: 0.2s; }
.resource-card:nth-child(4) { animation-delay: 0.3s; }

.subject-group {
    animation: slideIn 0.4s ease forwards;
}

/* Enhanced focus styles for better accessibility */
button:focus,
input:focus,
a:focus,
select:focus {
    outline: 3px solid var(--primary);
    outline-offset: 2px;
}

.mobile-toggle:focus,
.mobile-close-btn:focus {
    outline: 2px solid rgba(255, 255, 255, 0.8);
    outline-offset: 2px;
}

/* Loading state */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Collapsible content */
.collapsible-content {
    overflow: hidden;
    transition: max-height 0.4s ease;
}

.collapsible-content.collapsed {
    max-height: 0;
}

/* Print styles */
@media print {
    .header,
    .controls,
    .resource-actions,
    .footer {
        display: none !important;
    }
    
    .resource-card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid var(--gray-300);
    }
    
    body {
        background: white;
    }
}
</style>
</head>
<body>
<div class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
               <img src="../images/hello.png" alt="Student Portal Logo">
                <span>Student Portal</span>
            </div>
            
            <!-- Enhanced mobile toggle with better accessibility -->
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation menu">
                <i class="fas fa-bars" id="toggleIcon"></i>
            </button>
            
            <nav class="nav" id="mainNav" role="navigation">
                <!-- Added close button inside mobile sidebar -->
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
</div>

<div class="main">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?php echo htmlspecialchars($faculty); ?> Resources</h1>
            <div class="breadcrumb">
                <a href="https://dibashmagar123.com.np/"><i class="fa fa-home"></i> Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="https://dibashmagar123.com.np/UploadsQuestion/province.php">Resources</a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo htmlspecialchars($faculty); ?></span>
            </div>
        </div>
        
        <div class="controls">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="subjectSearch" class="search-input" placeholder="Search subject or resource name...">
                </div>
                <div class="search-box">
                    <i class="fas fa-book search-icon"></i>
                    <select id="resourceTypeDropdown" class="search-input">
                        <option value="all">All Resources</option>
                        <option value="note">Notes</option>
                        <option value="question">Questions</option>
                        <option value="solution">Solutions</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-calendar search-icon"></i>
                    <select id="yearSearch" class="search-input">
                        <option value="">All Years</option>
                        <?php
                        $years = fetchYears($con, $faculty);
                        foreach($years as $y) echo '<option value="'.$y.'">'.$y.'</option>';
                        ?>
                    </select>
                </div>
                <button id="searchBtn" class="btn btn-view"><i class="fas fa-filter"></i> Apply</button>
            </div>
            
            <div class="filter-controls">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-layer-group"></i> All
                </button>
                <button class="filter-btn technical" data-filter="technical">
                    <i class="fas fa-cogs"></i> Technical
                </button>
                <button class="filter-btn non-technical" data-filter="non-technical">
                    <i class="fas fa-pen-fancy"></i> Non-Technical
                </button>
            </div>
        </div>

        <section class="resources-section">
            <div class="section-header">
                <h2 class="section-title">Available Resources</h2>
                <span class="results-count" id="resultsCount">Loading resources...</span>
            </div>
            
            <div class="section-content">
                <?php displayGroupedResources($con, $faculty); ?>
            </div>
        </section>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About EduResources</h3>
                <p>Providing quality educational resources for students and educators worldwide. Access notes, questions, and solutions across multiple faculties.</p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                  <a href="https://dibashmagar123.com.np/"><i class="fa fa-home"></i> Home</a>
<a href="https://dibashmagar123.com.np/UploadsQuestion/province.php"><i class="fa fa-question-circle"></i> Questions Province Wise</a>
<a href="https://dibashmagar123.com.np/UploadsSolution/province.php"><i class="fa fa-check-circle"></i> Solutions Province Wise</a>
<a href="https://dibashmagar123.com.np/UploadNotes/View_Notes.php"><i class="fa fa-book"></i> Notes</a>
<a href="https://dibashmagar123.com.np/Pages/About.php"><i class="fa fa-info-circle"></i> About Us</a>
<a href="https://dibashmagar123.com.np/Pages/Contact.php"><i class="fa fa-phone"></i> Contact Us</a>

                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Connect With Us</h3>
                <ul class="footer-links">
                    <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                    <li><a href="#"><i class="fab fa-linkedin"></i> LinkedIn</a></li>
                </ul>
            </div>
        </div>
        
        <div class="copyright">
            &copy; 2024 EduResources. All rights reserved. | Empowering Education Through Technology
        </div>
    </div>
</footer>

<script>
const mobileToggle = document.getElementById('mobileToggle');
const mobileClose = document.getElementById('mobileClose');
const mainNav = document.getElementById('mainNav');
const navOverlay = document.getElementById('navOverlay');
const toggleIcon = document.getElementById('toggleIcon');

function toggleNav() {
    const isActive = mainNav.classList.contains('active');
    
    mainNav.classList.toggle('active');
    navOverlay.classList.toggle('active');
    document.body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
    
    if (mainNav.classList.contains('active')) {
        toggleIcon.className = 'fas fa-times';
        mobileToggle.setAttribute('aria-label', 'Close navigation menu');
    } else {
        toggleIcon.className = 'fas fa-bars';
        mobileToggle.setAttribute('aria-label', 'Open navigation menu');
    }
}

mobileToggle.addEventListener('click', toggleNav);
mobileClose.addEventListener('click', toggleNav);
navOverlay.addEventListener('click', toggleNav);

document.querySelectorAll('.nav a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            toggleNav();
        }
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && mainNav.classList.contains('active')) {
        toggleNav();
    }
});

// Resource filtering functionality (keeping existing functionality intact)
const subjectSearch = document.getElementById('subjectSearch');
const resourceTypeDropdown = document.getElementById('resourceTypeDropdown');
const yearSearch = document.getElementById('yearSearch');
const searchBtn = document.getElementById('searchBtn');
const subjectGroups = document.querySelectorAll('.subject-group');
const filterButtons = document.querySelectorAll('.filter-btn');
const resultsCount = document.getElementById('resultsCount');

function updateYearOptions() {
    const query = subjectSearch.value.toLowerCase().trim();
    const selectedType = resourceTypeDropdown.value;
    const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
    const yearSet = new Set();
    
    subjectGroups.forEach(group => {
        const groupTitle = group.querySelector('.subject-title').textContent.toLowerCase().trim();
        group.querySelectorAll('.resource-card').forEach(card => {
            const cardYear = card.getAttribute('data-year');
            const cardType = card.getAttribute('data-type');
            const cardSubjectType = card.getAttribute('data-subject-type');
            const cardTitle = card.querySelector('.resource-title').textContent.toLowerCase().trim();
            
             // Loose match + full word match
            const queryMatch = query === '' || cardTitle.includes(query) || groupTitle.includes(query);



            if ((selectedType === 'all' || cardType === selectedType) &&
                queryMatch &&
                (activeFilter === 'all' || cardSubjectType === activeFilter)) {
                yearSet.add(cardYear);
            }
        });
    });
    
    const currentYear = yearSearch.value;
    yearSearch.innerHTML = '<option value="">All Years</option>';
    Array.from(yearSet).sort().forEach(y => {
        const option = document.createElement('option');
        option.value = y;
        option.textContent = y;
        if (y === currentYear) option.selected = true;
        yearSearch.appendChild(option);
    });
}

function filterResources() {
    const query = subjectSearch.value.toLowerCase().trim();
    const selectedType = resourceTypeDropdown.value;
    const selectedYear = yearSearch.value;
    const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
    let totalResources = 0;

    subjectGroups.forEach(group => {
   const groupTitle = group.querySelector('.subject-title').textContent.toLowerCase().trim();
        let groupVisible = false;
        
        group.querySelectorAll('.resource-card').forEach(card => {
            const cardType = card.getAttribute('data-type');
            const cardYear = card.getAttribute('data-year');
            const cardSubjectType = card.getAttribute('data-subject-type');
            const cardTitle = card.querySelector('.resource-title').textContent.toLowerCase().trim();
            
            // Search match: loose match (includes) + exact word match
            const queryMatch = query === '' || cardTitle.includes(query) || groupTitle.includes(query);


            const matches = (selectedType === 'all' || cardType === selectedType) &&
                            (selectedYear === '' || cardYear === selectedYear) &&
                            queryMatch &&
                            (activeFilter === 'all' || cardSubjectType === activeFilter);
            
            card.style.display = matches ? 'block' : 'none';
            if (matches) {
                groupVisible = true;
                totalResources++;
            }
        });
        
        group.style.display = groupVisible ? 'block' : 'none';
    });
    
    resultsCount.textContent = totalResources + ' resource' + (totalResources !== 1 ? 's' : '') + ' found';
}

// Event listeners (keeping existing functionality)
subjectSearch.addEventListener('input', () => {
    updateYearOptions();
    filterResources();
});

resourceTypeDropdown.addEventListener('change', () => {
    updateYearOptions();
    filterResources();
});

yearSearch.addEventListener('change', filterResources);
searchBtn.addEventListener('click', () => {
    updateYearOptions();
    filterResources();
});

filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        updateYearOptions();
        filterResources();
    });
});

document.querySelectorAll('.subject-header').forEach(header => {
    // Add chevron icon to subject titles
    const titleElement = header.querySelector('.subject-title');
    if (!titleElement.querySelector('i')) {
        titleElement.innerHTML = '<i class="fas fa-chevron-down"></i>' + titleElement.innerHTML;
    }
    
    header.addEventListener('click', () => {
        const content = header.nextElementSibling;
        const isCollapsed = header.classList.contains('collapsed');
        
        header.classList.toggle('collapsed');
        
        if (isCollapsed) {
            content.style.maxHeight = content.scrollHeight + 'px';
            content.style.opacity = '1';
        } else {
            content.style.maxHeight = '0';
            content.style.opacity = '0';
        }
    });
});

// Initialize on page load
window.addEventListener('DOMContentLoaded', () => {
    updateYearOptions();
    filterResources();
    
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            mainNav.classList.remove('active');
            navOverlay.classList.remove('active');
            document.body.style.overflow = '';
            toggleIcon.className = 'fas fa-bars';
            mobileToggle.setAttribute('aria-label', 'Open navigation menu');
        }
    });
});

// Smooth scrolling for anchor links
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
</script>
</body>
</html>
<?php $con->close(); ?>
