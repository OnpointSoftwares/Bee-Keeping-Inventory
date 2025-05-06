// Reports Management
document.addEventListener('DOMContentLoaded', function() {
    // Show reports section when nav link is clicked
    document.querySelector('a[href="#reports"]').addEventListener('click', function() {
        loadReports();
    });
});

// Load reports data
function loadReports() {
    // Update active section
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    document.getElementById('reports').style.display = 'block';

    // Load different types of reports
    loadProductionReport();
    loadEquipmentReport();
    loadHealthReport();
}

// Load production report
function loadProductionReport() {
    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'controller=reports&action=getProductionReport'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayProductionReport(data.report);
        } else {
            showToast('error', 'Error loading production report: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to load production report');
    });
}

// Load equipment report
function loadEquipmentReport() {
    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'controller=reports&action=getEquipmentReport'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayEquipmentReport(data.report);
        } else {
            showToast('error', 'Error loading equipment report: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to load equipment report');
    });
}

// Load health report
function loadHealthReport() {
    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'controller=reports&action=getHealthReport'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayHealthReport(data.report);
        } else {
            showToast('error', 'Error loading health report: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to load health report');
    });
}

// Display production report
function displayProductionReport(report) {
    const container = document.getElementById('productionReportSection');
    if (!container) return;

    let html = `
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Honey Production Summary</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Total Production (kg)</th>
                                <th>Average Quality</th>
                                <th>Active Hives</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>This Month</td>
                                <td>${report.monthly?.totalProduction || 0}</td>
                                <td>${report.monthly?.avgQuality || 'N/A'}</td>
                                <td>${report.monthly?.activeHives || 0}</td>
                            </tr>
                            <tr>
                                <td>This Year</td>
                                <td>${report.yearly?.totalProduction || 0}</td>
                                <td>${report.yearly?.avgQuality || 'N/A'}</td>
                                <td>${report.yearly?.activeHives || 0}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;
}

// Display equipment report
function displayEquipmentReport(report) {
    const container = document.getElementById('equipmentReportSection');
    if (!container) return;

    let html = `
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Equipment Status Summary</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Equipment Type</th>
                                <th>Total Items</th>
                                <th>Good Condition</th>
                                <th>Needs Attention</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

    report.forEach(item => {
        html += `
            <tr>
                <td>${item.type}</td>
                <td>${item.totalCount}</td>
                <td>${item.goodCondition}</td>
                <td>${item.needsAttention}</td>
            </tr>
        `;
    });

    html += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;
}

// Display health report
function displayHealthReport(report) {
    const container = document.getElementById('healthReportSection');
    if (!container) return;

    let html = `
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hive Health Summary</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Health Metric</th>
                                <th>Good Status</th>
                                <th>Warning Status</th>
                                <th>Critical Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Colony Strength</td>
                                <td>${report.colonyStrength?.good || 0}</td>
                                <td>${report.colonyStrength?.warning || 0}</td>
                                <td>${report.colonyStrength?.critical || 0}</td>
                            </tr>
                            <tr>
                                <td>Disease Status</td>
                                <td>${report.diseaseStatus?.good || 0}</td>
                                <td>${report.diseaseStatus?.warning || 0}</td>
                                <td>${report.diseaseStatus?.critical || 0}</td>
                            </tr>
                            <tr>
                                <td>Food Stores</td>
                                <td>${report.foodStores?.good || 0}</td>
                                <td>${report.foodStores?.warning || 0}</td>
                                <td>${report.foodStores?.critical || 0}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;
}
