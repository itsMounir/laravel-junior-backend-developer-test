<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style for the error text inside input */
        .error-text {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #dc3545;
            font-size: 0.875rem;
            pointer-events: none;
        }

        /* Add padding to inputs to prevent text overlap with error message */
        .form-control {
            padding-right: 150px;
        }

        /* Container for input and error message */
        .input-container {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <form id="loginForm" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-container">
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <span class="error-text" id="email-error"></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-container">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <span class="error-text" id="password-error"></span>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>

                            <div class="text-center mt-3">
                                Don't have an account? <a href="/register">Register here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');

        function clearErrors() {
            // Clear all error messages
            document.querySelectorAll('.error-text').forEach(error => {
                error.textContent = '';
            });
            document.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('is-invalid');
            });
        }

        function showError(fieldName, message) {
            const input = document.getElementById(fieldName);
            const errorSpan = document.getElementById(`${fieldName}-error`);

            input.classList.add('is-invalid');
            errorSpan.textContent = message;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors();

            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            try {
                const response = await fetch('http://127.0.0.1:8000/api/v1/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: form.email.value,
                        password: form.password.value
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Store the token in localStorage
                    localStorage.setItem('token', data.access_token);
                    // Redirect to inquiries page
                    window.location.href = '/inquiries/create';
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, messages]) => {
                            showError(field, messages[0]);
                        });
                    } else {
                        alert(data.message || 'Login failed');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error during login. Please try again.');
            }
        });
    </script>
</body>
</html>
