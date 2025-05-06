// Health Management
document.addEventListener('DOMContentLoaded', function() {
    // Show health section when nav link is clicked
    document.querySelector('a[href="#health"]').addEventListener('click', function() {
        loadHealth();
    });

    // Add health record form submission
    document.getElementById('addHealthForm')?.addEventListener('submit', handleAddHealth);

    // Update health record form submission
    document.getElementById('updateHealthForm')?.addEventListener('submit', handleUpdateHealth);
});

// Load health data
function loadHealth() {
    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'controller=health&action=getAll'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayHealth(data);
            updateHealthSummary(data);
        } else {
            showToast('error', 'Error loading health records: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to load health records');
    });
}

// Display health records
function displayHealth(data) {
    const container = document.getElementById('healthContainer');
    if (!container) return;

    let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Hive</th>
                    <th>Date</th>
                    <th>Colony Strength</th>
                    <th>Disease Status</th>
                    <th>Food Stores</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(item => {
        html += `
            <tr data-id="${item.healthID}">
                <td>${item.hiveName}</td>
                <td>${formatDate(item.checkDate)}</td>
                <td>
                    <span class="status-${getHealthClass(item.colonyStrength)}">
                        ${item.colonyStrength}
                    </span>
                </td>
                <td>
                    <span class="status-${getHealthClass(item.diseaseStatus)}">
                        ${item.diseaseStatus}
                    </span>
                </td>
                <td>
                    <span class="status-${getHealthClass(item.foodStores)}">
                        ${item.foodStores}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" onclick="editHealth(${item.healthID})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteHealth(${item.healthID})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    container.innerHTML = html;
}

// Update health summary
function updateHealthSummary(data) {
    const summaryContainer = document.getElementById('healthSummary');
    if (!summaryContainer) return;

    const summary = {
        total: data.length,
        good: 0,
        warning: 0,
        critical: 0
    };

    data.forEach(item => {
        const status = getOverallHealth(item);
        summary[status.toLowerCase()]++;
    });

    let html = `
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Hives</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${summary.total}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Healthy</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${summary.good}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Warning</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${summary.warning}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Critical</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${summary.critical}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    summaryContainer.innerHTML = html;
}

// Handle add health record
function handleAddHealth(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        controller: 'health',
        action: 'add',
        hiveID: formData.get('hiveID'),
        checkDate: formData.get('checkDate'),
        colonyStrength: formData.get('colonyStrength'),
        diseaseStatus: formData.get('diseaseStatus'),
        foodStores: formData.get('foodStores'),
        notes: formData.get('notes')
    };

    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Health record added successfully');
            loadHealth();
            $('#addHealthModal').modal('hide');
            e.target.reset();
        } else {
            showToast('error', 'Error adding health record: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to add health record');
    });
}

// Handle update health record
function handleUpdateHealth(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        controller: 'health',
        action: 'update',
        healthID: formData.get('healthID'),
        colonyStrength: formData.get('colonyStrength'),
        diseaseStatus: formData.get('diseaseStatus'),
        foodStores: formData.get('foodStores'),
        notes: formData.get('notes')
    };

    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Health record updated successfully');
            loadHealth();
            $('#updateHealthModal').modal('hide');
        } else {
            showToast('error', 'Error updating health record: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to update health record');
    });
}

// Delete health record
function deleteHealth(healthID) {
    if (confirm('Are you sure you want to delete this health record?')) {
        fetch('api/handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `controller=health&action=delete&healthID=${healthID}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Health record deleted successfully');
                loadHealth();
            } else {
                showToast('error', 'Error deleting health record: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Failed to delete health record');
        });
    }
}

// Edit health record
function editHealth(healthID) {
    const record = document.querySelector(`tr[data-id="${healthID}"]`);
    if (!record) return;

    const modal = document.getElementById('updateHealthModal');
    if (!modal) return;

    const form = modal.querySelector('form');
    form.querySelector('[name="healthID"]').value = healthID;
    form.querySelector('[name="colonyStrength"]').value = record.dataset.colonyStrength;
    form.querySelector('[name="diseaseStatus"]').value = record.dataset.diseaseStatus;
    form.querySelector('[name="foodStores"]').value = record.dataset.foodStores;
    form.querySelector('[name="notes"]').value = record.dataset.notes || '';

    $(modal).modal('show');
}

// Helper function to get health status class
function getHealthClass(status) {
    switch (status.toLowerCase()) {
        case 'good':
        case 'healthy':
        case 'abundant':
            return 'good';
        case 'fair':
        case 'warning':
        case 'moderate':
            return 'warning';
        case 'poor':
        case 'critical':
        case 'low':
            return 'danger';
        default:
            return 'warning';
    }
}

// Get overall health status
function getOverallHealth(record) {
    const statuses = [
        getHealthClass(record.colonyStrength),
        getHealthClass(record.diseaseStatus),
        getHealthClass(record.foodStores)
    ];

    if (statuses.includes('danger')) return 'Critical';
    if (statuses.includes('warning')) return 'Warning';
    return 'Good';
}

// Format date helper
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}
