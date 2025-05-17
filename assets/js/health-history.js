/**
 * Health History JavaScript
 * Handles all functionality related to the health history section
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize health history functionality if the section exists
    if (document.getElementById('health_history')) {
        initHealthHistoryFilters();
        initHealthCharts();
        initHealthHistoryActions();
    }
});

/**
 * Initialize health history filters
 */
function initHealthHistoryFilters() {
    // Get filter elements
    const hiveFilter = document.getElementById('healthHistoryHiveFilter');
    const dateFilter = document.getElementById('healthHistoryDateFilter');
    const statusFilter = document.getElementById('healthHistoryStatusFilter');
    const issueFilter = document.getElementById('healthHistoryIssueFilter');
    
    // Add event listeners to filters
    if (hiveFilter && dateFilter && statusFilter && issueFilter) {
        [hiveFilter, dateFilter, statusFilter, issueFilter].forEach(filter => {
            filter.addEventListener('change', filterHealthHistory);
        });
    }
    
    // Initialize export button
    const exportBtn = document.getElementById('exportHealthHistoryBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportHealthHistory);
    }
}

/**
 * Filter health history based on selected filters
 */
function filterHealthHistory() {
    const hiveFilter = document.getElementById('healthHistoryHiveFilter').value;
    const dateFilter = document.getElementById('healthHistoryDateFilter').value;
    const statusFilter = document.getElementById('healthHistoryStatusFilter').value;
    const issueFilter = document.getElementById('healthHistoryIssueFilter').value;
    
    // Get all rows in the health history table
    const rows = document.querySelectorAll('#healthHistoryTable tbody tr');
    
    // Filter count for analytics
    let visibleCount = 0;
    let healthyCount = 0;
    let issuesCount = 0;
    let criticalCount = 0;
    
    // Issue counts for analytics
    let diseaseCount = 0;
    let pestCount = 0;
    let queenCount = 0;
    let strengthCount = 0;
    
    // Strength values for average calculation
    let strengthValues = [];
    
    // Filter data for charts
    let chartData = {
        hives: {},
        dates: [],
        strengths: []
    };
    
    rows.forEach(row => {
        const hiveId = row.getAttribute('data-hive-id');
        const status = row.getAttribute('data-status');
        const issues = row.getAttribute('data-issues') || '';
        const checkDate = row.querySelector('td:nth-child(2)').textContent;
        
        // Parse date for filtering
        const rowDate = new Date(checkDate);
        const now = new Date();
        const daysDiff = Math.floor((now - rowDate) / (1000 * 60 * 60 * 24));
        
        // Check if row matches all filters
        let showRow = true;
        
        // Hive filter
        if (hiveFilter !== 'all' && hiveId !== hiveFilter) {
            showRow = false;
        }
        
        // Date filter
        if (dateFilter !== 'all' && daysDiff > parseInt(dateFilter)) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter !== 'all' && status !== statusFilter) {
            showRow = false;
        }
        
        // Issue filter
        if (issueFilter !== 'all') {
            if (issueFilter === 'disease' && !issues.includes('disease')) {
                showRow = false;
            } else if (issueFilter === 'pest' && !issues.includes('pest')) {
                showRow = false;
            } else if (issueFilter === 'queen' && !issues.includes('queen')) {
                showRow = false;
            } else if (issueFilter === 'strength' && !issues.includes('strength')) {
                showRow = false;
            }
        }
        
        // Show or hide row based on filters
        if (showRow) {
            row.style.display = '';
            visibleCount++;
            
            // Count by status for analytics
            if (status === 'healthy') {
                healthyCount++;
            } else if (status === 'issues detected') {
                issuesCount++;
            } else if (status === 'critical') {
                criticalCount++;
            }
            
            // Count issues for analytics
            if (issues.includes('disease')) {
                diseaseCount++;
            }
            if (issues.includes('pest')) {
                pestCount++;
            }
            if (issues.includes('queen')) {
                queenCount++;
            }
            if (issues.includes('strength')) {
                strengthCount++;
            }
            
            // Get strength value for average calculation
            const strengthElement = row.querySelector('td:nth-child(3) .progress-bar');
            if (strengthElement) {
                const strength = parseInt(strengthElement.getAttribute('aria-valuenow'));
                strengthValues.push(strength);
            }
            
            // Collect data for charts
            const hiveNumber = row.querySelector('td:nth-child(1)').textContent;
            const strength = parseInt(row.querySelector('td:nth-child(3) .progress-bar').getAttribute('aria-valuenow'));
            
            // Store data for charts
            if (!chartData.hives[hiveNumber]) {
                chartData.hives[hiveNumber] = {
                    dates: [],
                    strengths: []
                };
            }
            
            chartData.hives[hiveNumber].dates.push(checkDate);
            chartData.hives[hiveNumber].strengths.push(strength);
            
            if (!chartData.dates.includes(checkDate)) {
                chartData.dates.push(checkDate);
            }
            chartData.strengths.push(strength);
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update charts with filtered data
    updateHealthCharts(chartData, {
        healthy: healthyCount,
        issues: issuesCount,
        critical: criticalCount
    }, {
        disease: diseaseCount,
        pest: pestCount,
        queen: queenCount,
        strength: strengthCount
    });
    
    // Show message if no results
    const noResultsMsg = document.querySelector('#healthHistoryTableContainer .no-results-message');
    if (visibleCount === 0) {
        if (!noResultsMsg) {
            const message = document.createElement('div');
            message.className = 'alert alert-info no-results-message';
            message.textContent = 'No health checks match the selected filters.';
            document.getElementById('healthHistoryTableContainer').appendChild(message);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

/**
 * Initialize health charts
 */
function initHealthCharts() {
    // Create initial charts
    createHealthStatusChart();
    createStrengthTrendChart();
    createIssuesBreakdownChart();
    createHiveComparisonChart();
    
    // Initialize with all data
    filterHealthHistory();
}

/**
 * Create health status distribution chart
 */
function createHealthStatusChart() {
    const ctx = document.getElementById('healthStatusChart');
    if (!ctx) return;
    
    window.healthStatusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Healthy', 'Issues Detected', 'Critical'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgb(40, 167, 69)',
                    'rgb(255, 193, 7)',
                    'rgb(220, 53, 69)'
                ],
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

/**
 * Create colony strength trend chart
 */
function createStrengthTrendChart() {
    const ctx = document.getElementById('strengthTrendChart');
    if (!ctx) return;
    
    window.strengthTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Average Colony Strength',
                data: [],
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10,
                    title: {
                        display: true,
                        text: 'Strength (1-10)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Check Date'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

/**
 * Create issues breakdown chart
 */
function createIssuesBreakdownChart() {
    const ctx = document.getElementById('issuesBreakdownChart');
    if (!ctx) return;
    
    window.issuesBreakdownChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Disease', 'Pests', 'Queen Issues', 'Low Strength'],
            datasets: [{
                label: 'Number of Issues',
                data: [0, 0, 0, 0],
                backgroundColor: [
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(111, 66, 193, 0.7)',
                    'rgba(23, 162, 184, 0.7)'
                ],
                borderColor: [
                    'rgb(220, 53, 69)',
                    'rgb(255, 193, 7)',
                    'rgb(111, 66, 193)',
                    'rgb(23, 162, 184)'
                ],
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
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

/**
 * Create hive comparison chart
 */
function createHiveComparisonChart() {
    const ctx = document.getElementById('hiveComparisonChart');
    if (!ctx) return;
    
    window.hiveComparisonChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Colony Strength', 'Queen Presence', 'Disease Resistance', 'Pest Resistance', 'Overall Health'],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 10,
                    ticks: {
                        stepSize: 2
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

/**
 * Update health charts with filtered data
 */
function updateHealthCharts(chartData, statusCounts, issueCounts) {
    // Update health status chart
    if (window.healthStatusChart) {
        window.healthStatusChart.data.datasets[0].data = [
            statusCounts.healthy,
            statusCounts.issues,
            statusCounts.critical
        ];
        window.healthStatusChart.update();
    }
    
    // Update issues breakdown chart
    if (window.issuesBreakdownChart) {
        window.issuesBreakdownChart.data.datasets[0].data = [
            issueCounts.disease,
            issueCounts.pest,
            issueCounts.queen,
            issueCounts.strength
        ];
        window.issuesBreakdownChart.update();
    }
    
    // Update strength trend chart
    if (window.strengthTrendChart) {
        // Sort dates chronologically
        const sortedDates = Object.keys(chartData.hives).length > 0 
            ? [...new Set(Object.values(chartData.hives).flatMap(hive => hive.dates))].sort((a, b) => new Date(a) - new Date(b))
            : [];
        
        // Calculate average strength for each date
        const avgStrengths = [];
        sortedDates.forEach(date => {
            let totalStrength = 0;
            let count = 0;
            
            Object.values(chartData.hives).forEach(hive => {
                const index = hive.dates.indexOf(date);
                if (index !== -1) {
                    totalStrength += hive.strengths[index];
                    count++;
                }
            });
            
            avgStrengths.push(count > 0 ? totalStrength / count : 0);
        });
        
        window.strengthTrendChart.data.labels = sortedDates;
        window.strengthTrendChart.data.datasets[0].data = avgStrengths;
        window.strengthTrendChart.update();
    }
    
    // Update hive comparison chart
    if (window.hiveComparisonChart) {
        // Create datasets for each hive
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
        Object.entries(chartData.hives).forEach(([hiveNumber, hiveData]) => {
            // Calculate average metrics for this hive
            const avgStrength = hiveData.strengths.reduce((sum, val) => sum + val, 0) / hiveData.strengths.length;
            
            // Calculate other metrics based on available data
            // This is a simplified example - in a real app, you would calculate these from actual data
            const queenPresence = 10; // Placeholder
            const diseaseResistance = 8; // Placeholder
            const pestResistance = 7; // Placeholder
            const overallHealth = (avgStrength + queenPresence + diseaseResistance + pestResistance) / 4;
            
            datasets.push({
                label: `Hive #${hiveNumber}`,
                data: [avgStrength, queenPresence, diseaseResistance, pestResistance, overallHealth],
                backgroundColor: `${colors[colorIndex % colors.length]}44`,
                borderColor: colors[colorIndex % colors.length],
                pointBackgroundColor: colors[colorIndex % colors.length],
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: colors[colorIndex % colors.length]
            });
            
            colorIndex++;
        });
        
        window.hiveComparisonChart.data.datasets = datasets;
        window.hiveComparisonChart.update();
        
        // Update legend
        updateHiveComparisonLegend(datasets);
    }
}

/**
 * Update hive comparison legend with custom content
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

/**
 * Initialize health history actions
 */
function initHealthHistoryActions() {
    // View health details button
    document.querySelectorAll('.view-health-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const healthId = this.getAttribute('data-health-id');
            viewHealthDetails(healthId);
        });
    });
    
    // Delete health check button
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
 * View health check details
 */
function viewHealthDetails(healthId) {
    // Fetch health check details via AJAX
    fetch(`api/health/get_health_check.php?id=${healthId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show health check details in modal
                const modal = new bootstrap.Modal(document.getElementById('viewHealthCheckModal'));
                
                // Populate modal content
                const detailsContainer = document.getElementById('healthCheckDetails');
                if (detailsContainer) {
                    let content = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Hive:</strong> ${data.data.hiveNumber}</p>
                                <p><strong>Check Date:</strong> ${data.data.checkDate}</p>
                                <p><strong>Colony Strength:</strong> ${data.data.colonyStrength}/10</p>
                                <p><strong>Queen Present:</strong> ${data.data.queenPresent == 1 ? 'Yes' : 'No'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Disease Symptoms:</strong> ${data.data.diseaseSymptoms || 'None'}</p>
                                <p><strong>Pest Problems:</strong> ${data.data.pestProblems || 'None'}</p>
                                <p><strong>Notes:</strong> ${data.data.notes || 'None'}</p>
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
                        showEditHealthCheckModal(data.data);
                    };
                }
                
                modal.show();
            } else {
                alert('Failed to load health check details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading health check details.');
        });
}

/**
 * Show edit health check modal with pre-filled data
 */
function showEditHealthCheckModal(healthData) {
    const modal = new bootstrap.Modal(document.getElementById('editHealthCheckModal'));
    const form = document.getElementById('editHealthCheckForm');
    
    if (form) {
        // Fill form fields with health check data
        form.elements['healthID'].value = healthData.checkID;
        form.elements['hiveID'].value = healthData.hiveID;
        form.elements['checkDate'].value = healthData.checkDate;
        form.elements['colonyStrength'].value = healthData.colonyStrength;
        form.elements['queenPresent'].value = healthData.queenPresent;
        form.elements['diseaseSymptoms'].value = healthData.diseaseSymptoms || '';
        form.elements['pestProblems'].value = healthData.pestProblems || '';
        form.elements['notes'].value = healthData.notes || '';
        
        // Set form submission handler
        form.onsubmit = function(e) {
            e.preventDefault();
            updateHealthCheck(form);
        };
    }
    
    modal.show();
}

/**
 * Update health check
 */
function updateHealthCheck(form) {
    // Get form data
    const formData = new FormData(form);
    
    // Convert to JSON
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });
    
    // Send update request via AJAX
    fetch('api/health/update_health_check.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Health check updated successfully.');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editHealthCheckModal'));
            if (modal) {
                modal.hide();
            }
            
            // Refresh page to show updated data
            window.location.reload();
        } else {
            alert('Failed to update health check: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the health check.');
    });
}

/**
 * Delete health check
 */
function deleteHealthCheck(healthId) {
    // Send delete request via AJAX
    fetch(`api/health/delete_health_check.php?id=${healthId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Health check deleted successfully.');
            
            // Remove row from table
            const row = document.querySelector(`tr[data-health-id="${healthId}"]`);
            if (row) {
                row.remove();
            }
            
            // Refresh charts
            filterHealthHistory();
        } else {
            alert('Failed to delete health check: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the health check.');
    });
}

/**
 * Export health history data
 */
function exportHealthHistory() {
    // Get visible rows
    const rows = document.querySelectorAll('#healthHistoryTable tbody tr:not([style*="display: none"])');
    
    // Create CSV content
    let csvContent = 'Hive,Check Date,Colony Strength,Queen Present,Health Status,Issues\n';
    
    rows.forEach(row => {
        const hive = row.querySelector('td:nth-child(1)').textContent.trim();
        const date = row.querySelector('td:nth-child(2)').textContent.trim();
        const strength = row.querySelector('td:nth-child(3) .progress-bar').textContent.trim();
        const queen = row.querySelector('td:nth-child(4) .badge').textContent.trim();
        const status = row.querySelector('td:nth-child(5) .badge').textContent.trim();
        
        // Get issues as comma-separated list
        const issueElements = row.querySelectorAll('td:nth-child(6) .badge');
        let issues = '';
        issueElements.forEach((el, index) => {
            if (el.textContent.trim() !== 'None') {
                issues += (index > 0 ? ', ' : '') + el.textContent.trim();
            }
        });
        
        // Add row to CSV
        csvContent += `"${hive}","${date}","${strength}","${queen}","${status}","${issues}"\n`;
    });
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'health_history_export.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
