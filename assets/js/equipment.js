/**
 * Equipment Management JavaScript
 * 
 * This file handles all functionality related to equipment management
 */

document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize equipment management if we're on the equipment page
        const equipmentSection = document.getElementById('equipment');
        if (equipmentSection) {
            initEquipmentManagement();
        }
    } catch (error) {
        console.error('Error initializing equipment management:', error);
    }
});

/**
 * Initialize equipment management functionality
 */
function initEquipmentManagement() {
    try {
        // Add equipment form submission
        const addEquipmentForm = document.getElementById('addEquipmentForm');
        if (addEquipmentForm) {
            addEquipmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                addEquipment(this);
            });
        }
        
        // Edit equipment form submission
        const editEquipmentForm = document.getElementById('editEquipmentForm');
        if (editEquipmentForm) {
            editEquipmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                updateEquipment(this);
            });
        }
        
        // Delete equipment buttons
        const deleteButtons = document.querySelectorAll('.delete-equipment-btn');
        if (deleteButtons && deleteButtons.length > 0) {
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const equipmentId = this.getAttribute('data-equipment-id');
                    if (confirm('Are you sure you want to delete this equipment?')) {
                        deleteEquipment(equipmentId);
                    }
                });
            });
        }
        
        // Edit equipment buttons
        const editButtons = document.querySelectorAll('.edit-equipment-btn');
        if (editButtons && editButtons.length > 0) {
            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const equipmentId = this.getAttribute('data-equipment-id');
                    showEditEquipmentModal(equipmentId);
                });
            });
        }
        
        // Equipment type filter
        const equipmentTypeFilter = document.getElementById('equipmentTypeFilter');
        if (equipmentTypeFilter) {
            equipmentTypeFilter.addEventListener('change', function() {
                filterEquipmentByType(this.value);
            });
        }
        
        // Initialize inventory chart
        initInventoryChart();
    } catch (error) {
        console.error('Error in equipment management initialization:', error);
    }
}

/**
 * Filter equipment by type
 * 
 * @param {string} type - Equipment type to filter by
 */
function filterEquipmentByType(type) {
    try {
        const rows = document.querySelectorAll('#equipmentContainer table tbody tr');
        
        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            
            if (!type || rowType === type) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update "no results" message
        const visibleRows = document.querySelectorAll('#equipmentContainer table tbody tr:not([style*="display: none"])');
        const noResultsMsg = document.querySelector('#equipmentContainer .no-results-message');
        
        if (visibleRows.length === 0) {
            if (!noResultsMsg) {
                const message = document.createElement('div');
                message.className = 'alert alert-info no-results-message';
                message.textContent = 'No equipment found for the selected type.';
                document.getElementById('equipmentContainer').appendChild(message);
            }
        } else if (noResultsMsg) {
            noResultsMsg.remove();
        }
    } catch (error) {
        console.error('Error filtering equipment:', error);
    }
}

/**
 * Initialize inventory chart
 */
async function initInventoryChart() {
    try {
        // Fetch equipment data
        const response = await fetch('api/equipment/index.php?action=getAll');
        const responseData = await response.json();
        
        if (responseData.success) {
            // Create summary data by type
            const summaryData = {};
            
            responseData.data.forEach(item => {
                if (!summaryData[item.type]) {
                    summaryData[item.type] = {
                        count: 0,
                        value: 0
                    };
                }
                
                summaryData[item.type].count += parseInt(item.quantity) || 0;
            });
            
            // Create chart
            createInventoryChart(summaryData);
        }
    } catch (error) {
        console.error('Error initializing inventory chart:', error);
    }
}

/**
 * Create inventory chart
 * 
 * @param {Object} summaryData - Equipment summary data by type
 */
function createInventoryChart(summaryData) {
    try {
        const ctx = document.getElementById('inventoryChart');
        if (!ctx) return;
        
        const types = Object.keys(summaryData);
        const counts = types.map(type => summaryData[type].count);
        
        // Generate colors
        const colors = types.map((_, index) => {
            const hue = (index * 137) % 360; // Golden ratio approximation for good color distribution
            return `hsl(${hue}, 70%, 60%)`;
        });
        
        // Create chart
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: types,
                datasets: [{
                    label: 'Quantity',
                    data: counts,
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('60%', '50%')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            precision: 0
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return `${data.datasets[0].label}: ${tooltipItem.yLabel}`;
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error creating inventory chart:', error);
    }
}

/**
 * Add new equipment
 * 
 * @param {HTMLFormElement} form - The form element
 */
async function addEquipment(form) {
    try {
        // Get form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        // Add equipment via API
        const response = await fetch('api/equipment/index.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        
        if (responseData.success) {
            // Show success message
            showNotification('Equipment added successfully!', 'success');
            
            // Reset form
            form.reset();
            
            // Close modal if it exists
            const modal = bootstrap.Modal.getInstance(document.getElementById('addEquipmentModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reload page to show new data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(responseData.message || 'Failed to add equipment');
        }
    } catch (error) {
        console.error('Error adding equipment:', error);
        showNotification('Failed to add equipment: ' + error.message, 'danger');
    }
}

/**
 * Update existing equipment
 * 
 * @param {HTMLFormElement} form - The form element
 */
async function updateEquipment(form) {
    try {
        // Get form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        // Update equipment via API
        const response = await fetch('api/equipment/index.php?action=update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        
        if (responseData.success) {
            // Show success message
            showNotification('Equipment updated successfully!', 'success');
            
            // Close modal if it exists
            const modal = bootstrap.Modal.getInstance(document.getElementById('editEquipmentModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(responseData.message || 'Failed to update equipment');
        }
    } catch (error) {
        console.error('Error updating equipment:', error);
        showNotification('Failed to update equipment: ' + error.message, 'danger');
    }
}

/**
 * Delete equipment
 * 
 * @param {string} equipmentId - The ID of the equipment to delete
 */
async function deleteEquipment(equipmentId) {
    try {
        // Delete equipment via API
        const response = await fetch(`api/equipment/index.php?action=delete&id=${equipmentId}`, {
            method: 'DELETE'
        });
        
        const responseData = await response.json();
        
        if (responseData.success) {
            // Show success message
            showNotification('Equipment deleted successfully!', 'success');
            
            // Remove the row from the table
            const row = document.querySelector(`tr[data-equipment-id="${equipmentId}"]`);
            if (row) {
                row.remove();
            }
            
            // Update inventory chart
            initInventoryChart();
        } else {
            throw new Error(responseData.message || 'Failed to delete equipment');
        }
    } catch (error) {
        console.error('Error deleting equipment:', error);
        showNotification('Failed to delete equipment: ' + error.message, 'danger');
    }
}

/**
 * Show edit equipment modal with pre-filled data
 * 
 * @param {string} equipmentId - The ID of the equipment to edit
 */
async function showEditEquipmentModal(equipmentId) {
    try {
        // Get equipment details via API
        const response = await fetch(`api/equipment/index.php?action=get&id=${equipmentId}`);
        const responseData = await response.json();
        
        if (!responseData.success) {
            throw new Error(responseData.message || 'Failed to load equipment details');
        }
        
        const equipment = responseData.data;
        
        // Show edit modal
        const modal = new bootstrap.Modal(document.getElementById('editEquipmentModal'));
        const form = document.getElementById('editEquipmentForm');
        
        if (form) {
            // Fill form fields with equipment data
            form.elements['equipmentID'].value = equipment.equipmentID;
            form.elements['name'].value = equipment.name;
            form.elements['type'].value = equipment.type;
            form.elements['quantity'].value = equipment.quantity;
            form.elements['condition_status'].value = equipment.condition_status;
            form.elements['purchaseDate'].value = equipment.purchaseDate || '';
            form.elements['notes'].value = equipment.notes || '';
        }
        
        modal.show();
    } catch (error) {
        console.error('Error loading equipment for editing:', error);
        showNotification('Failed to load equipment details: ' + error.message, 'danger');
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
