<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Register</h3>
                    </div>
                    <div class="card-body">
                        <form id="registerForm" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>

                            <div class="text-center mt-3">
                                Already have an account? <a href="/login">Login here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('registerForm');

        function clearErrors() {
            // Remove error states from all inputs
            form.querySelectorAll('.is-invalid').forEach(input => {
                input.classList.remove('is-invalid');
                input.nextElementSibling.textContent = '';
            });
        }

        function showError(fieldName, message) {
            const input = document.getElementById(fieldName);
            const feedback = input.nextElementSibling;

            input.classList.add('is-invalid');
            feedback.textContent = message;
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
                const response = await fetch('http://127.0.0.1:8000/api/v1/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        first_name: form.first_name.value,
                        last_name: form.last_name.value,
                        email: form.email.value,
                        password: form.password.value,
                        password_confirmation: form.password_confirmation.value
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
                        alert(data.message || 'Registration failed');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error during registration. Please try again.');
            }
        });
    </script>
</body>
</html>
