# Dynamic Form Management System - Comprehensive Documentation

This document provides a comprehensive overview of the Dynamic Form Management System, covering its API, codebase structure, and setup instructions.

## Table of Contents
1.  [Project Overview](#1-project-overview)
2.  [Technical Stack](#2-technical-stack)
3.  [API Documentation](#3-api-documentation)
    *   [Base URL](#base-url)
    *   [Authentication](#authentication)
    *   [Endpoints](#endpoints)
4.  [Codebase Overview](#4-codebase-overview)
    *   [Project Structure](#project-structure)
    *   [Backend (Laravel)](#backend-laravel)
    *   [Frontend (Vue.js)](#frontend-vuejs)
5.  [Setup and Running Locally](#5-setup-and-running-locally)
    *   [Prerequisites](#prerequisites)
    *   [Backend Setup](#backend-setup)
    *   [Frontend Setup](#frontend-setup)
    *   [Running the Application](#running-the-application)
6.  [Testing](#6-testing)

---

## 1. Project Overview

This project aims to build a dynamic, self-hosted form management system that allows administrators to create custom forms with highly flexible question types, branching (conditional) logic, file/image uploads, and multi-session support for respondents. Supervisors have a management dashboard to analyze and export responses.

## 2. Technical Stack

**Backend:**
*   Laravel 12 (PHP 8.3+)
*   MySQL or PostgreSQL (SQLite for local development)
*   Laravel Sanctum (API token-based authentication)
*   Laravel File Storage (for uploaded files/images)
*   Spatie Laravel Permission (Role-based access control)
*   Maatwebsite/Laravel Excel (Response export)
*   FilamentPHP (Supervisor dashboard)
*   Scramble (API documentation generator)

**Frontend:**
*   Vue 3
*   Vite
*   Pinia (State Management)
*   Vue Router
*   Vuestic UI (Component Library)
*   Zod (Schema Validation)
*   Axios (HTTP Client)

## 3. API Documentation

The backend exposes a RESTful API for managing forms, questions, responses, and user authentication.

### Base URL
`http://127.0.0.1:8000/api` (for local development)

### Authentication
API authentication is handled via **Laravel Sanctum** using bearer tokens. Most endpoints require authentication.

*   **Register:** `POST /api/register`
*   **Login:** `POST /api/login`
*   **Logout:** `POST /api/logout` (Requires Authorization header)

### Endpoints
For detailed API endpoint specifications (request/response schemas, parameters, etc.), please refer to the auto-generated OpenAPI specification:

`backend/api.json`

You can use tools like [Swagger UI](https://swagger.io/tools/swagger-ui/) or [Postman](https://www.postman.com/) to import this file and explore the API interactively.

## 4. Codebase Overview

### Project Structure
The project is organized into two main top-level directories:

*   `backend/`: Contains the Laravel API application.
*   `frontend/`: Contains the Vue.js Single Page Application (SPA).

### Backend (Laravel)

*   **`app/Http/Controllers/`**: Contains the API controllers (`AuthController`, `FormController`, `QuestionController`, `ResponseController`, `SupervisorController`) that handle incoming HTTP requests and return JSON responses.
*   **`app/Models/`**: Defines the Eloquent models (`User`, `Form`, `Question`, `Response`, `ResponseAnswer`) that interact with the database.
*   **`database/migrations/`**: Database schema definitions for all tables.
*   **`database/seeders/`**: Contains seeders (`RolesAndPermissionsSeeder`, `SafetyProcedureSeeder`) for populating the database with initial data and demo content.
*   **`routes/api.php`**: Defines all API routes, including authentication, forms, questions, responses, and supervisor endpoints. These routes are prefixed with `/api` and use the `api` middleware group.
*   **`bootstrap/app.php`**: The core Laravel application bootstrap file, responsible for loading routes and middleware.
*   **`app/Http/Kernel.php`**: Defines the application's HTTP middleware stack and middleware groups (`web`, `api`).
*   **`app/Providers/RouteServiceProvider.php`**: Configures how routes are loaded and middleware is applied.

### Frontend (Vue.js)

*   **`src/components/`**: Reusable Vue components, such as `FormRenderer.vue` for dynamic form rendering.
*   **`src/views/`**: Vue components that serve as pages or views, such as `LoginView.vue` and `FormsListView.vue`.
*   **`src/stores/`**: Pinia stores (`auth.ts`) for centralized state management, particularly for authentication.
*   **`src/router/index.ts`**: Configures Vue Router, defining application routes and navigation guards.
*   **`src/schemas/index.ts`**: Zod schemas for data validation and type inference, mirroring backend models.
*   **`src/plugins/axios.ts`**: Axios interceptors for handling authentication headers and response errors.
*   **`src/main.ts`**: The main entry point for the Vue application, where Vuestic UI, Pinia, and Vue Router are initialized.

## 5. Setup and Running Locally

### Prerequisites
*   PHP 8.3+
*   Composer
*   Node.js (LTS recommended)
*   npm or pnpm
*   A database (MySQL, PostgreSQL, or SQLite)

### Backend Setup
1.  **Navigate to the backend directory:**
    `cd backend`
2.  **Install Composer dependencies:**
    `composer install`
3.  **Copy environment file:**
    `cp .env.example .env`
4.  **Generate application key:**
    `php artisan key:generate`
5.  **Configure database:**
    Open `.env` and configure your database connection. For SQLite, ensure `DB_CONNECTION=sqlite` and create an empty `database.sqlite` file in the `database/` directory.
6.  **Run migrations and seeders:**
    `php artisan migrate:fresh --seed`
    This will set up the database schema and populate it with initial roles, a test admin user, and demo safety check forms/responses.
7.  **Create Filament Admin User:**
    `php artisan make:filament-user`
    Follow the prompts to create an admin user for the Filament dashboard.

### Frontend Setup
1.  **Navigate to the frontend directory:**
    `cd frontend`
2.  **Install Node.js dependencies:**
    `npm install` (or `pnpm install`)

### Running the Application
1.  **Start the Backend Server:**
    From the `backend` directory:
    `php artisan serve`
    (This will typically run on `http://127.0.0.1:8000`)
2.  **Start the Frontend Development Server:**
    From the `frontend` directory:
    `pnpm run dev`
    (This will typically run on `http://localhost:5173`)

Access the frontend application in your browser at `http://localhost:5173`.

**Demo User Credentials:**
*   **Admin/Supervisor:** `admin@example.com` / `password` (created via `make:filament-user`)
*   **Supervisor:** `supervisor@example.com` / `password` (created by seeder)
*   **Employee:** `employee@example.com` / `password` (created by seeder)

## 6. Testing

### Backend Tests (PHPUnit)
To run the backend feature tests:

1.  Ensure your backend server is not running (or run tests in a separate terminal).
2.  Navigate to the `backend` directory.
3.  Run:
    `php artisan test --testsuite Feature`

### Frontend Testing
Manual testing of the frontend UI can be performed by interacting with the application in your browser at `http://localhost:5173`.

---

**Note:** This documentation will be continuously updated as the project evolves.
