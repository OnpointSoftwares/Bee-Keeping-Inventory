/**
 * API Utilities
 * 
 * This file provides utility functions for interacting with the backend API
 * using the Fetch API for asynchronous data operations.
 */

const ApiUtils = {
    /**
     * Fetch data from the API
     * 
     * @param {string} endpoint - API endpoint (e.g., 'hive', 'health', 'production')
     * @param {string} action - Action to perform (e.g., 'getAll', 'get', 'add', 'update', 'delete')
     * @param {Object} params - Additional query parameters (optional)
     * @returns {Promise} - Promise that resolves with the fetched data
     */
    get: async function(endpoint, action, params = {}) {
        try {
            // Build query parameters
            const queryParams = { action, ...params };
            const queryString = new URLSearchParams(queryParams).toString();
            
            // Make the fetch request
            const response = await fetch(`api/${endpoint}/?${queryString}`);
            
            // Check if the response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            // Parse and return the JSON data
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'API request failed');
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },
    
    /**
     * Post data to the API
     * 
     * @param {string} endpoint - API endpoint (e.g., 'hive', 'health', 'production')
     * @param {string} action - Action to perform (e.g., 'add', 'update')
     * @param {Object} data - Data to post
     * @returns {Promise} - Promise that resolves with the response data
     */
    post: async function(endpoint, action, data) {
        try {
            // Make the fetch request
            const response = await fetch(`api/${endpoint}/?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            // Check if the response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            // Parse and return the JSON data
            const responseData = await response.json();
            
            if (!responseData.success) {
                throw new Error(responseData.message || 'API request failed');
            }
            
            return responseData;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },
    
    /**
     * Delete data via the API
     * 
     * @param {string} endpoint - API endpoint (e.g., 'hive', 'health', 'production')
     * @param {string} id - ID of the record to delete
     * @returns {Promise} - Promise that resolves with the response data
     */
    delete: async function(endpoint, id) {
        try {
            // Make the fetch request
            const response = await fetch(`api/${endpoint}/?action=delete&id=${id}`, {
                method: 'DELETE'
            });
            
            // Check if the response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            // Parse and return the JSON data
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'API request failed');
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },
    
    /**
     * Format date to YYYY-MM-DD
     * 
     * @param {Date} date - Date object
     * @returns {string} - Formatted date string
     */
    formatDate: function(date) {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    },
    
    /**
     * Show a notification to the user
     * 
     * @param {string} message - Message to display
     * @param {string} type - Type of notification (success, error, warning, info)
     */
    showNotification: function(message, type = 'info') {
        // Create notification element if it doesn't exist
        let notificationContainer = document.getElementById('notification-container');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            notificationContainer.style.position = 'fixed';
            notificationContainer.style.top = '20px';
            notificationContainer.style.right = '20px';
            notificationContainer.style.zIndex = '9999';
            document.body.appendChild(notificationContainer);
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.role = 'alert';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Add notification to container
        notificationContainer.appendChild(notification);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 150);
        }, 5000);
    }
};
