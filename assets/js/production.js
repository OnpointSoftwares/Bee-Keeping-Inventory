/**
 * Production Management JavaScript
 * 
 * This file handles all functionality related to honey production management
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize production management if we're on the production page
        const productionSection = document.getElementById('production');
        if (productionSection) {
            initProductionManagement();
        }
    } catch (error) {
        console.error('Error initializing production management:', error);
    }
});

/**
 * Initialize production management functionality
 */
function initProductionManagement() {
    try {
        // Add production form submission
        const addProductionForm = document.getElementById('addProductionForm');
        if (addProductionForm) {
            addProductionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                addProductionRecord(this);
            });
        }
        
        // Edit production form submission
        const editProductionForm = document.getElementById('editProductionForm');
        if (editProductionForm) {
            editProductionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                updateProductionRecord(this);
            });
        }
        
        // Delete production buttons
        const deleteProductionBtns = document.querySelectorAll('.delete-production-btn');
        if (deleteProductionBtns.length > 0) {
            deleteProductionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const productionId = this.getAttribute('data-production-id');
                    if (confirm('Are you sure you want to delete this production record?')) {
                        deleteProductionRecord(productionId);
                    }
                });
            });
        }
        
        // Edit production buttons
        const editProductionBtns = document.querySelectorAll('.edit-production-btn');
        if (editProductionBtns.length > 0) {
            editProductionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const productionId = this.getAttribute('data-production-id');
                    showEditProductionModal(productionId);
                });
            });
        }
        
        // Initialize production filters
        initProductionFilters();
        
        // Load production data
        loadProductionData();
    } catch (error) {
        console.error('Error initializing production management:', error);
    }
}

/**
 * Initialize production filters
 */
function initProductionFilters() {
    try {
        const dateRangeFilter = document.getElementById('productionDateRange');
        const hiveFilter = document.getElementById('hiveFilter');
        const typeFilter = document.getElementById('honeyTypeFilter');
        
        if (dateRangeFilter && hiveFilter && typeFilter) {
            [dateRangeFilter, hiveFilter, typeFilter].forEach(filter => {
                filter.addEventListener('change', function() {
                    loadProductionData({
                        dateRange: dateRangeFilter.value,
                        hiveID: hiveFilter.value,
                        type: typeFilter.value
                    });
                });
            });
        }
    } catch (error) {
        console.error('Error initializing production filters:', error);
    }
}

/**
 * Load production data from the API
 * 
 * @param {Object} filters - Filter parameters
 */
async function loadProductionData(filters = {}) {
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
                params.startDate = formatDate(startDate);
                params.endDate = formatDate(now);
            }
        }
        
        if (filters.type) {
            params.type = filters.type;
        }
        
        // Get production data via API
        const response = await fetchData('production', 'getAll', params);
        
        // Update production analytics
        updateProductionAnalytics(response.data, response.summary);
        
    } catch (error) {
        console.error('Error loading production data:', error);
        showNotification('Failed to load production data: ' + error.message, 'danger');
    }
}

/**
 * Update production analytics with filtered data
 * 
 * @param {Array} productionData - Production records
 * @param {Object} summary - Production summary data
 */
function updateProductionAnalytics(productionData, summary) {
    try {
        const analyticsContainer = document.getElementById('productionAnalyticsResults');
        if (!analyticsContainer) return;
        
        if (productionData.length === 0) {
            analyticsContainer.innerHTML = '<div class="alert alert-info">No production records match the selected filters.</div>';
            return;
        }
        
        // Create analytics content
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Production Summary</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Production:</strong> ${summary.totalQuantity.toFixed(1)} kg</p>
                            <p><strong>Average per Harvest:</strong> ${summary.averageQuantity.toFixed(1)} kg</p>
                            <p><strong>Total Records:</strong> ${summary.recordCount}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Production by Type</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
        `;
        
        summary.byType.forEach(item => {
            html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${item.type}
                    <span class="badge bg-primary rounded-pill">${item.quantity.toFixed(1)} kg</span>
                </li>
            `;
        });
        
        html += `
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        analyticsContainer.innerHTML = html;
        
        // Update charts
        updateProductionCharts(productionData, summary);
        
    } catch (error) {
        console.error('Error updating production analytics:', error);
    }
}

/**
 * Update production charts with filtered data
 * 
 * @param {Array} productionData - Production records
 * @param {Object} summary - Production summary data
 */
function updateProductionCharts(productionData, summary) {
    try {
        // Update production trend chart
        updateProductionTrendChart(productionData);
        
        // Update honey type chart
        updateHoneyTypeChart(summary.byType);
        
    } catch (error) {
        console.error('Error updating production charts:', error);
    }
}

/**
 * Update production trend chart
 * 
 * @param {Array} productionData - Production records
 */
function updateProductionTrendChart(productionData) {
    try {
        const ctx = document.getElementById('productionTrendChart');
        if (!ctx) return;
        
        // Group data by month
        const productionByMonth = {};
        
        productionData.forEach(record => {
            const date = new Date(record.harvestDate);
            const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            
            if (!productionByMonth[monthYear]) {
                productionByMonth[monthYear] = 0;
            }
            
            productionByMonth[monthYear] += parseFloat(record.quantity);
        });
        
        // Sort months chronologically
        const sortedMonths = Object.keys(productionByMonth).sort();
        
        // Format labels for display
        const labels = sortedMonths.map(month => {
            const [year, monthNum] = month.split('-');
            const date = new Date(year, monthNum - 1, 1);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        
        // Get data values
        const data = sortedMonths.map(month => productionByMonth[month]);
        
        // Create or update chart
        if (window.productionTrendChart) {
            window.productionTrendChart.data.labels = labels;
            window.productionTrendChart.data.datasets[0].data = data;
            window.productionTrendChart.update();
        } else {
            window.productionTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Honey Production (kg)',
                        data: data,
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 2,
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
                            title: {
                                display: true,
                                text: 'Quantity (kg)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    }
                }
            });
        }
        
    } catch (error) {
        console.error('Error updating production trend chart:', error);
    }
}

/**
 * Update honey type chart
 * 
 * @param {Array} typeData - Honey production by type
 */
function updateHoneyTypeChart(typeData) {
    try {
        const ctx = document.getElementById('honeyTypeChart');
        if (!ctx) return;
        
        // Prepare data for chart
        const labels = typeData.map(item => item.type);
        const data = typeData.map(item => item.quantity);
        
        // Generate colors
        const backgroundColors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)'
        ];
        
        // Create or update chart
        if (window.honeyTypeChart) {
            window.honeyTypeChart.data.labels = labels;
            window.honeyTypeChart.data.datasets[0].data = data;
            window.honeyTypeChart.update();
        } else {
            window.honeyTypeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors.slice(0, labels.length),
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
        
    } catch (error) {
        console.error('Error updating honey type chart:', error);
    }
}

/**
 * Add a new production record
 * 
 * @param {HTMLFormElement} form - The form element
 */
async function addProductionRecord(form) {
    try {
        // Get form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        console.log('Adding production record with data:', data);
        
        // Add production record via API
        const response = await fetch('api/production/add_production.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        console.log('Add production response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Production record added successfully!', 'success');
            
            // Reset form
            form.reset();
            
            // Close modal if it exists
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductionModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reload page to show new data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(responseData.message || 'Failed to add production record');
        }
    } catch (error) {
        console.error('Error adding production record:', error);
        showNotification('Failed to add production record: ' + error.message, 'danger');
    }
}

/**
 * Update an existing production record
 * 
 * @param {HTMLFormElement} form - The form element
 */
async function updateProductionRecord(form) {
    try {
        // Get form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        console.log('Updating production record with data:', data);
        
        // Update production record via API
        const response = await fetch('api/production/update_production.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        console.log('Update production response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Production record updated successfully!', 'success');
            
            // Close modal if it exists
            const modal = bootstrap.Modal.getInstance(document.getElementById('editProductionModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(responseData.message || 'Failed to update production record');
        }
    } catch (error) {
        console.error('Error updating production record:', error);
        showNotification('Failed to update production record: ' + error.message, 'danger');
    }
}

/**
 * Delete a production record
 * 
 * @param {string} productionId - The ID of the production record to delete
 */
async function deleteProductionRecord(productionId) {
    try {
        console.log('Deleting production record with ID:', productionId);
        
        // Delete production record via API
        const response = await fetch(`api/production/delete_production.php?id=${productionId}`, {
            method: 'DELETE'
        });
        
        const responseData = await response.json();
        console.log('Delete production response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Production record deleted successfully!', 'success');
            
            // Remove the row from the table
            const row = document.querySelector(`tr[data-production-id="${productionId}"]`);
            if (row) {
                row.remove();
            }
            
            // Reload production data to update charts
            loadProductionData();
        } else {
            throw new Error(responseData.message || 'Failed to delete production record');
        }
    } catch (error) {
        console.error('Error deleting production record:', error);
        showNotification('Failed to delete production record: ' + error.message, 'danger');
    }
}

/**
 * Show edit production modal with pre-filled data
 * 
 * @param {string} productionId - The ID of the production record to edit
 */
async function showEditProductionModal(productionId) {
    try {
        // Get production record details via API
        const response = await fetchData('production', 'get', { id: productionId });
        
        const production = response.data;
        
        // Show edit modal
        const modal = new bootstrap.Modal(document.getElementById('editProductionModal'));
        const form = document.getElementById('editProductionForm');
        
        if (form) {
            // Fill form fields with production data
            form.elements['productionID'].value = production.productionID;
            form.elements['hiveID'].value = production.hiveID;
            form.elements['harvestDate'].value = production.harvestDate;
            form.elements['quantity'].value = production.quantity;
            form.elements['type'].value = production.type;
            form.elements['quality'].value = production.quality || 'Standard';
            form.elements['notes'].value = production.notes || '';
        }
        
        modal.show();
        
    } catch (error) {
        console.error('Error loading production record for editing:', error);
        showNotification('Failed to load production record details: ' + error.message, 'danger');
    }
}
