// Equipment Management
document.addEventListener('DOMContentLoaded', function() {
    // Show equipment section when nav link is clicked
    document.querySelector('a[href="#equipment"]').addEventListener('click', function() {
        loadEquipment();
        loadInventoryReport();
    });

    // Initialize equipment type filter
    initializeEquipmentTypeFilter();

    // Add equipment form submission
    document.getElementById('addEquipmentForm')?.addEventListener('submit', handleAddEquipment);

    // Update equipment form submission
    document.getElementById('updateEquipmentForm')?.addEventListener('submit', handleUpdateEquipment);
});

// Load equipment data
async function loadEquipment() {
    try {
        const data = await api.get('equipment', { action: 'getAll' });
        console.log('Equipment API Response:', data); // Log the response
        if (data.success) {
            displayEquipment(data.data); // Pass the actual array of equipment
            updateTypeFilter(data.data);
        } else {
            alert('Error loading equipment: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to load equipment: ' + error.message);
    }
}

// Load and display inventory report
async function loadInventoryReport() {
    try {
        const data = await api.get('equipment', { action: 'getInventoryReport' });
        if (data.success) {
            displayInventoryReport(data.report);
            updateInventoryChart(data.report);
            document.getElementById('totalItems').textContent = data.totalItems || 0;
        } else {
            alert('Error loading inventory report: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to load inventory report: ' + error.message);
    }
}

// Display equipment in table
function displayEquipment(data) {
    const container = document.getElementById('equipmentContainer');
    if (!container) return;

    let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Condition</th>
                    <th>Purchase Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(item => {
        html += `
            <tr>
                <td>${item.name}</td>
                <td>${item.type}</td>
                <td>${item.quantity}</td>
                <td>${item.condition}</td>
                <td>${item.purchaseDate}</td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" onclick="editEquipment(${item.equipmentID})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteEquipment(${item.equipmentID})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    container.innerHTML = html;
}

// Display equipment in table
function displayEquipment(data) {
    const container = document.getElementById('equipmentContainer');
    if (!container) return;

    let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Condition</th>
                    <th>Purchase Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(item => {
        html += `
            <tr>
                <td>${item.name}</td>
                <td>${item.type}</td>
                <td>${item.quantity}</td>
                <td>
                    <span class="status-${getStatusClass(item.condition_status)}">
                        ${item.condition_status}
                    </span>
                </td>
                <td>${formatDate(item.purchaseDate)}</td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" onclick="editEquipment(${item.equipmentID})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteEquipment(${item.equipmentID})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    container.innerHTML = html;
}

// Display inventory report
function displayInventoryReport(report) {
    const container = document.getElementById('inventoryReportContainer');
    if (!container) return;

    let html = `
        <div class="mt-4">
            <h6 class="mb-3">Equipment by Type</h6>
            <div class="list-group">
    `;

    report.forEach(item => {
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">${item.type}</h6>
                    <small class="text-muted">Conditions: ${item.conditions}</small>
                </div>
                <span class="badge bg-primary rounded-pill">${item.totalQuantity}</span>
            </div>
        `;
    });

    html += '</div></div>';
    container.innerHTML = html;
}

// Update inventory chart
function updateInventoryChart(report) {
    const ctx = document.getElementById('inventoryChart')?.getContext('2d');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (window.inventoryChart instanceof Chart) {
        window.inventoryChart.destroy();
    }

    const labels = report.map(item => item.type);
    const data = report.map(item => item.totalQuantity);

    window.inventoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc',
                    '#f6c23e', '#e74a3b', '#858796'
                ]
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Initialize equipment type filter
function initializeEquipmentTypeFilter() {
    const filterSelect = document.getElementById('equipmentTypeFilter');
    if (!filterSelect) return;

    filterSelect.addEventListener('change', function() {
        const type = this.value;
        if (type) {
            fetch('{API_BASE}/equipment/getByType', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `controller=equipment&action=getByType&type=${encodeURIComponent(type)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayEquipment(data);
                } else {
                    showToast('error', 'Error filtering equipment: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to filter equipment');
            });
        } else {
            loadEquipment();
        }
    });
}

// Update type filter options
function updateTypeFilter(data) {
    const filterSelect = document.getElementById('equipmentTypeFilter');
    if (!filterSelect) return;

    const types = [...new Set(data.map(item => item.type))];
    let options = '<option value="">All Types</option>';
    
    types.forEach(type => {
        options += `<option value="${type}">${type}</option>`;
    });
    
    filterSelect.innerHTML = options;
}

// Handle add equipment form submission
function handleAddEquipment(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        controller: 'equipment',
        action: 'add',
        name: formData.get('name'),
        type: formData.get('type'),
        quantity: formData.get('quantity'),
        condition: formData.get('condition'),
        purchaseDate: formData.get('purchaseDate'),
        notes: formData.get('notes')
    };

    fetch('{API_BASE}/equipment/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Equipment added successfully');
            loadEquipment();
            loadInventoryReport();
            $('#addEquipmentModal').modal('hide');
            e.target.reset();
        } else {
            showToast('error', 'Error adding equipment: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to add equipment');
    });
}

// Handle update equipment form submission
function handleUpdateEquipment(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        controller: 'equipment',
        action: 'update',
        equipmentID: formData.get('equipmentID'),
        name: formData.get('name'),
        type: formData.get('type'),
        quantity: formData.get('quantity'),
        condition: formData.get('condition'),
        notes: formData.get('notes')
    };

    fetch('{API_BASE}/equipment/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Equipment updated successfully');
            loadEquipment();
            loadInventoryReport();
            $('#updateEquipmentModal').modal('hide');
        } else {
            showToast('error', 'Error updating equipment: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to update equipment');
    });
}

// Delete equipment
function deleteEquipment(equipmentID) {
    if (confirm('Are you sure you want to delete this equipment?')) {
        fetch('{API_BASE}/equipment/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `controller=equipment&action=delete&equipmentID=${equipmentID}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Equipment deleted successfully');
                loadEquipment();
                loadInventoryReport();
            } else {
                showToast('error', 'Error deleting equipment: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Failed to delete equipment');
        });
    }
}

// Edit equipment (show modal with data)
function editEquipment(equipmentID) {
    const equipment = document.querySelector(`tr[data-id="${equipmentID}"]`);
    if (!equipment) return;

    const modal = document.getElementById('updateEquipmentModal');
    if (!modal) return;

    const form = modal.querySelector('form');
    form.querySelector('[name="equipmentID"]').value = equipmentID;
    form.querySelector('[name="name"]').value = equipment.dataset.name;
    form.querySelector('[name="type"]').value = equipment.dataset.type;
    form.querySelector('[name="quantity"]').value = equipment.dataset.quantity;
    form.querySelector('[name="condition"]').value = equipment.dataset.condition;
    form.querySelector('[name="notes"]').value = equipment.dataset.notes || '';

    $(modal).modal('show');
}

// Helper function to get status class
function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'new':
        case 'good':
            return 'good';
        case 'fair':
        case 'used':
            return 'warning';
        case 'poor':
        case 'damaged':
            return 'danger';
        default:
            return 'warning';
    }
}

// Format date helper
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}
