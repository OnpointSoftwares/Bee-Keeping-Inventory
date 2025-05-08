// Production Management
document.addEventListener('DOMContentLoaded', function() {
    // Show production section when nav link is clicked
    document.querySelector('a[href="#production"]').addEventListener('click', function() {
        loadProduction();
        loadProductionReport();
        loadProductionData();
    });

    // Add production form submission
    document.getElementById('addProductionForm')?.addEventListener('submit', handleAddProduction);

    // Update production form submission
    document.getElementById('updateProductionForm')?.addEventListener('submit', handleUpdateProduction);
});

// Load production data
async function loadProduction() {
    const data = await api.get('production', { action: 'getReport' });
    if (data.success) {
        displayProduction(data.data);
        updateHiveFilter(data.data);
    } else {
        showToast('error', 'Error loading production: ' + data.error);
    }
}

// Load production report
async function loadProductionReport() {
    try {
        const data = await api.get('production', { action: 'getReport' });
        if (data.success) {
            displayProductionReport(data.data.report);
            updateProductionChart(data.data.report);
            document.getElementById('totalProduction').textContent = data.data.totalProduction || 0;
        } else {
            showToast('error', 'Error loading production report: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Failed to load production report');
    }
}

// Load and display production report
async function loadProductionReport() {
    try {
        const data = await api.get('production', { action: 'getReport' });
        if (data.success) {
            displayProductionReport(data.data.report);
            updateProductionChart(data.data.report);
            document.getElementById('totalProduction').textContent = data.data.totalProduction || 0;
        } else {
            showToast('error', 'Error loading production report: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Failed to load production report: ' + error.message);
    }
}

// Display production in table
function displayProduction(data) {
    const container = document.getElementById('productionContainer');
    if (!container) return;

    let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Hive</th>
                    <th>Date</th>
                    <th>Quantity (kg)</th>
                    <th>Quality</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(item => {
        html += `
            <tr>
                <td>${item.hiveName}</td>
                <td>${formatDate(item.harvestDate)}</td>
                <td>${item.quantity}</td>
                <td>
                    <span class="status-${getQualityClass(item.quality)}">
                        ${item.quality}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" onclick="editProduction(${item.productionID})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduction(${item.productionID})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    container.innerHTML = html;
}
// Display production report
function displayProductionReport(report) {
    const container = document.getElementById('productionReportContainer');
    if (!container) return;

    let html = `
        <div class="mt-4">
            <h6 class="mb-3">Production by Hive</h6>
            <div class="list-group">
    `;

    report.forEach(item => {
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">${item.hiveName}</h6>
                    <small class="text-muted">Average Quality: ${item.avgQuality}</small>
                </div>
                <span class="badge bg-primary rounded-pill">${item.totalQuantity} kg</span>
            </div>
        `;
    });

    html += '</div></div>';
    container.innerHTML = html;
}

// Update production chart
function updateProductionChart(report) {
    const ctx = document.getElementById('productionChart')?.getContext('2d');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (window.productionChart instanceof Chart) {
        window.productionChart.destroy();
    }

    const labels = report.map(item => item.hiveName);
    const data = report.map(item => item.totalQuantity);

    window.productionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Production (kg)',
                data: data,
                backgroundColor: '#4e73df',
                borderColor: '#2e59d9',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Kilograms'
                    }
                }
            }
        }
    });
}

// Load production data for charts
async function loadProductionData() {
    try {
        const data = await api.get('production', { action: 'getAllProduction' });
        if (data.success) {
            displayProduction(data.data); // Display the production data in the UI
            updateHiveFilter(data.data); // Update the hive filter with the production data
            updateProductionChart(data.data); // Update the production chart with the production data
        } else {
            console.error('Error loading production data:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
// Update charts with production data
function updateCharts(productionData) {
    const ctx = document.getElementById('productionChart').getContext('2d');
    const labels = productionData.map(item => item.type);
    const quantities = productionData.map(item => item.quantity);

    const productionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Production Quantity',
                data: quantities,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Handle add production form submission
function handleAddProduction(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        hiveID: formData.get('hiveID'),
        type: formData.get('type'),
        quantity: parseInt(formData.get('quantity'), 10), // Ensure quantity is an integer
        quality: formData.get('quality'),
        harvestDate: formData.get('harvestDate'),
        notes: formData.get('notes')
    };

    console.log('Data being sent:', data); // Log the data being sent to the server
    fetch('api/production?action=add', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Add Production Response:', data); // Log the response for debugging
        if (data.success) {
            showToast('success', 'Production record added successfully');
            loadProduction();
            loadProductionReport();
            updateHiveFilter(data.report); // Pass the report array to updateHiveFilter
            $('#addProductionModal').modal('hide');
            e.target.reset();
        } else {
            showToast('error', 'Error adding production: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to add production');
    });
}

// Handle update production form submission
function handleUpdateProduction(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        hiveID: formData.get('hiveID'),
        productionType: formData.get('productionType'),
        quantity: formData.get('quantity'),
        unit: formData.get('unit'),
        date: formData.get('date'),
        notes: formData.get('notes')
    };

    fetch('/inventory-management-system/api/production/?action=update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Production record updated successfully');
            loadProduction();
            loadProductionReport();
            $('#updateProductionModal').modal('hide');
        } else {
            showToast('error', 'Error updating production: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to update production');
    });
}

// Delete production
function deleteProduction(productionID) {
    if (confirm('Are you sure you want to delete this production record?')) {
        fetch('/inventory-management-system/api/production', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `controller=production&action=delete&productionID=${productionID}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Production record deleted successfully');
                loadProduction();
                loadProductionReport();
            } else {
                showToast('error', 'Error deleting production: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Failed to delete production');
        });
    }
}

// Edit production (show modal with data)
function editProduction(productionID) {
    const production = document.querySelector(`tr[data-id="${productionID}"]`);
    if (!production) return;

    const modal = document.getElementById('updateProductionModal');
    if (!modal) return;

    const form = modal.querySelector('form');
    form.querySelector('[name="productionID"]').value = productionID;
    form.querySelector('[name="hiveID"]').value = production.dataset.hiveId;
    form.querySelector('[name="quantity"]').value = production.dataset.quantity;
    form.querySelector('[name="quality"]').value = production.dataset.quality;
    form.querySelector('[name="notes"]').value = production.dataset.notes || '';

    $(modal).modal('show');
}

// Helper function to get quality class
function getQualityClass(quality) {
    switch (quality.toLowerCase()) {
        case 'premium':
        case 'excellent':
            return 'good';
        case 'standard':
        case 'average':
            return 'warning';
        case 'low':
        case 'poor':
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

// Update hive filter
function updateHiveFilter(productionData) {
    if (!Array.isArray(productionData)) {
        console.error('Expected productionData to be an array, but got:', productionData);
        return; // Exit if productionData is not an array
    }

    const hiveIDs = productionData.map(item => item.hiveID);
    const uniqueHiveIDs = [...new Set(hiveIDs)];

    const hiveFilter = document.getElementById('hiveFilter');
    hiveFilter.innerHTML = '';

    uniqueHiveIDs.forEach(hiveID => {
        const option = document.createElement('option');
        option.value = hiveID;
        option.textContent = `Hive ${hiveID}`;
        hiveFilter.appendChild(option);
    });
}
