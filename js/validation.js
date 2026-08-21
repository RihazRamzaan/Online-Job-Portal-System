// js/validation.js (owner: Member D)

document.addEventListener('DOMContentLoaded', function () {
    
    // Helper function to show error messages
    function showError(inputElement, message) {
        clearError(inputElement); // Clear existing error if any
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.85em';
        errorDiv.style.marginTop = '4px';
        errorDiv.innerText = message;
        inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
        inputElement.style.borderColor = 'red';
    }

    // Helper function to clear error messages
    function clearError(inputElement) {
        inputElement.style.borderColor = '';
        const nextElement = inputElement.nextElementSibling;
        if (nextElement && nextElement.classList.contains('error-message')) {
            nextElement.remove();
        }
    }

    // Helper for email format validation
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // --- 1. Admin Login Form Validation ---
    // Expected HTML: <form id="loginForm"> with inputs <input id="email">, <input id="password">
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            let isValid = true;
            
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            // Validate Email
            if (emailInput) {
                if (!emailInput.value.trim()) {
                    showError(emailInput, 'Email is required.');
                    isValid = false;
                } else if (!isValidEmail(emailInput.value.trim())) {
                    showError(emailInput, 'Please enter a valid email address.');
                    isValid = false;
                } else {
                    clearError(emailInput);
                }
            }

            // Validate Password
            if (passwordInput) {
                if (!passwordInput.value.trim()) {
                    showError(passwordInput, 'Password is required.');
                    isValid = false;
                } else {
                    clearError(passwordInput);
                }
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission
            }
        });
    }

    // --- 2. Add / Edit Job Form Validation ---
    // Expected HTML: <form id="jobForm"> with corresponding field IDs
    const jobForm = document.getElementById('jobForm');
    if (jobForm) {
        jobForm.addEventListener('submit', function (e) {
            let isValid = true;

            const fields = [
                { id: 'title', message: 'Job title is required.' },
                { id: 'company_name', message: 'Company name is required.' },
                { id: 'location', message: 'Location is required.' },
                { id: 'job_type', message: 'Please select a job type.' },
                { id: 'category_id', message: 'Please select a category.' }
            ];

            // Required simple fields
            fields.forEach(field => {
                const input = document.getElementById(field.id);
                if (input) {
                    if (!input.value.trim()) {
                        showError(input, field.message);
                        isValid = false;
                    } else {
                        clearError(input);
                    }
                }
            });

            // Salary Validation
            const salaryMin = document.getElementById('salary_min');
            const salaryMax = document.getElementById('salary_max');
            let minVal = -1;
            let maxVal = -1;

            if (salaryMin) {
                if (!salaryMin.value.trim()) {
                    showError(salaryMin, 'Minimum salary is required.');
                    isValid = false;
                } else if (isNaN(salaryMin.value) || Number(salaryMin.value) < 0) {
                    showError(salaryMin, 'Minimum salary must be a valid number >= 0.');
                    isValid = false;
                } else {
                    clearError(salaryMin);
                    minVal = Number(salaryMin.value);
                }
            }

            if (salaryMax) {
                if (!salaryMax.value.trim()) {
                    showError(salaryMax, 'Maximum salary is required.');
                    isValid = false;
                } else if (isNaN(salaryMax.value) || Number(salaryMax.value) < 0) {
                    showError(salaryMax, 'Maximum salary must be a valid number >= 0.');
                    isValid = false;
                } else {
                    clearError(salaryMax);
                    maxVal = Number(salaryMax.value);
                }
            }

            if (salaryMin && salaryMax && minVal !== -1 && maxVal !== -1) {
                if (maxVal < minVal) {
                    showError(salaryMax, 'Maximum salary cannot be less than minimum salary.');
                    isValid = false;
                }
            }

            // Description Validation
            const description = document.getElementById('description');
            if (description) {
                if (!description.value.trim()) {
                    showError(description, 'Job description is required.');
                    isValid = false;
                } else if (description.value.trim().length < 20) {
                    showError(description, 'Description must be at least 20 characters long.');
                    isValid = false;
                } else {
                    clearError(description);
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // --- 3. Application Form Validation ---
    // Expected HTML: <form id="applicationForm"> with inputs for applicant_name, applicant_email, cover_letter
    const applicationForm = document.getElementById('applicationForm');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function (e) {
            let isValid = true;
            
            const nameInput = document.getElementById('applicant_name');
            const emailInput = document.getElementById('applicant_email');
            const coverLetterInput = document.getElementById('cover_letter');

            // Validate Name
            if (nameInput) {
                if (!nameInput.value.trim()) {
                    showError(nameInput, 'Full name is required.');
                    isValid = false;
                } else {
                    clearError(nameInput);
                }
            }

            // Validate Email
            if (emailInput) {
                if (!emailInput.value.trim()) {
                    showError(emailInput, 'Email is required.');
                    isValid = false;
                } else if (!isValidEmail(emailInput.value.trim())) {
                    showError(emailInput, 'Please enter a valid email address.');
                    isValid = false;
                } else {
                    clearError(emailInput);
                }
            }

            // Validate Cover Letter
            if (coverLetterInput) {
                if (!coverLetterInput.value.trim()) {
                    showError(coverLetterInput, 'Cover letter is required.');
                    isValid = false;
                } else if (coverLetterInput.value.trim().length < 50) {
                    showError(coverLetterInput, 'Cover letter must be at least 50 characters long.');
                    isValid = false;
                } else {
                    clearError(coverLetterInput);
                }
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission
            }
        });
    }

    // --- 4. Mock Payment Form Validation ---
    // Expected HTML: <form id="paymentForm"> with inputs for card_name, card_number, expiry_date, cvv
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function (e) {
            let isValid = true;
            
            const cardName = document.getElementById('card_name');
            const cardNumber = document.getElementById('card_number');
            const expiryDate = document.getElementById('expiry_date');
            const cvv = document.getElementById('cvv');

            // Validate Card Name
            if (cardName) {
                if (!cardName.value.trim()) {
                    showError(cardName, 'Name on card is required.');
                    isValid = false;
                } else {
                    clearError(cardName);
                }
            }

            // Validate Card Number (Format only: 16 digits)
            if (cardNumber) {
                const numVal = cardNumber.value.replace(/\s+/g, '');
                if (!numVal) {
                    showError(cardNumber, 'Card number is required.');
                    isValid = false;
                } else if (!/^\d{16}$/.test(numVal)) {
                    showError(cardNumber, 'Card number must be exactly 16 digits (mock validation).');
                    isValid = false;
                } else {
                    clearError(cardNumber);
                }
            }

            // Validate Expiry Date (Format MM/YY)
            if (expiryDate) {
                if (!expiryDate.value.trim()) {
                    showError(expiryDate, 'Expiry date is required.');
                    isValid = false;
                } else if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryDate.value.trim())) {
                    showError(expiryDate, 'Expiry date must be in MM/YY format.');
                    isValid = false;
                } else {
                    clearError(expiryDate);
                }
            }

            // Validate CVV (3 or 4 digits)
            if (cvv) {
                if (!cvv.value.trim()) {
                    showError(cvv, 'CVV is required.');
                    isValid = false;
                } else if (!/^\d{3,4}$/.test(cvv.value.trim())) {
                    showError(cvv, 'CVV must be 3 or 4 digits.');
                    isValid = false;
                } else {
                    clearError(cvv);
                }
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission
            }
        });
    }

});