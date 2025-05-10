<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/database.php';
require_once 'utils/database_utils.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedIn'])) {
    header('Location: login.php');
    exit();
}

// Determine which page to load
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$validPages = ['dashboard', 'hives', 'equipment', 'production', 'health', 'health_history', 'reports', 'settings'];

// Validate the page parameter
if (!in_array($page, $validPages)) {
    $page = 'dashboard';
}

// Load all data required for the application
require_once 'includes/data_loader.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/header/meta.php'; ?>
    <title>Beekeeping Inventory Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'includes/header/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <?php 
            // Include the appropriate section based on the page parameter
            include 'includes/sections/' . $page . '.php'; 
            ?>
        </div>
    </main>

    <!-- Include modals -->
    <?php include 'includes/modals/hive_modals.php'; ?>
    <?php include 'includes/modals/equipment_modals.php'; ?>
    <?php include 'includes/modals/production_modals.php'; ?>
    <?php include 'includes/modals/health_modals.php'; ?>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- API Utilities -->
    <script src="assets/js/api-utils.js"></script>
    
    <script>
    // Common JavaScript functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile sidebar toggle
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
                document.querySelector('.main-content').classList.toggle('sidebar-active');
            });
        }
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Set active nav link based on current page
        const currentPage = '<?php echo $page; ?>';
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.includes(currentPage)) {
                link.classList.add('active');
            }
        });
        
        // Initialize page-specific scripts based on current page
        if (currentPage === 'dashboard') {
            initializeDashboard();
        } else if (currentPage === 'hives') {
            initializeHives();
        } else if (currentPage === 'equipment') {
            initializeEquipment();
        } else if (currentPage === 'production') {
            initializeProduction();
        } else if (currentPage === 'health') {
            initializeHealth();
        } else if (currentPage === 'health_history') {
            initializeHealth();
            initializeHealthHistory();
        } else if (currentPage === 'reports') {
            initializeReports();
        }
    });
    </script>
    
    <!-- Page-specific scripts -->
    <?php if ($page == 'dashboard'): ?>
    <script src="js/dashboard.js"></script>
    <script src="js/dashboard_charts.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'hives'): ?>
    <script src="js/hive.js"></script>
    <script src="js/hive_init.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'equipment'): ?>
    <script src="js/equipment.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'production'): ?>
    <script src="js/production.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'health' || $page == 'health_history'): ?>
    <script src="js/health.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'health_history'): ?>
    <script src="js/health-history.js"></script>
    <script src="js/health_history_charts.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'reports'): ?>
    <script src="js/reports.js"></script>
    <?php endif; ?>
</body>
</html>
