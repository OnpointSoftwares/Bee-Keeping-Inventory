/**
 * Health Data JavaScript
 * 
 * This file handles all data operations for the health section using the Fetch API
 * to interact with the backend database utilities.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize health data functionality if we're on the health page
    if (document.getElementById('health') || document.getElementById('health_history')) {
        initHealthDataHandlers();
    }
});

/**
 * Initialize health data handlers
 */
function initHealthDataHandlers() {
    // Add health check form submission
    const addHealthCheckForm = document.getElementById('addHealthCheckForm');
    if (addHealthCheckForm) {
        addHealthCheckForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addHealthCheck(this);
        });
    }
    
    // Add health check modal form submission
    const healthCheckModalForm = document.getElementById('healthCheckModalForm');
    if (healthCheckModalForm) {
        healthCheckModalForm.addEventListener('submit', function(e) {
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
    
    // View health check buttons
    document.querySelectorAll('.view-health-btn, .view-health-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            viewHealthCheck(healthId);
        });
    });
    
    // Initialize health history filters if on the health history page
    if (document.getElementById('health_history')) {
        const hiveFilter = document.getElementById('healthHistoryHiveFilter');
        const dateFilter = document.getElementById('healthHistoryDateFilter');
        const statusFilter = document.getElementById('healthHistoryStatusFilter');
        const issueFilter = document.getElementById('healthHistoryIssueFilter');
        
        if (hiveFilter && dateFilter && statusFilter && issueFilter) {
            [hiveFilter, dateFilter, statusFilter, issueFilter].forEach(filter => {
                filter.addEventListener('change', function() {
                    loadHealthChecks({
                        hiveID: hiveFilter.value !== 'all' ? hiveFilter.value : null,
                        dateRange: dateFilter.value !== 'all' ? dateFilter.value : null,
                        status: statusFilter.value !== 'all' ? statusFilter.value : null,
                        issueType: issueFilter.value !== 'all' ? issueFilter.value : null
                    });
                });
            });
            
            // Initial load of health checks
            loadHealthChecks();
        }
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
        
        // Handle checkbox values
        if (form.elements['queenPresent']) {
            // If it's a select element
            if (form.elements['queenPresent'].tagName === 'SELECT') {
                data.queenPresent = form.elements['queenPresent'].value;
            } else {
                // If it's a checkbox
                data.queenPresent = form.elements['queenPresent'].checked ? 1 : 0;
            }
        }
        
        console.log('Adding health check with data:', data);
        
        // Add health check via API
        const response = await fetch('api/health/add_health_check.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        console.log('Add health check response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Health check added successfully!', 'success');
            
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
        } else {
            throw new Error(responseData.message || 'Failed to add health check');
        }
    } catch (error) {
        console.error('Error adding health check:', error);
        showNotification('Failed to add health check: ' + error.message, 'danger');
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
        
        // Handle checkbox values
        if (form.elements['queenPresent']) {
            // If it's a select element
            if (form.elements['queenPresent'].tagName === 'SELECT') {
                data.queenPresent = form.elements['queenPresent'].value;
            } else {
                // If it's a checkbox
                data.queenPresent = form.elements['queenPresent'].checked ? 1 : 0;
            }
        }
        
        console.log('Updating health check with data:', data);
        
        // Update health check via API
        const response = await fetch('api/health/update_health_check.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        console.log('Update health check response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Health check updated successfully!', 'success');
            
            // Close modal if it exists
            const modal = bootstrap.Modal.getInstance(document.getElementById('editHealthCheckModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(responseData.message || 'Failed to update health check');
        }
    } catch (error) {
        console.error('Error updating health check:', error);
        showNotification('Failed to update health check: ' + error.message, 'danger');
    }
}

/**
 * Delete a health check
 * 
 * @param {string} healthId - The ID of the health check to delete
 */
async function deleteHealthCheck(healthId) {
    try {
        console.log('Deleting health check with ID:', healthId);
        
        // Delete health check via API
        const response = await fetch(`api/health/delete_health_check.php?id=${healthId}`, {
            method: 'DELETE'
        });
        
        const responseData = await response.json();
        console.log('Delete health check response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Health check deleted successfully!', 'success');
            
            // Remove the row from the table
            const row = document.querySelector(`tr[data-health-id="${healthId}"]`);
            if (row) {
                row.remove();
            }
            
            // Reload health data to update charts
            loadHealthChecks();
        } else {
            throw new Error(responseData.message || 'Failed to delete health check');
        }
    } catch (error) {
        console.error('Error deleting health check:', error);
        showNotification('Failed to delete health check: ' + error.message, 'danger');
    }
}

/**
 * View health check details
 * 
 * @param {string} healthId - The ID of the health check to view
 */
async function viewHealthCheck(healthId) {
    try {
        console.log('Viewing health check with ID:', healthId);
        
        // Get health check details via API
        const response = await fetch(`api/health/get_health_checks.php?id=${healthId}`);
        
        const responseData = await response.json();
        console.log('View health check response:', responseData);
        
        if (!responseData.success) {
            throw new Error(responseData.message || 'Failed to load health check details');
        }
        
        const healthCheck = responseData.data;
        
        // Show health check details in modal
        const modal = new bootstrap.Modal(document.getElementById('viewHealthCheckModal'));
        
        // Populate modal content
        const detailsContainer = document.getElementById('healthCheckDetails');
        if (detailsContainer) {
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Hive:</strong> ${healthCheck.hiveNumber}</p>
                        <p><strong>Check Date:</strong> ${healthCheck.checkDate}</p>
                        <p><strong>Colony Strength:</strong> ${healthCheck.colonyStrength}/10</p>
                        <p><strong>Queen Present:</strong> ${healthCheck.queenPresent == 1 ? 'Yes' : 'No'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Disease Symptoms:</strong> ${healthCheck.diseaseSymptoms || 'None'}</p>
                        <p><strong>Pest Problems:</strong> ${healthCheck.pestProblems || 'None'}</p>
                        <p><strong>Notes:</strong> ${healthCheck.notes || 'None'}</p>
                    </div>
                </div>
            `;
            
            detailsContainer.innerHTML = content;
        }
        
        // Set up edit button
        const editBtn = document.getElementById('editHealthCheckBtn');
        if (editBtn) {
            editBtn.setAttribute('data-health-id', healthId);
            editBtn.onclick = function() {
                // Hide view modal
                modal.hide();
                
                // Show edit modal with pre-filled data
                showEditHealthCheckModal(healthCheck);
            };
        }
        
        modal.show();
    } catch (error) {
        console.error('Error viewing health check:', error);
        showNotification('Failed to load health check details: ' + error.message, 'danger');
    }
}

/**
 * Show edit health check modal with pre-filled data
 * 
 * @param {Object} healthData - The health check data
 */
function showEditHealthCheckModal(healthData) {
    const modal = new bootstrap.Modal(document.getElementById('editHealthCheckModal'));
    const form = document.getElementById('editHealthCheckForm');
    
    if (form) {
        // Fill form fields with health check data
        form.elements['healthID'].value = healthData.healthID;
        form.elements['hiveID'].value = healthData.hiveID;
        form.elements['checkDate'].value = healthData.checkDate;
        form.elements['colonyStrength'].value = healthData.colonyStrength;
        form.elements['queenPresent'].value = healthData.queenPresent;
        form.elements['diseaseSymptoms'].value = healthData.diseaseSymptoms || '';
        form.elements['pestProblems'].value = healthData.pestProblems || '';
        form.elements['notes'].value = healthData.notes || '';
    }
    
    modal.show();
}

/**
 * Load health checks based on filters
 * 
 * @param {Object} filters - Filter parameters
 */
async function loadHealthChecks(filters = {}) {
    try {
        console.log('Loading health checks with filters:', filters);
        
        // Prepare API parameters
        let url = 'api/health/get_health_checks.php';
        const queryParams = [];
        
        if (filters.hiveID) {
            queryParams.push(`hiveID=${filters.hiveID}`);
        }
        
        if (filters.dateRange) {
            // Calculate date range
            const now = new Date();
            let days = 0;
            
            switch (filters.dateRange) {
                case 'week':
                    days = 7;
                    break;
                case 'month':
                    days = 30;
                    break;
                case 'quarter':
                    days = 90;
                    break;
                case 'year':
                    days = 365;
                    break;
            }
            
            if (!isNaN(days)) {
                const startDate = new Date();
                startDate.setDate(now.getDate() - days);
                const formattedDate = formatDate(startDate);
                queryParams.push(`startDate=${formattedDate}`);
            }
        }
        
        // Build the URL with query parameters
        if (queryParams.length > 0) {
            url += '?' + queryParams.join('&');
        }
        
        console.log('Fetching health checks from URL:', url);
        
        // Get health checks via API
        const response = await fetch(url);
        
        const responseData = await response.json();
        console.log('Load health checks response:', responseData);
        
        if (responseData.success) {
            // Filter results client-side for status and issue type
            let filteredChecks = responseData.data;
            
            // Update health history table
            updateHealthHistoryTable(filteredChecks);
            
            // Update health charts if they exist
            if (typeof updateHealthCharts === 'function') {
                try {
                    // Create status counts object for charts
                    const statusCounts = {
                        healthy: filteredChecks.filter(check => check.status === 'Healthy').length,
                        warning: filteredChecks.filter(check => check.status === 'Warning').length,
                        critical: filteredChecks.filter(check => check.status === 'Critical').length
                    };
                    
                    // Create issue counts object for charts
                    const issueCounts = {
                        disease: filteredChecks.filter(check => check.diseaseSymptoms && check.diseaseSymptoms.trim() !== '').length,
                        pests: filteredChecks.filter(check => check.pestProblems && check.pestProblems.trim() !== '').length,
                        noQueen: filteredChecks.filter(check => check.queenPresent == 0).length,
                        lowStrength: filteredChecks.filter(check => check.colonyStrength < 5).length
                    };
                    
                    // Update charts with the data
                    updateHealthCharts(filteredChecks, statusCounts, issueCounts);
                } catch (error) {
                    console.error('Error updating health charts:', error);
                }
            }
        } else {
            // Show error message
            showNotification('Error loading health checks: ' + responseData.message, 'danger');
        }
    } catch (error) {
        console.error('Error loading health checks:', error);
        showNotification('An error occurred while loading health checks.', 'danger');
    }
}

/**
 * Update health history table with filtered data
 * 
 * @param {Array} healthChecks - Array of health check objects
 */
function updateHealthHistoryTable(healthChecks) {
    const tableContainer = document.getElementById('healthHistoryTableContainer');
    if (!tableContainer) return;
    
    if (healthChecks.length === 0) {
        tableContainer.innerHTML = '<div class="alert alert-info">No health checks match the selected filters.</div>';
        return;
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="healthHistoryTable">
                <thead>
                    <tr>
                        <th>Hive</th>
                        <th>Check Date</th>
                        <th>Colony Strength</th>
                        <th>Queen Present</th>
                        <th>Health Status</th>
                        <th>Issues</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    healthChecks.forEach(check => {
        const issues = check.issues.length > 0 
            ? check.issues.map(issue => `<span class="badge bg-secondary me-1">${issue}</span>`).join('') 
            : '<span class="badge bg-success">None</span>';
        
        html += `
            <tr data-health-id="${check.healthID}" data-hive-id="${check.hiveID}" data-status="${check.healthStatus.toLowerCase()}" data-issues="${check.issues.join(',').toLowerCase()}">
                <td>${check.hiveNumber}</td>
                <td>${check.checkDate}</td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar ${getStrengthColorClass(check.colonyStrength)}" role="progressbar" 
                            style="width: ${check.colonyStrength * 10}%" 
                            aria-valuenow="${check.colonyStrength}" aria-valuemin="0" aria-valuemax="10">
                            ${check.colonyStrength}/10
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge ${check.queenPresent == 1 ? 'bg-success' : 'bg-danger'}">
                        ${check.queenPresent == 1 ? 'Yes' : 'No'}
                    </span>
                </td>
                <td><span class="badge ${check.statusClass}">${check.healthStatus}</span></td>
                <td>${issues}</td>
                <td>
                    <button class="btn btn-sm btn-primary view-health-details-btn" data-health-id="${check.healthID}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-health-btn" data-health-id="${check.healthID}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    tableContainer.innerHTML = html;
    
    // Reattach event listeners
    document.querySelectorAll('.view-health-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            viewHealthCheck(healthId);
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
}

/**
 * Get color class for colony strength
 * 
 * @param {number} strength - Colony strength value
 * @returns {string} - Bootstrap color class
 */
function getStrengthColorClass(strength) {
    if (strength >= 7) {
        return 'bg-success';
    } else if (strength >= 4) {
        return 'bg-warning';
    } else {
        return 'bg-danger';
    }
}

/**
 * Update health charts with filtered data
 * 
 * @param {Array} healthChecks - Array of health check objects
 * @param {Object} statusCounts - Status counts object
 * @param {Object} issueCounts - Issue counts object
 */
function updateHealthCharts(healthChecks, statusCounts, issueCounts) {
    if (!healthChecks || !document.getElementById('health_history')) return;
    
    // Count health status
    let healthyCount = statusCounts.healthy;
    let issuesCount = statusCounts.warning;
    let criticalCount = statusCounts.critical;
    
    // Count issues
    let diseaseCount = issueCounts.disease;
    let pestCount = issueCounts.pests;
    let queenCount = issueCounts.noQueen;
    let strengthCount = issueCounts.lowStrength;
    
    // Prepare data for strength trend chart
    const strengthByDate = {};
    
    healthChecks.forEach(check => {
        // Group strength by date
        if (!strengthByDate[check.checkDate]) {
            strengthByDate[check.checkDate] = {
                total: 0,
                count: 0
            };
        }
        
        strengthByDate[check.checkDate].total += parseInt(check.colonyStrength);
        strengthByDate[check.checkDate].count++;
    });
    
    // Update health status chart
    if (window.healthStatusChart) {
        window.healthStatusChart.data.datasets[0].data = [
            healthyCount,
            issuesCount,
            criticalCount
        ];
        window.healthStatusChart.update();
    }
    
    // Update issues breakdown chart
    if (window.issuesBreakdownChart) {
        window.issuesBreakdownChart.data.datasets[0].data = [
            diseaseCount,
            pestCount,
            queenCount,
            strengthCount
        ];
        window.issuesBreakdownChart.update();
    }
    
    // Update strength trend chart
    if (window.strengthTrendChart) {
        // Convert strength by date to arrays for chart
        const dates = Object.keys(strengthByDate).sort();
        const avgStrengths = dates.map(date => {
            return strengthByDate[date].total / strengthByDate[date].count;
        });
        
        window.strengthTrendChart.data.labels = dates;
        window.strengthTrendChart.data.datasets[0].data = avgStrengths;
        window.strengthTrendChart.update();
    }
    
    // Update hive comparison chart
    updateHiveComparisonChart(healthChecks);
}

/**
 * Update hive comparison chart
 * 
 * @param {Array} healthChecks - Array of health check objects
 */
function updateHiveComparisonChart(healthChecks) {
    if (!window.hiveComparisonChart) return;
    
    // Group data by hive
    const hiveData = {};
    
    healthChecks.forEach(check => {
        if (!hiveData[check.hiveNumber]) {
            hiveData[check.hiveNumber] = {
                strength: [],
                queenPresent: [],
                diseaseSymptoms: [],
                pestProblems: []
            };
        }
        
        hiveData[check.hiveNumber].strength.push(parseInt(check.colonyStrength));
        hiveData[check.hiveNumber].queenPresent.push(check.queenPresent == 1 ? 1 : 0);
        hiveData[check.hiveNumber].diseaseSymptoms.push(check.diseaseSymptoms ? 0 : 1); // Invert for chart (1 = good)
        hiveData[check.hiveNumber].pestProblems.push(check.pestProblems ? 0 : 1); // Invert for chart (1 = good)
    });
    
    // Create datasets for chart
    const datasets = [];
    const colors = [
        'rgb(255, 99, 132)',
        'rgb(54, 162, 235)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(153, 102, 255)',
        'rgb(255, 159, 64)'
    ];
    
    let colorIndex = 0;
    
    Object.entries(hiveData).forEach(([hiveNumber, data]) => {
        // Calculate averages
        const avgStrength = data.strength.reduce((sum, val) => sum + val, 0) / data.strength.length;
        const avgQueenPresent = data.queenPresent.reduce((sum, val) => sum + val, 0) / data.queenPresent.length * 10; // Scale to 0-10
        const avgDiseaseResistance = data.diseaseSymptoms.reduce((sum, val) => sum + val, 0) / data.diseaseSymptoms.length * 10; // Scale to 0-10
        const avgPestResistance = data.pestProblems.reduce((sum, val) => sum + val, 0) / data.pestProblems.length * 10; // Scale to 0-10
        
        // Calculate overall health (average of all metrics)
        const overallHealth = (avgStrength + avgQueenPresent + avgDiseaseResistance + avgPestResistance) / 4;
        
        datasets.push({
            label: `Hive #${hiveNumber}`,
            data: [avgStrength, avgQueenPresent, avgDiseaseResistance, avgPestResistance, overallHealth],
            backgroundColor: `${colors[colorIndex % colors.length]}44`,
            borderColor: colors[colorIndex % colors.length],
            pointBackgroundColor: colors[colorIndex % colors.length],
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: colors[colorIndex % colors.length]
        });
        
        colorIndex++;
    });
    
    // Update chart
    window.hiveComparisonChart.data.datasets = datasets;
    window.hiveComparisonChart.update();
    
    // Update legend
    updateHiveComparisonLegend(datasets);
}

/**
 * Update hive comparison legend
 * 
 * @param {Array} datasets - Chart datasets
 */
function updateHiveComparisonLegend(datasets) {
    const legendContainer = document.getElementById('hiveComparisonLegend');
    if (!legendContainer) return;
    
    // Clear existing legend
    legendContainer.innerHTML = '';
    
    // Create legend items
    datasets.forEach(dataset => {
        const item = document.createElement('div');
        item.className = 'd-flex align-items-center mb-2';
        
        const colorBox = document.createElement('div');
        colorBox.style.width = '20px';
        colorBox.style.height = '20px';
        colorBox.style.backgroundColor = dataset.borderColor;
        colorBox.style.marginRight = '10px';
        
        const label = document.createElement('span');
        label.textContent = dataset.label;
        
        item.appendChild(colorBox);
        item.appendChild(label);
        legendContainer.appendChild(item);
    });
}
