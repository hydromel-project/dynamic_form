# QA Server: Dynamic Form Management System

This project implements a dynamic, self-hosted form management system designed to allow administrators to create custom forms with highly flexible question types, branching logic, and file/image uploads. It provides a robust backend API and a modern React-based frontend for form filling and response management.

## Key Features

-   **Dynamic Form Creation**: Administrators can define custom forms with various question types.
-   **Flexible Question Types**: Supports text, number, boolean (yes/no), 1-10 scale, file/photo uploads, select (dropdown), multiple select, radio buttons, and date pickers.
-   **Conditional Logic**: Questions can be configured to appear or disappear based on previous answers.
-   **Session Management**: Respondents can save and resume incomplete forms.
-   **Authentication & Authorization**: Secure user authentication with Laravel Sanctum and role-based access control.
-   **Responsive Frontend**: Built with React.js and `shadcn/ui` for a modern and adaptive user experience across devices.

## Technical Stack

### Backend
-   **Laravel 12 (PHP 8.3+)**: Robust PHP framework for the API.
-   **MySQL / PostgreSQL (SQLite for local dev)**: Database for storing forms, questions, and responses.
-   **Laravel Sanctum**: API token-based authentication.
-   **Laravel File Storage**: For handling uploaded files and images.
-   **Scramble**: API documentation generation.

### Frontend
-   **React.js**: JavaScript library for building user interfaces.
-   **Vite**: Fast build tool for modern web projects.
-   **pnpm**: Efficient package manager.
-   **shadcn/ui**: Re-usable UI components built with Radix UI and Tailwind CSS.
-   **React Router DOM**: For declarative routing.
-   **Axios**: HTTP client for API requests.
-   **date-fns**: For date manipulation.

## Getting Started

To get the project up and running on your local machine, follow these steps:

### Prerequisites
-   PHP 8.3+ (with Composer)
-   Node.js (LTS recommended)
-   pnpm (install globally: `npm install -g pnpm`)
-   Docker (optional, but recommended for backend setup)

### Backend Setup
1.  Navigate to the `backend` directory:
    ```bash
    cd backend
    ```
2.  Install Composer dependencies:
    ```bash
    composer install
    ```
3.  Copy the environment file and generate an application key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4.  Configure your database in the `.env` file. For SQLite, ensure `DB_CONNECTION=sqlite` and create an empty `database.sqlite` file in the `database/` directory.
5.  Run migrations and seeders to set up the database and populate it with demo data:
    ```bash
    php artisan migrate:fresh --seed
    ```
6.  Create a Filament admin user (optional, for backend dashboard access):
    ```bash
    php artisan make:filament-user
    ```
7.  Start the Laravel development server:
    ```bash
    php artisan serve
    ```

### Frontend Setup
1.  Navigate to the `frontend-react` directory:
    ```bash
    cd frontend-react
    ```
2.  Install Node.js dependencies using pnpm:
    ```bash
    pnpm install
    ```
3.  Start the frontend development server:
    ```bash
    pnpm dev
    ```

### Accessing the Application
-   The backend API will typically be available at `http://127.0.0.1:8000/api`.
-   The frontend application will typically be available at `http://localhost:5173`.

**Demo User Credentials (from seeders):**
-   **Admin/Supervisor:** `admin@example.com` / `password` (created via `make:filament-user`)
-   **Supervisor:** `supervisor@example.com` / `password`
-   **Employee:** `employee@example.com` / `password`

## API Documentation

For detailed API endpoint specifications, refer to the auto-generated OpenAPI specification at `backend/api.json`. You can use tools like Swagger UI or Postman to explore it interactively.
