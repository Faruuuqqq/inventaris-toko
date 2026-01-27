// Enhanced Client-Side Validation

class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.errors = {};
        this.rules = {};
        this.messages = {};
        this.init();
    }
    
    init() {
        // Get validation rules from data attributes
        this.extractRules();
        
        // Set up event listeners
        this.form.addEventListener('submit', (e) => {
            if (!this.validate()) {
                e.preventDefault();
                this.showErrors();
            }
        });
        
        // Real-time validation
        this.form.addEventListener('input', (e) => {
            if (e.target.hasAttribute('data-validate')) {
                this.validateField(e.target);
            }
        });
    }
    
    extractRules() {
        const fields = this.form.querySelectorAll('[data-validate]');
        
        fields.forEach(field => {
            const rules = field.dataset.validate.split('|');
            this.rules[field.name] = rules;
            
            // Custom messages from data attributes
            if (field.dataset.messages) {
                this.messages[field.name] = JSON.parse(field.dataset.messages);
            }
        });
    }
    
    validate() {
        this.errors = {};
        let isValid = true;
        
        for (const fieldName in this.rules) {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    validateField(field) {
        const fieldName = field.name;
        const rules = this.rules[fieldName] || [];
        const value = this.getFieldValue(field);
        let isValid = true;
        
        // Clear previous errors
        this.clearFieldError(field);
        
        for (const rule of rules) {
            const [ruleName, ...params] = rule.split(':');
            
            if (!this.validateRule(ruleName, value, params, field)) {
                isValid = false;
                this.addFieldError(field, ruleName);
                break;
            }
        }
        
        return isValid;
    }
    
    validateRule(ruleName, value, params, field) {
        switch (ruleName) {
            case 'required':
                return this.validateRequired(value, field);
            case 'email':
                return this.validateEmail(value);
            case 'min':
                return this.validateMin(value, params[0], field);
            case 'max':
                return this.validateMax(value, params[0], field);
            case 'number':
                return this.validateNumber(value);
            case 'positive':
                return this.validatePositive(value);
            case 'indonesian_phone':
                return this.validateIndonesianPhone(value);
            case 'password':
                return this.validatePassword(value);
            case 'confirm':
                return this.validateConfirm(value, params[0]);
            default:
                return true;
        }
    }
    
    getFieldValue(field) {
        if (field.type === 'checkbox') {
            return field.checked;
        }
        return field.value.trim();
    }
    
    validateRequired(value, field) {
        if (field.type === 'checkbox') {
            return value === true;
        }
        return value !== '';
    }
    
    validateEmail(value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(value);
    }
    
    validateMin(value, minLength, field) {
        if (field.type === 'number') {
            return parseFloat(value) >= parseFloat(minLength);
        }
        return value.length >= parseInt(minLength);
    }
    
    validateMax(value, maxLength, field) {
        if (field.type === 'number') {
            return parseFloat(value) <= parseFloat(maxLength);
        }
        return value.length <= parseInt(maxLength);
    }
    
    validateNumber(value) {
        return !isNaN(value) && value !== '';
    }
    
    validatePositive(value) {
        const num = parseFloat(value);
        return !isNaN(num) && num > 0;
    }
    
    validateIndonesianPhone(value) {
        const cleaned = value.replace(/[^0-9]/g, '');
        return /^0[0-9]{9,12}$/.test(cleaned) || /^62[0-9]{9,12}$/.test(cleaned);
    }
    
    validatePassword(value) {
        // At least 8 characters, one uppercase, one lowercase, one number
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/.test(value);
    }
    
    validateConfirm(value, fieldName) {
        const originalField = this.form.querySelector(`[name="${fieldName}"]`);
        return originalField && value === originalField.value;
    }
    
    addFieldError(field, ruleName) {
        const fieldName = field.name;
        
        if (!this.errors[fieldName]) {
            this.errors[fieldName] = [];
        }
        
        this.errors[fieldName].push(ruleName);
        
        // Add error class
        field.classList.add('is-invalid');
        
        // Create error message element
        let errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentNode.appendChild(errorElement);
        }
        
        // Set error message
        const message = this.getErrorMessage(field, ruleName);
        errorElement.textContent = message;
    }
    
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    getErrorMessage(field, ruleName) {
        const fieldName = field.name;
        const customMessages = this.messages[fieldName] || {};
        
        // Check for custom message
        if (customMessages[ruleName]) {
            return customMessages[ruleName];
        }
        
        // Default messages
        const defaultMessages = {
            required: `${this.getFieldLabel(field)} is required`,
            email: `${this.getFieldLabel(field)} must be a valid email address`,
            min: `${this.getFieldLabel(field)} must be at least ${field.dataset.min || 8} characters`,
            max: `${this.getFieldLabel(field)} must not exceed ${field.dataset.max || 255} characters`,
            number: `${this.getFieldLabel(field)} must be a valid number`,
            positive: `${this.getFieldLabel(field)} must be a positive number`,
            indonesian_phone: `${this.getFieldLabel(field)} must be a valid Indonesian phone number`,
            password: `${this.getFieldLabel(field)} must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number`,
            confirm: `${this.getFieldLabel(field)} does not match`
        };
        
        return defaultMessages[ruleName] || `${this.getFieldLabel(field)} is invalid`;
    }
    
    getFieldLabel(field) {
        const label = this.form.querySelector(`label[for="${field.id}"]`);
        return label ? label.textContent : field.name;
    }
    
    showErrors() {
        // Focus on first field with error
        const firstErrorField = this.form.querySelector('.is-invalid');
        if (firstErrorField) {
            firstErrorField.focus();
        }
        
        // Show error summary
        this.showErrorSummary();
    }
    
    showErrorSummary() {
        let summary = this.form.querySelector('.validation-summary');
        if (!summary) {
            summary = document.createElement('div');
            summary.className = 'alert alert-danger validation-summary';
            this.form.insertBefore(summary, this.form.firstChild);
        }
        
        summary.innerHTML = '<h6>Please fix the following errors:</h6><ul>' + 
            Object.entries(this.errors)
                .map(([field, rules]) => {
                    const fieldElement = this.form.querySelector(`[name="${field}"]`);
                    const label = this.getFieldLabel(fieldElement);
                    return `<li>${label}: ${rules.join(', ')}</li>`;
                })
                .join('') + 
            '</ul>';
    }
}

// Initialize form validation
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('[data-validate-form]');
    forms.forEach(form => {
        new FormValidator(form);
    });
});

// Additional validation helper functions
function validateStockAvailability(productId, warehouseId, quantity) {
    return fetch(`/api/stock/check`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            product_id: productId,
            warehouse_id: warehouseId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => data.available);
}

function validateCustomerCreditLimit(customerId, amount) {
    return fetch(`/api/customer/credit-check`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            customer_id: customerId,
            amount: amount
        })
    })
    .then(response => response.json())
    .then(data => data.available);
}