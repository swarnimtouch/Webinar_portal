class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (!this.form) {
            console.error(`Form with ID "${formId}" not found`);
            return;
        }

        this.fields = this.form.querySelectorAll('input[data-field], select[data-field], textarea[data-field]');
        this.init();
    }

    init() {
        // Prevent form submission and validate
        this.form.addEventListener('submit', (e) => {
            e.preventDefault(); // Stop form from submitting

            if (this.validateAll()) {
                // If all validations pass, submit the form
                this.form.submit();
            }
        });
    }

    validateAll() {
        let isValid = true;

        this.fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        // Scroll to first error
        if (!isValid) {
            const firstError = this.form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }

        return isValid;
    }

    validateField(field) {
        const fieldName = field.getAttribute('data-field');
        const label = field.getAttribute('data-label') || fieldName;
        const isRequired = field.getAttribute('data-is-required') === '1';
        const value = field.value.trim();
        const errorContainer = field.parentElement.querySelector('.js-error');

        // Clear previous errors
        this.clearError(field, errorContainer);

        // Required validation
        if (isRequired && !value) {
            this.showError(field, errorContainer, `${label} is required`);
            return false;
        }

        // Skip other validations if field is empty and not required
        if (!value && !isRequired) {
            return true;
        }

        // Email validation
        if (field.type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showError(field, errorContainer, 'Please enter a valid email address');
                return false;
            }
        }

        // Mobile number validation (Indian format)
        if (fieldName === 'mobile' || fieldName === 'mobile_number' || fieldName === 'phone') {
            const mobileRegex = /^[6-9]\d{9}$/;
            if (!mobileRegex.test(value)) {
                this.showError(field, errorContainer, 'Please enter a valid 10-digit mobile number');
                return false;
            }
        }

        // Password validation
        if (field.type === 'password' && value.length < 6) {
            this.showError(field, errorContainer, 'Password must be at least 6 characters');
            return false;
        }

        // Number validation
        if (field.type === 'number') {
            if (isNaN(value) || value === '') {
                this.showError(field, errorContainer, `${label} must be a valid number`);
                return false;
            }
        }

        return true;
    }

    showError(field, errorContainer, message) {
        field.classList.add('is-invalid');
        if (errorContainer) {
            errorContainer.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> ${message}`;
            errorContainer.style.display = 'flex';
        }
    }

    clearError(field, errorContainer) {
        field.classList.remove('is-invalid');
        if (errorContainer) {
            errorContainer.innerHTML = '';
            errorContainer.style.display = 'none';
        }
    }

    // Enable real-time validation on blur
    enableRealTimeValidation() {
        this.fields.forEach(field => {
            field.addEventListener('blur', () => {
                this.validateField(field);
            });

            // Clear error on input
            field.addEventListener('input', () => {
                const errorContainer = field.parentElement.querySelector('.js-error');
                this.clearError(field, errorContainer);
            });
        });
    }
}

// Helper function to enable real-time validation
function enableRealTimeValidation(validator) {
    if (validator && typeof validator.enableRealTimeValidation === 'function') {
        validator.enableRealTimeValidation();
    }
}
