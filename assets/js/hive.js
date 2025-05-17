/**
 * Hive Management JavaScript
 * 
 * This file handles all functionality related to hive management using the Fetch API
 * to interact with the backend database utilities.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the hives page
    if (document.getElementById('hives')) {
        initHiveManagement();
    }
});

/**
 * Initialize hive management functionality
 */
function initHiveManagement() {
    // Add hive form submission
    const addHiveForm = document.getElementById('addHiveForm');
    if (addHiveForm) {
        addHiveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addHive(this);
        });
    }
    
    // Edit hive form submission
    const editHiveForm = document.getElementById('editHiveForm');
    if (editHiveForm) {
        editHiveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateHive(this);
        });
    }
    
    // Delete hive buttons
    document.querySelectorAll('.delete-hive-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const hiveId = this.getAttribute('data-hive-id');
            if (confirm('Are you sure you want to delete this hive?')) {
                deleteHive(hiveId);
            }
        });
    });
    
    // View hive buttons
    document.querySelectorAll('.view-hive-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const hiveId = this.getAttribute('data-hive-id');
            viewHive(hiveId);
        });
    });
    
    // Edit hive buttons
    document.querySelectorAll('.edit-hive-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const hiveId = this.getAttribute('data-hive-id');
            showEditHiveModal(hiveId);
        });
    });
    
    // Load hives data for charts if they exist
    if (document.getElementById('hiveStatusChart') || document.getElementById('hiveAgeChart')) {
        loadHives().then(hives => {
            if (hives.length > 0) {
                updateHiveCharts(hives);
            }
        });
    }
}

/**
 * Load all hives from the API
 * 
 * @returns {Promise<Array>} - Promise resolving to array of hives
 */
async function loadHives() {
    try {
        console.log('Loading hives...');
        
        const response = await fetch('api/hives/get_hives.php');
        const responseData = await response.json();
        
        console.log('Load hives response:', responseData);
        
        if (responseData.success) {
            return responseData.data;
        } else {
            throw new Error(responseData.message || 'Failed to load hives');
        }
    } catch (error) {
        console.error('Error loading hives:', error);
        showNotification('Failed to load hives: ' + error.message, 'danger');
        return [];
    }
}

/**
 * Add a new hive
 * 
 * @param {HTMLFormElement} form - The form element
 */
async function addHive(form) {
    try {
        // Get form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        console.log('Adding hive with data:', data);
        
        // Add hive via API
        const response = await fetch('api/hives/add_hive.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        console.log('Add hive response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Hive added successfully!', 'success');
            
            // Reset form
            form.reset();
            
            // Close modal if it exists
            const modal = bootstrap.Modal.getInstance(document.getElementById('addHiveModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reload page to show new data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(responseData.message || 'Failed to add hive');
        }
    } catch (error) {
        console.error('Error adding hive:', error);
        showNotification('Failed to add hive: ' + error.message, 'danger');
    }
}

/**
 * Update an existing hive
 * 
 * @param {HTMLFormElement} form - The form element
 */
async function updateHive(form) {
    try {
        // Get form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        console.log('Updating hive with data:', data);
        
        // Update hive via API
        const response = await fetch('api/hives/update_hive.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        console.log('Update hive response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Hive updated successfully!', 'success');
            
            // Close modal if it exists
            const modal = bootstrap.Modal.getInstance(document.getElementById('editHiveModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(responseData.message || 'Failed to update hive');
        }
    } catch (error) {
        console.error('Error updating hive:', error);
        showNotification('Failed to update hive: ' + error.message, 'danger');
    }
}

/**
 * Delete a hive
 * 
 * @param {string} hiveId - The ID of the hive to delete
 */
async function deleteHive(hiveId) {
    try {
        console.log('Deleting hive with ID:', hiveId);
        
        // Delete hive via API
        const response = await fetch(`api/hives/delete_hive.php?id=${hiveId}`, {
            method: 'DELETE'
        });
        
        const responseData = await response.json();
        console.log('Delete hive response:', responseData);
        
        if (responseData.success) {
            // Show success message
            showNotification('Hive deleted successfully!', 'success');
            
            // Remove the row from the table
            const row = document.querySelector(`tr[data-hive-id="${hiveId}"]`);
            if (row) {
                row.remove();
            }
            
            // Reload page to update charts
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(responseData.message || 'Failed to delete hive');
        }
    } catch (error) {
        console.error('Error deleting hive:', error);
        showNotification('Failed to delete hive: ' + error.message, 'danger');
    }
}

/**
 * View hive details
 * 
 * @param {string} hiveId - The ID of the hive to view
 */
async function viewHive(hiveId) {
    try {
        console.log('Viewing hive with ID:', hiveId);
        
        // Get hive details via API
        const response = await fetch(`api/hives/get_hives.php?id=${hiveId}`);
        const responseData = await response.json();
        
        console.log('View hive response:', responseData);
        
        if (!responseData.success) {
            throw new Error(responseData.message || 'Failed to load hive details');
        }
        
        const hive = responseData.data;
        
        // Show hive details in modal
        const modal = new bootstrap.Modal(document.getElementById('viewHiveModal'));
        
        // Set modal title
        const modalTitle = document.querySelector('#viewHiveModal .modal-title');
        if (modalTitle) {
            modalTitle.textContent = `Hive #${hive.hiveNumber}`;
        }
        
        // Populate modal content
        const modalBody = document.querySelector('#viewHiveModal .modal-body');
        if (modalBody) {
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Hive Number:</strong> ${hive.hiveNumber}</p>
                        <p><strong>Location:</strong> ${hive.location}</p>
                        <p><strong>Date Established:</strong> ${hive.dateEstablished}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Queen Age (months):</strong> ${hive.queenAge || 'N/A'}</p>
                        <p><strong>Status:</strong> ${hive.status}</p>
                        <p><strong>Notes:</strong> ${hive.notes || 'None'}</p>
                    </div>
                </div>
            `;
        }
        
        modal.show();
    } catch (error) {
        console.error('Error viewing hive:', error);
        showNotification('Failed to load hive details: ' + error.message, 'danger');
    }
}

/**
 * Show edit hive modal with pre-filled data
 * 
 * @param {string} hiveId - The ID of the hive to edit
 */
async function showEditHiveModal(hiveId) {
    try {
        console.log('Loading hive data for editing, ID:', hiveId);
        
        // Get hive details via API
        const response = await fetch(`api/hives/get_hives.php?id=${hiveId}`);
        const responseData = await response.json();
        
        console.log('Get hive for edit response:', responseData);
        
        if (!responseData.success) {
            throw new Error(responseData.message || 'Failed to load hive details');
        }
        
        const hive = responseData.data;
        
        // Show edit modal
        const modal = new bootstrap.Modal(document.getElementById('editHiveModal'));
        const form = document.getElementById('editHiveForm');
        
        if (form) {
            // Fill form fields with hive data
            form.elements['hiveID'].value = hive.hiveID;
            form.elements['hiveNumber'].value = hive.hiveNumber;
            form.elements['location'].value = hive.location;
            form.elements['queenAge'].value = hive.queenAge || '';
            form.elements['notes'].value = hive.notes || '';
        }
        
        modal.show();
    } catch (error) {
        console.error('Error showing edit hive modal:', error);
        showNotification('Failed to load hive details: ' + error.message, 'danger');
    }
}

/**
 * Update hive charts
 * 
 * @param {Array} hives - Array of hive objects
 */
function updateHiveCharts(hives) {
    try {
        // Update hive status chart
        const statusChart = document.getElementById('hiveStatusChart');
        if (statusChart) {
            const statusCounts = {
                active: hives.filter(hive => hive.status === 'Active').length,
                inactive: hives.filter(hive => hive.status === 'Inactive').length,
                maintenance: hives.filter(hive => hive.status === 'Maintenance').length
            };
            
            new Chart(statusChart, {
                type: 'pie',
                data: {
                    labels: ['Active', 'Inactive', 'Maintenance'],
                    datasets: [{
                        data: [statusCounts.active, statusCounts.inactive, statusCounts.maintenance],
                        backgroundColor: ['#28a745', '#dc3545', '#ffc107']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        }
        
        // Update hive age chart
        const ageChart = document.getElementById('hiveAgeChart');
        if (ageChart) {
            // Group hives by queen age
            const ageGroups = {
                'New (0-6 months)': hives.filter(hive => hive.queenAge >= 0 && hive.queenAge <= 6).length,
                'Mid-age (7-12 months)': hives.filter(hive => hive.queenAge > 6 && hive.queenAge <= 12).length,
                'Old (13+ months)': hives.filter(hive => hive.queenAge > 12).length,
                'Unknown': hives.filter(hive => !hive.queenAge).length
            };
            
            new Chart(ageChart, {
                type: 'bar',
                data: {
                    labels: Object.keys(ageGroups),
                    datasets: [{
                        label: 'Queen Age Distribution',
                        data: Object.values(ageGroups),
                        backgroundColor: ['#4caf50', '#2196f3', '#ff9800', '#9e9e9e']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }]
                    },
                    legend: {
                        display: false
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error updating hive charts:', error);
    }
}

/**
 * Show notification message
 * 
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, danger, warning, info)
 */
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show notification`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add notification to container
    const container = document.querySelector('.notification-container');
    if (container) {
        container.appendChild(notification);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 150);
        }, 5000);
    } else {
        console.warn('Notification container not found');
        alert(message);
    }
}
