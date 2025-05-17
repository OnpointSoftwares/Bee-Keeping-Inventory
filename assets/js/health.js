/**
 * Health Management JavaScript
 * 
 * This file handles all functionality related to hive health management
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize health management if we're on the health page
    const healthSection = document.getElementById('health');
    if (healthSection) {
        initHealthManagement();
    }
});

/**
 * Initialize health management functionality
 */
function initHealthManagement() {
    // Add health check form submission
    const addHealthCheckForm = document.getElementById('addHealthCheckForm');
    if (addHealthCheckForm) {
        addHealthCheckForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addHealthCheck(this);
        });
    }
    
    // Edit health check form submission
    const editHealthCheckForm = document.getElementById('editHealthCheckForm');
    if (editHealthCheckForm) {
        editHealthCheckForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateHealthCheck(this);
        });
    }
    
    // Delete health check buttons
    document.querySelectorAll('.delete-health-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            if (confirm('Are you sure you want to delete this health check?')) {
                deleteHealthCheck(healthId);
            }
        });
    });
    
    // Edit health check buttons
    document.querySelectorAll('.edit-health-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            showEditHealthCheckModal(healthId);
        });
    });
    
    // View health check buttons
    document.querySelectorAll('.view-health-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            viewHealthCheck(healthId);
        });
    });
    
    // Initialize health filters
    initHealthFilters();
    
    // Load health data
    loadHealthData();
}

/**
 * Initialize health filters
 */
function initHealthFilters() {
    const dateRangeFilter = document.getElementById('healthDateRange');
    const hiveFilter = document.getElementById('healthHiveFilter');
    const statusFilter = document.getElementById('healthStatusFilter');
    
    if (dateRangeFilter && hiveFilter && statusFilter) {
        [dateRangeFilter, hiveFilter, statusFilter].forEach(filter => {
            filter.addEventListener('change', function() {
                loadHealthData({
                    dateRange: dateRangeFilter.value,
                    hiveID: hiveFilter.value,
                    status: statusFilter.value
                });
            });
        });
    }
}

/**
 * Load health data from the API
 * 
 * @param {Object} filters - Filter parameters
 */
async function loadHealthData(filters = {}) {
    try {
        // Prepare API parameters
        const params = { action: 'getAll' };
        
        if (filters.hiveID) {
            params.hiveID = filters.hiveID;
        }
        
        if (filters.dateRange) {
            // Calculate date range
            const now = new Date();
            let startDate;
            
            switch (filters.dateRange) {
                case 'month':
                    startDate = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                    break;
                case 'quarter':
                    startDate = new Date(now.getFullYear(), now.getMonth() - 3, now.getDate());
                    break;
                case 'year':
                    startDate = new Date(now.getFullYear() - 1, now.getMonth(), now.getDate());
                    break;
                default:
                    // All time, no date filter
                    break;
            }
            
            if (startDate) {
                params.startDate = ApiUtils.formatDate(startDate);
                params.endDate = ApiUtils.formatDate(now);
            }
        }
        
        if (filters.status) {
            params.status = filters.status;
        }
        
        // Get health data via API
        const response = await ApiUtils.get('health', 'getAll', params);
        
        // Update health data display
        updateHealthDisplay(response.data, response.summary);
        
        // Update health charts
        updateHealthCharts(response.data, response.summary);
        
    } catch (error) {
        console.error('Error loading health data:', error);
        ApiUtils.showNotification('Failed to load health data: ' + error.message, 'danger');
    }
}

/**
 * Update health data display with filtered data
 * 
 * @param {Array} healthData - Health check records
 * @param {Object} summary - Health summary data
 */
function updateHealthDisplay(healthData, summary) {
    const healthTable = document.getElementById('healthTable');
    if (!healthTable) return;
    
    const tbody = healthTable.querySelector('tbody');
    if (!tbody) return;
    
    // Clear existing rows
    tbody.innerHTML = '';
    
    if (healthData.length === 0) {
        // No data message
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="7" class="text-center">No health checks match the selected filters.</td>`;
        tbody.appendChild(row);
        return;
    }
    
    // Add health check rows
    healthData.forEach(check => {
        const row = document.createElement('tr');
        row.setAttribute('data-health-id', check.healthID);
        
        // Determine status class
        let statusClass = 'bg-success';
        if (check.status === 'Warning') {
            statusClass = 'bg-warning';
        } else if (check.status === 'Critical') {
            statusClass = 'bg-danger';
        }
        
        row.innerHTML = `
            <td>${check.hiveNumber}</td>
            <td>${check.checkDate}</td>
            <td>${check.queenPresent === '1' ? 'Yes' : 'No'}</td>
            <td>${check.colonyStrength}</td>
            <td><span class="badge ${statusClass}">${check.status}</span></td>
            <td>
                <button class="btn btn-sm btn-info view-health-btn" data-health-id="${check.healthID}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-primary edit-health-btn" data-health-id="${check.healthID}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-health-btn" data-health-id="${check.healthID}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Add event listeners to new buttons
    document.querySelectorAll('.view-health-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            viewHealthCheck(healthId);
        });
    });
    
    document.querySelectorAll('.edit-health-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            showEditHealthCheckModal(healthId);
        });
    });
    
    document.querySelectorAll('.delete-health-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            if (confirm('Are you sure you want to delete this health check?')) {
                deleteHealthCheck(healthId);
            }
        });
    });
    
    // Update summary cards
    updateHealthSummaryCards(summary);
}

/**
 * Update health summary cards
 * 
 * @param {Object} summary - Health summary data
 */
function updateHealthSummaryCards(summary) {
    // Update total checks card
    const totalChecksElement = document.getElementById('totalHealthChecks');
    if (totalChecksElement) {
        totalChecksElement.textContent = summary.totalChecks;
    }
    
    // Update healthy hives card
    const healthyHivesElement = document.getElementById('healthyHives');
    if (healthyHivesElement) {
        healthyHivesElement.textContent = summary.healthyCount;
    }
    
    // Update warning hives card
    const warningHivesElement = document.getElementById('warningHives');
    if (warningHivesElement) {
        warningHivesElement.textContent = summary.warningCount;
    }
    
    // Update critical hives card
    const criticalHivesElement = document.getElementById('criticalHives');
    if (criticalHivesElement) {
        criticalHivesElement.textContent = summary.criticalCount;
    }
}

/**
 * Update health charts with filtered data
 * 
 * @param {Array} healthData - Health check records
 * @param {Object} summary - Health summary data
 */
function updateHealthCharts(healthData, summary) {
    // Update health status chart
    updateHealthStatusChart(summary);
    
    // Update health issues chart
    updateHealthIssuesChart(summary);
}

/**
 * Update health status chart
 * 
 * @param {Object} summary - Health summary data
 */
function updateHealthStatusChart(summary) {
    const ctx = document.getElementById('healthStatusChart');
    if (!ctx) return;
    
    // Prepare data for chart
    const labels = ['Healthy', 'Warning', 'Critical'];
    const data = [summary.healthyCount, summary.warningCount, summary.criticalCount];
    const backgroundColors = [
        'rgba(40, 167, 69, 0.7)',
        'rgba(255, 193, 7, 0.7)',
        'rgba(220, 53, 69, 0.7)'
    ];
    
    // Create or update chart
    if (window.healthStatusChart) {
        window.healthStatusChart.data.datasets[0].data = data;
        window.healthStatusChart.update();
    } else {
        window.healthStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

/**
 * Update health issues chart
 * 
 * @param {Object} summary - Health summary data
 */
function updateHealthIssuesChart(summary) {
    const ctx = document.getElementById('healthIssuesChart');
    if (!ctx) return;
    
    // Prepare data for chart
    const labels = [];
    const data = [];
    
    if (summary.issues && summary.issues.length > 0) {
        summary.issues.forEach(issue => {
            labels.push(issue.issue);
            data.push(issue.count);
        });
    }
    
    // Create or update chart
    if (window.healthIssuesChart) {
        window.healthIssuesChart.data.labels = labels;
        window.healthIssuesChart.data.datasets[0].data = data;
        window.healthIssuesChart.update();
    } else {
        window.healthIssuesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Number of Occurrences',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Count'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Issue Type'
                        }
                    }
                }
            }
        });
    }
}

/**
 * Add a new health check
 * 
 * @param {HTMLFormElement} form - The form element
 */
async function addHealthCheck(form) {
    try {
        // Get form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        // Handle checkboxes
        data.queenPresent = form.elements['queenPresent'].checked ? 1 : 0;
        
        // Add health check via API
        const response = await ApiUtils.post('health', 'add', data);
        
        // Show success message
        ApiUtils.showNotification('Health check added successfully!', 'success');
        
        // Reset form
        form.reset();
        
        // Close modal if it exists
        const modal = bootstrap.Modal.getInstance(document.getElementById('addHealthCheckModal'));
        if (modal) {
            modal.hide();
        }
        
        // Reload page to show new data
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    } catch (error) {
        console.error('Error adding health check:', error);
        ApiUtils.showNotification('Failed to add health check: ' + error.message, 'danger');
    }
}

/**
 * Update an existing health check
 * 
 * @param {HTMLFormElement} form - The form element
 */
async function updateHealthCheck(form) {
    try {
        // Get form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        // Handle checkboxes
        data.queenPresent = form.elements['queenPresent'].checked ? 1 : 0;
        
        // Update health check via API
        const response = await ApiUtils.post('health', 'update', data);
        
        // Show success message
        ApiUtils.showNotification('Health check updated successfully!', 'success');
        
        // Close modal if it exists
        const modal = bootstrap.Modal.getInstance(document.getElementById('editHealthCheckModal'));
        if (modal) {
            modal.hide();
        }
        
        // Reload page to show updated data
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    } catch (error) {
        console.error('Error updating health check:', error);
        ApiUtils.showNotification('Failed to update health check: ' + error.message, 'danger');
    }
}

/**
 * Delete a health check
 * 
 * @param {string} healthId - The ID of the health check to delete
 */
async function deleteHealthCheck(healthId) {
    try {
        // Delete health check via API
        const response = await ApiUtils.delete('health', healthId);
        
        // Show success message
        ApiUtils.showNotification('Health check deleted successfully!', 'success');
        
        // Remove the row from the table
        const row = document.querySelector(`tr[data-health-id="${healthId}"]`);
        if (row) {
            row.remove();
        }
        
        // Reload health data to update charts
        loadHealthData();
    } catch (error) {
        console.error('Error deleting health check:', error);
        ApiUtils.showNotification('Failed to delete health check: ' + error.message, 'danger');
    }
}

/**
 * View health check details
 * 
 * @param {string} healthId - The ID of the health check to view
 */
async function viewHealthCheck(healthId) {
    try {
        // Get health check details via API
        const response = await ApiUtils.get('health', 'get', { id: healthId });
        const healthCheck = response.data;
        
        // Show health check details in modal
        const modal = new bootstrap.Modal(document.getElementById('viewHealthCheckModal'));
        
        // Set modal title
        const modalTitle = document.querySelector('#viewHealthCheckModal .modal-title');
        if (modalTitle) {
            modalTitle.textContent = `Health Check for Hive #${healthCheck.hiveNumber}`;
        }
        
        // Determine status class
        let statusClass = 'bg-success';
        if (healthCheck.status === 'Warning') {
            statusClass = 'bg-warning';
        } else if (healthCheck.status === 'Critical') {
            statusClass = 'bg-danger';
        }
        
        // Populate modal content
        const modalBody = document.querySelector('#viewHealthCheckModal .modal-body');
        if (modalBody) {
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Hive Number:</strong> ${healthCheck.hiveNumber}</p>
                        <p><strong>Check Date:</strong> ${healthCheck.checkDate}</p>
                        <p><strong>Queen Present:</strong> ${healthCheck.queenPresent === '1' ? 'Yes' : 'No'}</p>
                        <p><strong>Colony Strength:</strong> ${healthCheck.colonyStrength}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="badge ${statusClass}">${healthCheck.status}</span></p>
                        <p><strong>Disease Symptoms:</strong> ${healthCheck.diseaseSymptoms || 'None'}</p>
                        <p><strong>Pest Problems:</strong> ${healthCheck.pestProblems || 'None'}</p>
                        <p><strong>Notes:</strong> ${healthCheck.notes || 'None'}</p>
                    </div>
                </div>
            `;
        }
        
        modal.show();
    } catch (error) {
        console.error('Error viewing health check:', error);
        ApiUtils.showNotification('Failed to load health check details: ' + error.message, 'danger');
    }
}

/**
 * Show edit health check modal with pre-filled data
 * 
 * @param {string} healthId - The ID of the health check to edit
 */
async function showEditHealthCheckModal(healthId) {
    try {
        // Get health check details via API
        const response = await ApiUtils.get('health', 'get', { id: healthId });
        const healthCheck = response.data;
        
        // Show edit modal
        const modal = new bootstrap.Modal(document.getElementById('editHealthCheckModal'));
        const form = document.getElementById('editHealthCheckForm');
        
        if (form) {
            // Fill form fields with health check data
            form.elements['healthID'].value = healthCheck.healthID;
            form.elements['hiveID'].value = healthCheck.hiveID;
            form.elements['checkDate'].value = healthCheck.checkDate;
            form.elements['queenPresent'].checked = healthCheck.queenPresent === '1';
            form.elements['colonyStrength'].value = healthCheck.colonyStrength;
            form.elements['diseaseSymptoms'].value = healthCheck.diseaseSymptoms || '';
            form.elements['pestProblems'].value = healthCheck.pestProblems || '';
            form.elements['notes'].value = healthCheck.notes || '';
        }
        
        modal.show();
    } catch (error) {
        console.error('Error loading health check for editing:', error);
        ApiUtils.showNotification('Failed to load health check details: ' + error.message, 'danger');
    }
}
