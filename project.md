# 📄 Backend Development Plan – Dynamic Form Management System (Laravel 12 + React)

## 📌 Project Overview

This project aims to build a **dynamic, self-hosted form management system** that allows administrators to create custom forms with highly flexible question types, branching (conditional) logic, file/image uploads, and multi-session support for respondents. Forms will be fillable on any modern web browser (PC, Mac, tablet), and supervisors will have a management dashboard to analyze and export responses.

## 🗺️ Project Roadmap

This roadmap outlines the key phases and milestones for the project. Progress will be tracked here.

### Phase 1: Project Setup & Foundation

- [x] Set up Laravel 12 project with Docker.
- [x] Initialize Git repository.
- [x] Install and configure Laravel Sanctum for authentication.
- [x] Install and configure Scramble for API documentation.
- [x] Create initial database migrations for `users`, `forms`, `questions`, `responses`, and `response_answers`.

### Phase 2: Core Form & Question Management

- [x] Implement CRUD API endpoints for Forms (`/api/forms`).
- [x] Implement CRUD API endpoints for Questions (`/api/forms/{form_id}/questions`).
- [x] Develop feature tests for Form and Question APIs.

### Phase 3: Response Handling & Logic

- [x] Implement API endpoints for starting, saving, and submitting responses.
- [x] Implement file upload logic and storage.
- [x] Implement backend validation for conditional logic.
- [x] Develop feature tests for the entire response submission flow.

### Phase 4: Supervisor & Management Features

- [ ] Implement Supervisor API endpoints for viewing and filtering responses.
- [ ] Implement response export functionality (CSV/Excel).
- [ ] Set up role-based access control using Spatie Laravel Permission.
- [ ] Build or integrate a supervisor dashboard (Filament or custom).

### Phase 5: Frontend Implementation (React.js)

- [x] Set up the React.js project with Vite.
- [x] Integrate `shadcn/ui` for UI components.
- [x] Implement user authentication flow (login, logout, session persistence).
- [x] Implement periodic session validity check.
- [x] Develop Axios HTTP proxy for API calls.
- [x] Develop components for rendering dynamic forms based on JSON schema.
- [x] Support various question types (text, number, boolean, 1-10 scales, file, photo, select, multiple select, radio, date).
- [x] Implement conditional logic for questions.
- [x] Implement form filling and submission, including file uploads.
- [x] Create a page to list available forms.
- [x] Create a page to demonstrate all field types.
- [ ] Build the supervisor dashboard interface.

### Phase 6: Deployment & Finalization

- [ ] Finalize Docker Compose configuration for production.
- [ ] Write comprehensive documentation.
- [ ] Perform final testing and bug fixing.

---

## 🎯 Objectives

- Allow creation and management of forms with dynamic, user-defined questions.
- Support question types including text, number, boolean, 1–10 scales, file uploads, photos, and future extensions.
- Allow branching logic (conditional questions based on previous answers).
- Enable respondents to save and resume incomplete forms.
- Enable multiple people to respond to the same form at different times.
- Provide a supervisor/manager interface to review, filter, and export responses.
- Secure user authentication and role-based access for respondents and supervisors.
- Support local self-hosting with Docker.

---

## 🏗️ Technical Stack

- **Laravel 12 (PHP 8.3+)**
- **MySQL** or **PostgreSQL**
- **Laravel Sanctum** (API token-based authentication)
- **React.js** (frontend, will consume Laravel API)
- **shadcn/ui** (React UI components)
- **Laravel File Storage** (for uploaded files/images)
- **Docker Compose** for local deployment
- **Scramble** (API documentation generator)
- Optional: **FilamentPHP** for a supervisor dashboard

---

## 📂 Backend Modules & Responsibilities

### 1️⃣ Authentication

- **Laravel Sanctum** will be used for secure API token authentication.
- The project will leverage Laravel's built-in authentication scaffolding as a starting point.
- The default `users` table provided by Laravel will be used.
- Role-based access control will be implemented with three primary roles: **admin / supervisor / respondent**.

---

### 2️⃣ Form Management

- Create, update, delete forms
- Store a JSON schema describing form layout and questions
- Manage form versioning for future changes

#### Data Structure:

forms

- id
- name
- description
- json_schema (JSON, describes question flow and branching)
- created_at
- updated_at

---

### 3 Question Definitions

questions

- id
- form_id
- question_text
- type (text, number, boolean, file, etc.)
- options (JSON)
- conditional_rules (JSON)
- required (boolean)
- order
- created_at
- updated_at

### 4. Response Sessions

responses

- id
- form_id
- user_id (nullable if anonymous)
- session_token (string, unique identifier for resume)
- submitted (boolean)
- created_at
- updated_at

### 5. Answers

response_answers

- id
- response_id
- question_id
- answer (JSON, flexible for all types)
- created_at
- updated_at

### 6. File Uploads

- Laravel file storage (storage/app/public)
- Validate uploads for type/size
- Store path in response_answers.answer JSON

### 7. Conditional logic :

- Store conditional rules as JSON in the question record:

  ```json
  {
    "show_if": {
      "question_id": 5,
      "operator": "equals",
      "value": "no"
    }
  }
  ```

- Evaluate conditions on the frontend (Vue) for speed
- Validate conditions on the backend during final submission

### 8 Supervisor Dashboard

- View responses
- Filter/search responses by form, date, user
- Export results (Laravel Excel or CSV)
- Manage users and roles (Spatie Laravel Permission recommended)

## 🧪 Testing Strategy

- **Backend (Laravel & PHPUnit):** A comprehensive test suite will be developed using PHPUnit. This will include:
  - **Feature Tests:** To validate all API endpoints, ensuring they behave as expected regarding input, output, and status codes.
  - **Unit Tests:** For critical business logic, such as conditional rule evaluation, data validation, and file handling, to ensure correctness in isolation.
- **Continuous Integration:** A CI pipeline (e.g., GitHub Actions) will be set up to automatically run the test suite on every push to the main branch, preventing regressions.

## 🛠️ Development Guidelines

- **Artisan CLI:** Development will heavily utilize Laravel's `artisan` command-line interface for generating boilerplate code (controllers, models, migrations, etc.).
- **Official Packages:** Only officially recognized Laravel packages or those with a strong community reputation (e.g., Spatie) will be used to ensure stability and maintainability.
- **Customization:** When necessary, Laravel's default generated templates will be modified to fit project-specific requirements.

## API Endpoints

Authentication
POST /api/login
POST /api/logout
POST /api/register (if applicable)
Forms
GET /api/forms – list forms
POST /api/forms – create form
GET /api/forms/{id} – view form details
PUT /api/forms/{id} – update form
DELETE /api/forms/{id} – delete form
Questions (if separate)
GET /api/forms/{form_id}/questions
POST /api/forms/{form_id}/questions
PUT /api/questions/{id}
DELETE /api/questions/{id}
Responses
POST /api/responses/start – initialize a response session
POST /api/responses/{session_token}/save – save progress
POST /api/responses/{session_token}/submit – submit completed form
GET /api/responses/{session_token} – get saved answers to resume
Supervisor
GET /api/supervisor/responses – list responses with filters
GET /api/supervisor/responses/{id} – view single response
GET /api/supervisor/export – export responses to CSV/Excel

Deployment & Local Hosting
Docker Compose stack:
PHP-FPM (Laravel)
Nginx
MySQL/Postgres
.env environment variables
Local storage mounted for uploads
Supervisor jobs handled via php artisan queue:work
HTTPS certificates via Caddy or nginx self-signed if desired

Summary Deliverables
✅ Laravel 12 API server
✅ React-compatible JSON endpoints
✅ Dynamic question renderer (defined by JSON schema)
✅ File uploads support
✅ Session-based resume
✅ Supervisor dashboard (Filament or custom)
✅ Role-based user management
✅ Secure local Docker Compose deployment
✅ Documented codebase and migration strategy

🛠️ Next Steps for the Team

- Finalize the data model and JSON schema for dynamic questions
- ✅ Set up a Laravel 12 project with Docker (local setup for now)
- ✅ Build migrations for the key tables (forms, questions, responses, response_answers)
- ✅ Scaffold authentication with Sanctum
- ✅ Develop the CRUD for forms/questions
- ✅ Implement the responses flow with partial saves
- ✅ Build the supervisor module
- Test and refine

Questions or clarifications can be tracked in the project issue board.