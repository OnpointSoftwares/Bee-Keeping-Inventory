// Hive Initialization Script
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the hives page
    if (document.getElementById('hives')) {
        initializeHives();
    }
});

// Initialize hives functionality
function initializeHives() {
    console.log('Initializing hives functionality');
    
    // This function will be called from the hives page
    // It ensures that the HiveManager is properly initialized
    
    // Check if hiveManager is already defined
    if (typeof window.hiveManager === 'undefined') {
        // If not defined, create a new instance
        try {
            // The HiveManager class should be defined in hive.js
            // This will use that class if it exists
            if (typeof HiveManager !== 'undefined') {
                window.hiveManager = new HiveManager();
            } else {
                console.error('HiveManager class not found. Make sure hive.js is loaded correctly.');
            }
        } catch (error) {
            console.error('Error initializing hives:', error);
        }
    }
    
    // Add event listeners for hive-related buttons that might not be handled by HiveManager
    setupHiveEventListeners();
}

// Set up additional event listeners for hive-related functionality
function setupHiveEventListeners() {
    // Add hive button
    const addHiveBtn = document.getElementById('addHiveBtn');
    if (addHiveBtn) {
        addHiveBtn.addEventListener('click', function() {
            const modal = document.getElementById('addHiveModal');
            if (modal) {
                new bootstrap.Modal(modal).show();
            }
        });
    }
    
    // Filter hives by status
    const hiveStatusFilter = document.getElementById('hiveStatusFilter');
    if (hiveStatusFilter) {
        hiveStatusFilter.addEventListener('change', function() {
            filterHivesByStatus(this.value);
        });
    }
}

// Filter hives by status
function filterHivesByStatus(status) {
    const hiveRows = document.querySelectorAll('#hivesContainer tr[data-id]');
    
    if (status === 'all') {
        hiveRows.forEach(row => {
            row.style.display = '';
        });
        return;
    }
    
    hiveRows.forEach(row => {
        const statusBadge = row.querySelector('.badge');
        if (statusBadge) {
            const hiveStatus = statusBadge.textContent.trim().toLowerCase();
            if (status === 'all' || hiveStatus === status.toLowerCase()) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}
