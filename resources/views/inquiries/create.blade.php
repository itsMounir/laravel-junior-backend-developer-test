<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Inquiry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .spinner-border {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }
        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Submit Inquiry</h3>
                        <button class="btn btn-outline-danger" onclick="logout()" id="logoutButton">Logout</button>
                    </div>
                    <div class="card-body">
                        <form id="inquiryForm" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                                <div class="invalid-feedback">Please enter a valid phone number.</div>
                            </div>

                            <div class="mb-3">
                                <label for="company_id" class="form-label">Company</label>
                                <select class="form-select" id="company_id" name="company_id" required>
                                    <option value="">Loading companies...</option>
                                </select>
                                <div class="invalid-feedback">Please select a company.</div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                <div class="invalid-feedback">Please enter your message.</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="submitButton">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="button-text">Submit Inquiry</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Authentication check and company loading
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('token')) {
                window.location.href = '/login';
            } else {
                loadCompanies();
            }
        });

        async function loadCompanies() {
            const companySelect = document.getElementById('company_id');

            try {
                const response = await fetch('http://127.0.0.1:8000/api/v1/companies', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        handleUnauthorized();
                        return;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                populateCompanies(data.companies);

            } catch (error) {
                console.error('Company loading failed:', error);
                companySelect.innerHTML = `
                    <option value="">Error loading companies</option>
                    <option value="" disabled>Please refresh the page to try again</option>
                `;
            }
        }

        function populateCompanies(companies) {
            const companySelect = document.getElementById('company_id');
            companySelect.innerHTML = '<option value="">Select a company</option>';

            companies.forEach(company => {
                const option = document.createElement('option');
                option.value = company.id;
                option.textContent = company.name;
                companySelect.appendChild(option);
            });
        }

        function handleUnauthorized() {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }

        // Logout function
        async function logout() {
            const logoutButton = document.getElementById('logoutButton');
            logoutButton.disabled = true;

            try {
                const response = await fetch('http://127.0.0.1:8000/api/v1/auth/logout', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                } else {
                    const data = await response.json();
                    alert(data.message || 'Logout failed');
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('Error during logout');
            } finally {
                logoutButton.disabled = false;
            }
        }

        // Form submission handling
        const form = document.getElementById('inquiryForm');
        const submitButton = document.getElementById('submitButton');
        const spinner = submitButton.querySelector('.spinner-border');
        const buttonText = submitButton.querySelector('.button-text');

        function setLoading(isLoading) {
            submitButton.disabled = isLoading;
            spinner.classList.toggle('d-none', !isLoading);
            buttonText.textContent = isLoading ? 'Submitting...' : 'Submit Inquiry';
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            setLoading(true);

            try {
                const response = await fetch('http://127.0.0.1:8000/api/v1/inquiries', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify({
                        name: form.name.value,
                        email: form.email.value,
                        phone: form.phone.value,
                        company_id: form.company_id.value,
                        message: form.message.value
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Inquiry submitted successfully!');
                    form.reset();
                    form.classList.remove('was-validated');
                } else {
                    handleApiError(response, data);
                }
            } catch (error) {
                console.error('Submission error:', error);
                alert('Network error. Please check your connection.');
            } finally {
                setLoading(false);
            }
        });

        function handleApiError(response, data) {
            if (response.status === 422 && data.errors) {
                const errorMessages = Object.values(data.errors)
                    .flat()
                    .join('\n');
                alert(errorMessages);
            } else if (response.status === 401) {
                handleUnauthorized();
            } else {
                alert(data.message || 'An error occurred. Please try again.');
            }
        }
    </script>
</body>
</html>