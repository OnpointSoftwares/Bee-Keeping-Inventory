<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beekeeping Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- JavaScript Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="js/utils.js"></script>
    <script src="js/hive.js"></script>
    <script src="js/production.js"></script>
    <script src="js/equipment.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h3>Beekeeping MS</h3>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="#dashboard">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#hives">
                    <i class="fas fa-box-archive"></i>
                    Hives
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#equipment">
                    <i class="fas fa-tools"></i>
                    Equipment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#production">
                    <i class="fas fa-flask"></i>
                    Production
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#health">
                    <i class="fas fa-heart"></i>
                    Health
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#reports">
                    <i class="fas fa-chart-bar"></i>
                    Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#settings">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link logout-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Mobile Toggle -->
    <button class="sidebar-toggle d-md-none">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Dashboard Section -->
           <!-- Dashboard Section -->
<section id="dashboard" class="tab-pane active">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Dashboard</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Total Production</h5>
                </div>
                <div class="card-body">
                    <canvas id="productionChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Equipment Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="equipmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

            <!-- Hives Section -->
            <section id="hives" class="tab-pane">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Hive Data</h2>
                            <div id="hivesContainer"></div> 
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Hive Management</h5>
                                <div id="hivesContainer"></div> <!-- Hives container -->
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHiveModal">
                                    Add New Hive
                                </button>
                            </div>
                            <div class="card-body">
                            <div id="hivesContainer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Health Check</h5>
                            </div>
                            <div class="card-body">
                                <form id="addHealthCheckForm">
                                    <div class="form-group">
                                        <label>Select Hive</label>
                                        <select name="hiveID" class="form-control hive-select" required></select>
                                    </div>
                                    <div class="form-group">
                                        <label>Check Date</label>
                                        <input type="date" name="checkDate" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Colony Strength (1-10)</label>
                                        <input type="number" name="colonyStrength" class="form-control" min="1" max="10" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Queen Present</label>
                                        <select name="queenPresent" class="form-control">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Disease Symptoms</label>
                                        <textarea name="diseaseSymptoms" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Pest Problems</label>
                                        <textarea name="pestProblems" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Health Check</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Health History</h5>
                            </div>
                            <div class="card-body">
                                <div id="healthHistoryContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Equipment Section -->
            <section id="equipment" class="tab-pane">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Equipment Inventory</h5>
                                <div>
                                    <select id="equipmentTypeFilter" class="form-control d-inline-block w-auto me-2">
                                        <option value="">All Types</option>
                                    </select>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                                        Add Equipment
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="equipmentContainer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Inventory Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="production-stat mb-4">
                                    <h3>Total Items</h3>
                                    <p id="totalItems">0</p>
                                </div>
                                <div class="chart-container">
                                    <canvas id="inventoryChart"></canvas>
                                </div>
                                <div id="inventoryReportContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Production Section -->
            <section id="production" class="tab-pane">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Production Summary</h5>
                                <select id="productionDateRange" class="form-control w-auto">
                                    <option value="month">Last Month</option>
                                    <option value="quarter">Last Quarter</option>
                                    <option value="year" selected>Last Year</option>
                                </select>
                            </div>
                            <div class="card-body">
                                <div class="production-summary">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="production-stat">
                                                <h3>Total Production</h3>
                                                <p id="totalProduction">0 kg</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="chart-container">
                                                <canvas id="productionChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="productionTableContainer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Add Production</h5>
                            </div>
                            <div class="card-body">
                                <form id="addProductionForm">
                                    <div class="form-group">
                                        <label>Select Hive</label>
                                        <select name="hiveID" class="form-control hive-select" required></select>
                                    </div>
                                    <div class="form-group">
                                        <label>Harvest Date</label>
                                        <input type="date" name="harvestDate" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Quantity (kg)</label>
                                        <input type="number" name="quantity" class="form-control" step="0.1" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Honey Type</label>
                                        <select name="type" class="form-control" required>
                                            <option value="Wildflower">Wildflower</option>
                                            <option value="Clover">Clover</option>
                                            <option value="Buckwheat">Buckwheat</option>
                                            <option value="Orange Blossom">Orange Blossom</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Quality</label>
                                        <select name="quality" class="form-control">
                                            <option value="Premium">Premium</option>
                                            <option value="Standard">Standard</option>
                                            <option value="Processing">Processing</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Production</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Honey Type Distribution</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="honeyTypeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Hive Production History</h5>
                            </div>
                            <div class="card-body">
                                <select id="productionHiveSelect" class="form-control mb-3 hive-select">
                                    <option value="">Select Hive</option>
                                </select>
                                <div id="hiveProductionContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Health Section -->
            <!-- Hive Health Section -->
<section id="health" class="tab-pane">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hive Health Monitoring</h5>
                </div>
                <div class="card-body">
                    <form id="healthCheckForm">
                        <div class="form-group">
                            <label for="hiveID">Select Hive:</label>
                            <select id="hiveID" class="form-control hive-select" required>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="checkDate">Check Date:</label>
                            <input type="date" id="checkDate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="colonyStrength">Colony Strength (1-10):</label>
                            <input type="number" id="colonyStrength" class="form-control" min="1" max="10" required>
                        </div>
                        <div class="form-group">
                            <label for="queenPresent">Queen Present:</label>
                            <select id="queenPresent" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="diseaseSymptoms">Disease Symptoms:</label>
                            <textarea id="diseaseSymptoms" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="pestProblems">Pest Problems:</label>
                            <textarea id="pestProblems" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes:</label>
                            <textarea id="notes" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Health Check</button>
                    </form>
                    <div id="healthHistory" class="mt-4">
                        <!-- Historical health records will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

            <!-- Reports Section -->
            <!-- Reports Section -->
<section id="reports" class="tab-pane">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Reports Generation</h5>
                </div>
                <div class="card-body">
                    <form id="reportsForm">
                        <div class="form-group">
                            <label for="reportType">Select Report Type:</label>
                            <select id="reportType" class="form-control" required>
                                <option value="">--Select Report--</option>
                                <option value="honeyProduction">Honey Production Report</option>
                                <option value="hiveHealth">Hive Health Report</option>
                                <option value="equipmentUsage">Equipment Usage Report</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="startDate">Start Date:</label>
                            <input type="date" id="startDate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="endDate">End Date:</label>
                            <input type="date" id="endDate" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </form>
                    <div id="reportResults" class="mt-4">
                        <!-- Report results will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

          <!-- Settings Section -->
<section id="settings" class="tab-pane">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <form id="settingsForm">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Change Password:</label>
                            <input type="password" id="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="language">Preferred Language:</label>
                            <select id="language" class="form-control">
                                <option value="en">English</option>
                                <option value="sw">Kiswahili</option>
                                <option value="ki">Kikuyu</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
        </div>
    </main>

    <!-- Modals -->
    <!-- Add Hive Modal -->
    <div class="modal fade" id="addHiveModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Hive</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addHiveForm">
                        <div class="form-group">
                            <label>Hive Number</label>
                            <input type="text" name="hiveNumber" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Date Established</label>
                            <input type="date" name="dateEstablished" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Queen Age (months)</label>
                            <input type="number" name="queenAge" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addHiveForm" class="btn btn-primary">Add Hive</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Hive Modal -->
    <div class="modal fade" id="viewHiveModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Equipment Modal -->
    <div class="modal fade" id="addEquipmentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addEquipmentForm">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="Hive Tools">Hive Tools</option>
                                <option value="Protective Gear">Protective Gear</option>
                                <option value="Extraction Equipment">Extraction Equipment</option>
                                <option value="Storage">Storage</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Condition</label>
                            <select name="condition" class="form-control">
                                <option value="New">New</option>
                                <option value="Good">Good</option>
                                <option value="Fair">Fair</option>
                                <option value="Poor">Poor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Purchase Date</label>
                            <input type="date" name="purchaseDate" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addEquipmentForm" class="btn btn-primary">Add Equipment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Equipment Modal -->
    <div class="modal fade" id="editEquipmentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editEquipmentForm">
                        <input type="hidden" name="equipmentID">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="Hive Tools">Hive Tools</option>
                                <option value="Protective Gear">Protective Gear</option>
                                <option value="Extraction Equipment">Extraction Equipment</option>
                                <option value="Storage">Storage</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Condition</label>
                            <select name="condition" class="form-control">
                                <option value="New">New</option>
                                <option value="Good">Good</option>
                                <option value="Fair">Fair</option>
                                <option value="Poor">Poor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="equipmentManager.updateEquipment(document.getElementById('editEquipmentForm'))">
                        Update Equipment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before the closing body tag -->
    <script>
    // Sidebar Toggle
    document.querySelector('.sidebar-toggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('show');
        document.querySelector('.main-content').classList.toggle('sidebar-open');
    });

    // Navigation
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Prevent default anchor behavior
            e.preventDefault();

            // Remove active class from all links and sections
            document.querySelectorAll('.sidebar-nav .nav-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => {
                p.classList.remove('active'); // Hide all sections
                p.style.display = 'none'; // Ensure sections are hidden
            });

            // Add active class to clicked link
            this.classList.add('active');

            // Show corresponding section
            const target = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(target);
            if (targetSection) {
                targetSection.classList.add('active'); // Add active class
                targetSection.style.display = 'block'; // Show the selected section
            }

            // On mobile, close sidebar after navigation
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.remove('show');
                document.querySelector('.main-content').classList.remove('sidebar-open');
            }
        });
    });
</script>

</body>
</html>
