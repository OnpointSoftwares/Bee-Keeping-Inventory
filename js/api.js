// api.js

const api = {
    async post(endpoint, data) {
        try {
            const response = await fetch(`localhost/inventory-management-system/api/${endpoint}/`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            });
            return await response.json();
        } catch (error) {
            console.error('API POST Error:', error);
            throw new Error('Failed to make POST request');
        }
    },

    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = `localhost/inventory-management-system/api/${endpoint}/?${queryString}`;
        try {
            const response = await fetch(url);
            return await response.json();
        } catch (error) {
            console.error('API GET Error:', error);
            throw new Error('Failed to make GET request');
        }
    }
};

// Attach to the global window object
window.api = api;