# 🏥 AI-Enhanced Elderly Home Care Service Platform

**ElderCare** is a care management platform that connects **Care Seekers** (those in need of care) with **Care Givers** (those who provide care).

This repository houses the robust **PHP Laravel** backend API port of the legacy Java Spring Boot platform ("ElderSync"). It has been re-architected into a highly structured, production-grade RESTful API employing modern backend design patterns to deliver AI-powered matching, an advanced booking lifecycle, secure token-based authentication, and transaction workflows.

---

## 🚀 System Architecture & Design Patterns

ElderCare is built purely as a stateless **RESTful API Backend** adhering to rigorous Laravel and object-oriented best practices:

- **Service-Oriented Architecture:** Heavy utilization of **Service Contracts** (Interfaces) paired with discrete implementations (`*Imp.php`) to decouple business logic from controllers.
- **Data Transfer Objects (DTOs):** Strict data enforcement across layers using a dedicated `app/Dto/` layer to handle incoming data boundaries safely.
- **API Resources:** Standardized JSON API presentation via Laravel's `app/Http/Resources/` layer for consistent, predictable frontend consumption.
- **Type-Safe Constants:** Deep integration of PHP 8.1+ **Enums** (`app/Enums/`) governing system states, account statuses, and system roles.
- **Data Persistence:** Relational structure engineered over MySQL leveraging Eloquent ORM relationships, factory blueprints, and automated test data seeding.

---

## 🛠️ Core Technologies & Ecosystem

| Component          | Technology                 | Purpose                                                                |
| :----------------- | :------------------------- | :--------------------------------------------------------------------- |
| **Core Framework** | PHP 8.2+ & Laravel         | Modern, service-driven RESTful API execution                           |
| **Database**       | MySQL 8.0+ + Eloquent      | Advanced Active Record data modeling, schema migrations, and factories |
| **Security**       | Laravel Sanctum            | Stateless API token guarding & session-less user authentication        |
| **API Docs**       | L5-Swagger / OpenAPI       | Interactive REST API documentation and sandbox testing                 |
| **AI Matching**    | Groq AI (`llama3-8b-8192`) | Intelligent algorithmic pairing processed via Laravel HTTP Client      |
| **Payments**       | MoMo Payment Gateway       | Secure transaction routing using MoMo's development sandbox mode       |
| **Mail & Alerts**  | Laravel Mail (Gmail SMTP)  | Transactional email workflows and structured template deliveries       |

---

## 🧩 Key Features

### 🤖 Advanced AI Matching Algorithm

- **Algorithmic Prompt Engineering:** Interacts programmatically with Groq's `llama3-8b-8192` model to score and analyze Care Giver compatibility against Care Seeker needs.
- **Granular Scoring Breakdown:** The system utilizes an internal weighting mechanism allocating up to 20 points across four discrete pillars:
    1. Skillsets and Professional Specialties
    2. Patient Medical Conditions & History
    3. Gender Alignment Preferences
    4. Geographic Location & Address Proximity
- **Matching Threshold:** Automatically qualifies pairs achieving a cumulative score of $\ge$ 40 points.

### 📅 Booking Lifecycle & Financial Operations

- **Workflow Integrity:** Complete tracking from booking creation to completion, with validation handled natively via dedicated **Form Requests**.
- **MoMo Sandbox Processing:** Full checkout routing through the MoMo gateway utilizing custom sandbox credentials, incorporating secure Instant Payment Notification (IPN) callbacks.
- **Task Management:** Real-time tracking of operational care checklists inside active bookings, allowing caregivers to fulfill assigned care directives.

### 👥 Multi-Role RBAC (Role-Based Access Control)

- **User Roles:** Governs privileges for four distinct platform archetypes: **USER**, **SEEKER**, **GIVER**, and **ADMIN**.
- **Controller & Service-Level Security:** Access control is strictly guarded using a hybrid defense model:
    1. Global token authorization handled by Laravel Sanctum (`auth:sanctum`).
    2. Context-aware role verification executed directly inside Controller methods.
    3. Algorithmic filtering applied inside Service layers to isolate data access boundaries.

### 🔔 Event-Driven Notification System

- Houses a multi-channel notification engine dispatching tailored operational notifications based on platform milestones, including:
    - `MATCH_FOUND` — Fired when the Groq AI yields a matching profile.
    - `BOOKING_CONFIRMED` — Triggered upon successful MoMo payment verification.

---

## 📂 Project Directory Structure

```microcopy
ElderCare/
├── app/
│   ├── Dto/                # Data Transfer Objects enforcing data layer structure
│   ├── Enums/              # PHP 8.1+ Enums for type-safe statuses and roles
│   ├── Http/
│   │   ├── Controllers/    # Slim API Controllers directing requests to Services
│   │   ├── Requests/       # Custom Form Requests handling robust input validation
│   │   └── Resources/      # API Transformation layers mapping JSON responses
│   ├── Models/             # Rich Eloquent Models detailing relational database schema
│   └── Services/           # Interface-driven business logic layer (Contracts & *Imp)
├── config/                 # Global configuration schemas
├── database/
│   ├── factories/          # Model Factories for automated mock data compilation
│   ├── migrations/         # Version-controlled database schema definitions
│   └── seeders/            # Production and Development mock data seeds
├── routes/
│   └── api.php             # Core stateless RESTful route endpoints
├── tests/
│   ├── Feature/            # End-to-end HTTP API integration tests
│   └── Unit/               # Isolated logical unit tests
└── composer.json           # Backend dependency mappings
```

---

## 🛠️ Getting Started & Installation

### Prerequisites

- **PHP** >= 8.2
- **Composer**
- **MySQL** >= 8.0
- **Node.js & NPM** (for frontend interaction)

### Step-by-Step Setup

1. **Clone the Repository:**

    ```bash
    git clone https://github.com/BaoKhoa1709/ElderCare.git
    cd ElderCare
    ```

2. **Install Backend Dependencies:**

    ```bash
    composer install
    ```

3. **Configure Environment Variables:**
   Copy the example environment configuration file and update your credentials:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Set Up the Database & Configurations:**
   Open your `.env` file and configure your database, AI API, and payment settings:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=eldercare_db
    DB_USERNAME=root
    DB_PASSWORD=your_password

    # Groq AI Settings
    GROQ_API_KEY=your_groq_api_key

    # MoMo Payment Gateway Credentials
    MOMO_PARTNER_CODE=your_partner_code
    MOMO_ACCESS_KEY=your_access_key
    MOMO_SECRET_KEY=your_secret_key

    # Cloudinary Storage
    CLOUDINARY_URL=your_cloudinary_url
    ```

5. **Run Migrations and Seeders:**
   Prepare your database schema and default platform roles/administrative accounts:

    ```bash
    php artisan migrate --seed
    ```

6. **Generate API Documentation:**

    ```bash
    php artisan l5-swagger:generate
    ```

7. **Start the Application Server:**
    ```bash
    php artisan serve
    ```
    The backend server will spin up and run on `http://127.0.0.1:8000`.

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for complete details.
