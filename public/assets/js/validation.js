// Enhanced Form Validation with Visual Feedback

/**
 * Validate a single input field
 * @param {HTMLInputElement} input
 * @returns {boolean}
 */
function validateInput(input) {
    // Remove previous states
    input.classList.remove('is-valid', 'is-invalid', 'is-warning');

    // Skip if not required and empty
    if (!input.hasAttribute('required') && input.value.trim() === '') {
        return true;
    }

    // Check if required and empty
    if (input.hasAttribute('required') && input.value.trim() === '') {
        input.classList.add('is-invalid');
        return false;
    }

    // Email validation
    if (input.type === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(input.value)) {
            input.classList.add('is-invalid');
            return false;
        }
    }

    // Number validation
    if (input.type === 'number') {
        const value = parseFloat(input.value);
        const min = input.hasAttribute('min') ? parseFloat(input.getAttribute('min')) : null;
        const max = input.hasAttribute('max') ? parseFloat(input.getAttribute('max')) : null;

        if (isNaN(value)) {
            input.classList.add('is-invalid');
            return false;
        }

        if (min !== null && value < min) {
            input.classList.add('is-invalid');
            return false;
        }

        if (max !== null && value > max) {
            input.classList.add('is-invalid');
            return false;
        }
    }

    // Min length validation
    if (input.hasAttribute('minlength')) {
        const minLength = parseInt(input.getAttribute('minlength'));
        if (input.value.length < minLength) {
            input.classList.add('is-invalid');
            return false;
        }
    }

    // Max length validation
    if (input.hasAttribute('maxlength')) {
        const maxLength = parseInt(input.getAttribute('maxlength'));
        if (input.value.length > maxLength) {
            input.classList.add('is-warning');
            return true; // Warning, not error
        }
    }

    // Pattern validation
    if (input.hasAttribute('pattern')) {
        const pattern = new RegExp(input.getAttribute('pattern'));
        if (!pattern.test(input.value)) {
            input.classList.add('is-invalid');
            return false;
        }
    }

    // If all validations pass
    input.classList.add('is-valid');
    return true;
}

/**
 * Validate entire form
 * @param {HTMLFormElement} form
 * @returns {boolean}
 */
function validateForm(form) {
    const inputs = form.querySelectorAll('.form-input[required], .form-input[type="email"], .form-input[pattern]');
    let isValid = true;

    inputs.forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });

    return isValid;
}

/**
 * Show validation error message
 * @param {HTMLInputElement} input
 * @param {string} message
 */
function showValidationError(input, message) {
    input.classList.add('is-invalid');

    // Find or create feedback element
    let feedback = input.nextElementSibling;
    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentNode.insertBefore(feedback, input.nextSibling);
    }

    feedback.textContent = message;
}

/**
 * Clear validation state
 * @param {HTMLInputElement} input
 */
function clearValidation(input) {
    input.classList.remove('is-valid', 'is-invalid', 'is-warning');
}

/**
 * Initialize form validation
 */
document.addEventListener('DOMContentLoaded', function () {
    // Find all forms with validation
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        const inputs = form.querySelectorAll('.form-input');

        // Real-time validation on blur
        inputs.forEach(input => {
            input.addEventListener('blur', function () {
                if (this.value.trim() !== '' || this.hasAttribute('required')) {
                    validateInput(this);
                }
            });

            // Clear error on input
            input.addEventListener('input', function () {
                if (this.classList.contains('is-invalid')) {
                    if (this.value.trim() !== '') {
                        validateInput(this);
                    }
                }
            });
        });

        // Form submit validation
        form.addEventListener('submit', function (e) {
            if (!validateForm(this)) {
                e.preventDefault();

                // Show toast notification
                if (typeof Toast !== 'undefined') {
                    Toast.error('Mohon lengkapi semua field yang wajib diisi', 'Validasi Gagal');
                }

                // Focus first invalid input
                const firstInvalid = this.querySelector('.form-input.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    });

    // Password confirmation validation
    const passwordConfirms = document.querySelectorAll('input[data-password-confirm]');
    passwordConfirms.forEach(confirmInput => {
        const passwordInput = document.querySelector(confirmInput.dataset.passwordConfirm);

        if (passwordInput) {
            confirmInput.addEventListener('input', function () {
                if (this.value !== passwordInput.value) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    showValidationError(this, 'Password tidak cocok');
                } else if (this.value !== '') {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        }
    });

    // Number input validation (prevent non-numeric)
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('keypress', function (e) {
            // Allow: backspace, delete, tab, escape, enter, decimal point
            if ([46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });

    // Auto-format currency inputs
    const currencyInputs = document.querySelectorAll('input[data-currency]');
    currencyInputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (this.value) {
                const value = parseFloat(this.value.replace(/[^0-9.-]+/g, ''));
                if (!isNaN(value)) {
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                }
            }
        });

        input.addEventListener('focus', function () {
            this.value = this.value.replace(/[^0-9.-]+/g, '');
        });
    });
});

// Export functions for use in other scripts
window.FormValidation = {
    validateInput,
    validateForm,
    showValidationError,
    clearValidation
};
