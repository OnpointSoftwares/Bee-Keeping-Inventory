// Settings Management
document.addEventListener('DOMContentLoaded', function() {
    // Show settings section when nav link is clicked
    document.querySelector('a[href="#settings"]').addEventListener('click', function() {
        loadSettings();
    });

    // Save settings form submission
    document.getElementById('settingsForm')?.addEventListener('submit', handleSaveSettings);
});

// Load settings
function loadSettings() {
    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'controller=settings&action=get'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySettings(data.settings);
        } else {
            showToast('error', 'Error loading settings: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to load settings');
    });
}

// Display settings
function displaySettings(settings) {
    // Update profile settings
    document.getElementById('userName')?.setAttribute('value', settings.userName || '');
    document.getElementById('email')?.setAttribute('value', settings.email || '');
    
    // Update notification settings
    document.getElementById('emailNotifications')?.checked = settings.emailNotifications;
    document.getElementById('healthAlerts')?.checked = settings.healthAlerts;
    document.getElementById('productionAlerts')?.checked = settings.productionAlerts;
    
    // Update system settings
    document.getElementById('language')?.value = settings.language || 'en';
    document.getElementById('dateFormat')?.value = settings.dateFormat || 'MM/DD/YYYY';
    document.getElementById('weightUnit')?.value = settings.weightUnit || 'kg';
}

// Handle save settings
function handleSaveSettings(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        controller: 'settings',
        action: 'save',
        userName: formData.get('userName'),
        email: formData.get('email'),
        emailNotifications: formData.get('emailNotifications') === 'on',
        healthAlerts: formData.get('healthAlerts') === 'on',
        productionAlerts: formData.get('productionAlerts') === 'on',
        language: formData.get('language'),
        dateFormat: formData.get('dateFormat'),
        weightUnit: formData.get('weightUnit')
    };

    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Settings saved successfully');
        } else {
            showToast('error', 'Error saving settings: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to save settings');
    });
}

// Handle password change
function handleChangePassword(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        controller: 'settings',
        action: 'changePassword',
        currentPassword: formData.get('currentPassword'),
        newPassword: formData.get('newPassword'),
        confirmPassword: formData.get('confirmPassword')
    };

    if (data.newPassword !== data.confirmPassword) {
        showToast('error', 'New passwords do not match');
        return;
    }

    fetch('api/handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Password changed successfully');
            e.target.reset();
        } else {
            showToast('error', 'Error changing password: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to change password');
    });
}
