// Health Management
document.addEventListener('DOMContentLoaded', function() {
    // Show health section when nav link is clicked
    document.querySelector('a[href="#health"]')?.addEventListener('click', function() {
        loadHealth();
    });

    // Add health record form submission
    document.getElementById('healthCheckForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = {
            action: 'addHealthCheck',
            hiveID: formData.get('hiveID'),
            checkDate: formData.get('checkDate') || new Date().toISOString().split('T')[0],
            queenPresent: formData.get('queenPresent') === 'yes' ? 1 : 0,
            colonyStrength: formData.get('colonyStrength') || 'Moderate',
            diseaseSymptoms: formData.get('diseaseSymptoms') || '',
            pestProblems: formData.get('pestProblems') || '',
            notes: formData.get('notes') || ''
        };

        console.log('Submitting health check:', data);

        try {
            const result = await api.post('hive', data);
            console.log('Health check response:', result);
            
            if (result.success) {
                showToast('Health check added successfully', 'success');
                e.target.reset();
                loadHealth(); // Reload the health records
            } else {
                showToast('Error: ' + (result.error || 'Failed to add health check'), 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error: ' + error.message, 'error');
        }
    });

    // Initial load of health data
    loadHealth();
});

// Load health data
async function loadHealth() {
    console.log('Loading health data...');
    try {
        const result = await api.get('hive', { action: 'getAll' });
        console.log('Health data response:', result);
        
        if (result.success && Array.isArray(result.data)) {
            console.log('Found', result.data.length, 'hives');
            displayHealth(result.data);
        } else {
            console.error('Invalid response:', result);
            showToast('Failed to load health records: ' + (result.error || 'Invalid response format'), 'error');
        }
    } catch (error) {
        console.error('Error loading health data:', error);
        showToast('Error: ' + error.message, 'error');
    }
}

// Display health records
function displayHealth(hives) {
    console.log('Displaying health data for hives:', hives);
    const container = document.getElementById('healthContainer');
    if (!container) {
        console.error('Health container not found');
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Hive Number</th>
                        <th>Last Check Date</th>
                        <th>Queen Present</th>
                        <th>Colony Strength</th>
                        <th>Disease Symptoms</th>
                        <th>Pest Problems</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (hives.length === 0) {
        html += `
            <tr>
                <td colspan="6" class="text-center">No hives found</td>
            </tr>
        `;
    } else {
        hives.forEach(hive => {
            console.log('Processing hive:', hive);
            const health = hive.lastHealth || {};
            console.log('Hive health data:', health);
            
            html += `
                <tr>
                    <td>Hive #${hive.hiveNumber || 'N/A'}</td>
                    <td>${health.checkDate ? formatDate(health.checkDate) : 'No check'}</td>
                    <td>
                        <span class="badge ${health.queenPresent == 1 ? 'bg-success' : 'bg-danger'}">
                            ${health.queenPresent == 1 ? 'Yes' : 'No'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${getHealthStatusClass(health.colonyStrength)}">
                            ${health.colonyStrength || 'N/A'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${health.diseaseSymptoms ? 'bg-warning' : 'bg-success'}">
                            ${health.diseaseSymptoms || 'None'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${health.pestProblems ? 'bg-warning' : 'bg-success'}">
                            ${health.pestProblems || 'None'}
                        </span>
                    </td>
                </tr>
            `;
        });
    }

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Helper function to get appropriate class for health status
function getHealthStatusClass(status) {
    if (!status) return 'bg-secondary';
    
    status = status.toLowerCase();
    switch(status) {
        case 'strong':
            return 'bg-success';
        case 'moderate':
            return 'bg-warning';
        case 'weak':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// View health history for a specific hive
async function viewHealthHistory(hiveID) {
    console.log('Viewing health history for hive:', hiveID);
    try {
        const result = await api.get('hive', { 
            action: 'getHealthHistory',
            hiveID: hiveID
        });
        
        console.log('Health history response:', result);
        
        if (result.success && Array.isArray(result.data)) {
            displayHealthHistory(result.data);
        } else {
            console.error('Invalid history response:', result);
            showToast('Failed to load health history: ' + (result.error || 'Invalid response format'), 'error');
        }
    } catch (error) {
        console.error('Error loading health history:', error);
        showToast('Error: ' + error.message, 'error');
    }
}

// Display health history in a modal
function displayHealthHistory(history) {
    console.log('Displaying health history:', history);
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'healthHistoryModal';
    
    let html = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Health Check History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Queen Present</th>
                                    <th>Colony Strength</th>
                                    <th>Disease Symptoms</th>
                                    <th>Pest Problems</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
    `;
    
    if (history.length === 0) {
        html += `
            <tr>
                <td colspan="6" class="text-center">No health records found</td>
            </tr>
        `;
    } else {
        history.forEach(record => {
            html += `
                <tr>
                    <td>${formatDate(record.checkDate)}</td>
                    <td>
                        <span class="badge ${record.queenPresent == 1 ? 'bg-success' : 'bg-danger'}">
                            ${record.queenPresent == 1 ? 'Yes' : 'No'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${getHealthStatusClass(record.colonyStrength)}">
                            ${record.colonyStrength || 'N/A'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${record.diseaseSymptoms ? 'bg-warning' : 'bg-success'}">
                            ${record.diseaseSymptoms || 'None'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${record.pestProblems ? 'bg-warning' : 'bg-success'}">
                            ${record.pestProblems || 'None'}
                        </span>
                    </td>
                    <td>${record.notes || ''}</td>
                </tr>
            `;
        });
    }
    
    html += `
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    modal.innerHTML = html;
    document.body.appendChild(modal);
    
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
    
    modal.addEventListener('hidden.bs.modal', function () {
        document.body.removeChild(modal);
    });
}

// Format date helper
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString();
}
