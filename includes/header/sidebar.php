<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-header">
        <h3>Beekeeping MS</h3>
    </div>
    <ul class="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'hives') ? 'active' : ''; ?>" href="index.php?page=hives">
                <i class="fas fa-box-archive"></i>
                Hives
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'equipment') ? 'active' : ''; ?>" href="index.php?page=equipment">
                <i class="fas fa-tools"></i>
                Equipment
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'production') ? 'active' : ''; ?>" href="index.php?page=production">
                <i class="fas fa-flask"></i>
                Production
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'health') ? 'active' : ''; ?>" href="index.php?page=health">
                <i class="fas fa-heart"></i>
                Health
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'health_history') ? 'active' : ''; ?>" href="index.php?page=health_history">
                <i class="fas fa-history"></i>
                Health History
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'reports') ? 'active' : ''; ?>" href="index.php?page=reports">
                <i class="fas fa-chart-bar"></i>
                Reports
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'settings') ? 'active' : ''; ?>" href="index.php?page=settings">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php" class="btn btn-danger w-100">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

<!-- Mobile Toggle -->
<button class="sidebar-toggle d-md-none">
    <i class="fas fa-bars"></i>
</button>
