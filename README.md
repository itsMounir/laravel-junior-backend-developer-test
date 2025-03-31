**Laravel 10 API Project**

📋 *Project Overview*
A feature-rich API backend with following capabilities:
- ✅ Sanctum token authentication
- 📞 Phone number validation via AbstractAPI
- 🏢 Company CRUD operations with relationships
- 📬 Inquiry management system
- 📚 Comprehensive API documentation

⚙️ *Requirements*
- PHP 8.2.4+
- MySQL 8.0+
- Composer 2.7.2+

🚀 *Installation & Setup*

1. Clone Repository
```bash
git clone https://github.com/itsMounir/laravel-junior-backend-developer-test.git
cd project-directory
```

2. Install Dependencies
```bash
composer install
```

3. Configure Environment
```bash
cp .env.example .env
```
Edit `.env` with your configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE="your_db_name"
DB_USERNAME="your_db_user"
DB_PASSWORD="your_db_password"

ABSTRACT_PHONE_VALIDATION_API_KEY="your_api_key"
```

4. Database Setup
```bash
php artisan migrate
php artisan db:seed  # Optional: Seed sample data
```

5. Start Development Server
```bash
php artisan serve
```

🔧 *Configuration*
`Required API Keys`
1. Obtain AbstractAPI key from [abstractapi.com](https://www.abstractapi.com/phone-validation-api)
2. Add to `.env`:
```env
ABSTRACT_PHONE_VALIDATION_API_KEY="your_api_key_here"
```

📡 *API Documentation*

`Postman Setup`
1. Import Postman collection from `https://documenter.getpostman.com/view/31040346/2sB2cRC47Q`
2. Configure environment variables:
   - `base_url`: Your API endpoint (e.g., `http://localhost:8000/api/v1/`)


`Authentication Flow`
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@gmail.com",
    "password": "your_password"
}

Response:
{
    "message": "User logged in successfully.",
    "user": {
        "id": 11,
        "first_name": "first",
        "last_name": "last",
        "email": "user@gmail.com",
        "created_from": "1 minutes ago"
    },
    "access_token": "your_access_token"
}
```

```http
GET /api/v1/auth/logout
Authorization: Bearer your_access_token

Response:
{
    "message": "User logged out successfully."
}
```

`Company Endpoints`
**Create Company**
```http
POST /api/v1/companies
Authorization: Bearer your_access_token
Content-Type: application/json

{
    "name": "Tech Corp",
    "email": "info@techcorp.com",
    "phone": "+1234567890",
    "country_id": 1,
    "industry_id": 1
}

Response (201 Created):
{
    "message": "Company created successfully."
}
```

**List Companies (with filters)**
*Example 1: Filter by name*
GET /api/companies?name=Tech
*Example 2: Filter by owner*
GET /api/companies?ownerId=5
*Example 3: Combine filters*
GET /api/companies?name=Corp&ownerId=5

**Filter Parameters**
| Parameter | Type   | Description                     | Example        |
|-----------|--------|---------------------------------|----------------|
| name      | string | Partial match for company name  | `?name=Tech`   |
| ownerId   | int    | Exact match for owner ID        | `?ownerId=5`   |

```http
GET /api/v1/companies
Authorization: Bearer your_access_token

Response (200 OK):
{
    "companies": [
        {
            "id": 1,
            "owner_id": 11,
            "name": "Tech Corp",
            "email": "info@techcorp.com",
            "phone": "+1234567890",
            "country_id": 1,
            "industry_id": 1
            "country_name": "est",
            "industry_name": "rerum",
            "created_from": "8 hours ago"
        }
    ]
}
```

🗃️ *Database Schema*

`Core Tables`
| Table       | Description                          | Relationships                    |
|-------------|--------------------------------------|----------------------------------|
| users       | System users with auth credentials   | Has many companies               |
| countries   | Supported countries list             | Has many companies               |
| industries  | Business industry categories         | Has many companies               |
| companies   | Registered business entities         | Belongs to user/country/industry |
| inquiries   | Customer contact requests            | Belongs to company               |

`Schema Diagram`
```sql
users
└──┬ id
   └── companies.owner_id (1:n)

countries
└──┬ id
   └── companies.country_id (1:n)

industries
└──┬ id
   └── companies.industry_id (1:n)

companies
└──┬ id
   └── inquiries.company_id (1:n)
```

🧪 *Testing*
Run test suite with:
```bash
php artisan test
```

**Sample Test Case:**
```php
    /**
     * Test successful user registration
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $userData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name']
        ]);
    }
```

🌐 *Third-Party Services*
| Service          | Requirement | Documentation                          |
|------------------|-------------|----------------------------------------|
| AbstractAPI      | Required    | [Phone Validation API Docs](https://www.abstractapi.com/phone-validation-api) |

📜 *License*
[MIT License](LICENSE.md) - See repository license file

⁉ *Support*
Contact [your.email@domain.com] for technical inquiries or issues

---

**Security Note:** Always store sensitive information in `.env` and never commit it to version control.
```