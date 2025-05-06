class HiveManager {
    constructor() {
        this.initializeEventListeners();
        this.loadHives();
    }

    initializeEventListeners() {
        // Add hive form
        document.getElementById('addHiveForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.addHive();
        });

        // Add health check form
        document.getElementById('addHealthCheckForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.addHealthCheck();
        });

        // Hive selection for health check
        document.getElementById('healthCheckHiveSelect')?.addEventListener('change', (e) => {
            this.loadHiveHealth(e.target.value);
        });
    }

    async addHive() {
        try {
            const data = getFormData('addHiveForm');
            data.action = 'add';
            
            const result = await api.post('hive', data);
            if (result.success) {
                showToast('Hive added successfully');
                this.loadHives();
                document.getElementById('addHiveForm').reset();
            } else {
                showToast('Failed to add hive', 'error');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    async loadHives() {
        try {
            const data = await api.get('hive', { action: 'getAll' });
            console.log('API Response Data:', data); // Log the API response
            if (data.success) {
                this.displayHives(data.data); // Pass the actual array of hives
                this.updateHiveSelects(data.data);
            } else {
                showToast('Failed to load hives', 'error');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    displayHives(hives) {
        const container = document.getElementById('hivesContainer');
        console.log('Container:', container); // Log the container element
    
        if (!container) return;
    
        // Check if there are hives to display
        console.log('Hives data:', hives); // Log the hives data
        if (!hives || hives.length === 0) {
            container.innerHTML = '<p>No hives available.</p>';
            return;
        }
    
        const columns = [
            { key: 'hiveNumber', label: 'Hive Number' },
            { key: 'location', label: 'Location' },
            { key: 'dateEstablished', label: 'Established', format: formatDate },
            { key: 'queenAge', label: 'Queen Age (months)' },
            { 
                key: 'lastHealth', 
                label: 'Last Health Check',
                format: (health) => health ? `
                    <div>Date: ${formatDate(health.checkDate)}</div>
                    <div>Strength: ${health.colonyStrength}</div>
                    ${health.diseaseSymptoms ? `<div class="text-danger">Issues: ${health.diseaseSymptoms}</div>` : ''}
                ` : 'No health check'
            },
            {
                key: 'hiveID',
                label: 'Actions',
                format: (id) => `
                    <button onclick="hiveManager.viewHive(${id})" class="btn btn-sm btn-info">View</button>
                    <button onclick="hiveManager.deleteHive(${id})" class="btn btn-sm btn-danger">Delete</button>
                `
            }
        ];
    
        container.innerHTML = ''; // Clear existing content
        const table = generateTable(hives, columns);
        console.log('Generated Table:', table); // Log the generated table
        container.appendChild(table); // Append the table to the container
    }

    updateHiveSelects(hives) {
        const selects = document.querySelectorAll('.hive-select');
        selects.forEach(select => {
            select.innerHTML = '<option value="">Select Hive</option>' +
                hives.map(hive => `
                    <option value="${hive.hiveID}">
                        Hive #${hive.hiveNumber} - ${hive.location}
                    </option>
                `).join('');
        });
    }

    async addHealthCheck() {
        try {
            const data = getFormData('addHealthCheckForm');
            data.action = 'addHealth';
            
            const result = await api.post('hive', data);
            if (result.success) {
                showToast('Health check added successfully');
                this.loadHiveHealth(data.hiveID);
                document.getElementById('addHealthCheckForm').reset();
            } else {
                showToast('Failed to add health check', 'error');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    async loadHiveHealth(hiveID) {
        if (!hiveID) return;
        
        try {
            const data = await api.get('hive', { 
                action: 'getHealth',
                hiveID: hiveID
            });
            this.displayHealthHistory(data);
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    displayHealthHistory(history) {
        const container = document.getElementById('healthHistoryContainer');
        if (!container) return;

        const columns = [
            { key: 'checkDate', label: 'Date', format: formatDate },
            { key: 'queenPresent', label: 'Queen Present', format: v => v ? 'Yes' : 'No' },
            { key: 'colonyStrength', label: 'Colony Strength' },
            { key: 'diseaseSymptoms', label: 'Disease Symptoms' },
            { key: 'pestProblems', label: 'Pest Problems' },
            { key: 'notes', label: 'Notes' }
        ];

        container.innerHTML = '';
        container.appendChild(generateTable(history, columns));
    }

    async deleteHive(hiveID) {
        if (!confirm('Are you sure you want to delete this hive?')) return;
        
        try {
            const result = await api.post('hive', {
                action: 'delete',
                hiveID: hiveID
            });
            
            if (result.success) {
                showToast('Hive deleted successfully');
                this.loadHives();
            } else {
                showToast('Failed to delete hive', 'error');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    async viewHive(hiveID) {
        try {
            const data = await api.get('hive', {
                action: 'getOne',
                hiveID: hiveID
            });
            
            // Update view hive modal with data
            const modal = document.getElementById('viewHiveModal');
            if (modal) {
                modal.querySelector('.modal-title').textContent = `Hive #${data.hiveNumber}`;
                modal.querySelector('.modal-body').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Hive Details</h5>
                            <p><strong>Location:</strong> ${data.location}</p>
                            <p><strong>Established:</strong> ${formatDate(data.dateEstablished)}</p>
                            <p><strong>Queen Age:</strong> ${data.queenAge} months</p>
                            <p><strong>Notes:</strong> ${data.notes || 'No notes'}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Health History</h5>
                            ${this.generateHealthTimeline(data.healthHistory)}
                        </div>
                    </div>
                `;
                
                new bootstrap.Modal(modal).show();
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    generateHealthTimeline(history) {
        if (!history || !history.length) return 'No health records';
        
        return `
            <div class="timeline">
                ${history.map(check => `
                    <div class="timeline-item">
                        <div class="timeline-date">${formatDate(check.checkDate)}</div>
                        <div class="timeline-content">
                            <div>Strength: ${check.colonyStrength}</div>
                            <div>Queen: ${check.queenPresent ? 'Present' : 'Not seen'}</div>
                            ${check.diseaseSymptoms ? `<div class="text-danger">Disease: ${check.diseaseSymptoms}</div>` : ''}
                            ${check.pestProblems ? `<div class="text-warning">Pests: ${check.pestProblems}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
}

// Initialize hive manager when document is ready
document.addEventListener('DOMContentLoaded', () => {
    window.hiveManager = new HiveManager();
});
