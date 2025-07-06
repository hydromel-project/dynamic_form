# React + TypeScript + Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Babel](https://babeljs.io/) for Fast Refresh
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/) for Fast Refresh

## Expanding the ESLint configuration

If you are developing a production application, we recommend updating the configuration to enable type-aware lint rules:

```js
export default tseslint.config([
  globalIgnores(["dist"]),
  {
    files: ["**/*.{ts,tsx}"],
    extends: [
      // Other configs...

      // Remove tseslint.configs.recommended and replace with this
      ...tseslint.configs.recommendedTypeChecked,
      // Alternatively, use this for stricter rules
      ...tseslint.configs.strictTypeChecked,
      // Optionally, add this for stylistic rules
      ...tseslint.configs.stylisticTypeChecked,

      // Other configs...
    ],
    languageOptions: {
      parserOptions: {
        project: ["./tsconfig.node.json", "./tsconfig.app.json"],
        tsconfigRootDir: import.meta.dirname,
      },
      // other options...
    },
  },
]);
```

You can also install [eslint-plugin-react-x](https://github.com/Rel1cx/eslint-react/tree/main/packages/plugins/eslint-plugin-react-x) and [eslint-plugin-react-dom](https://github.com/Rel1cx/eslint-react/tree/main/packages/plugins/eslint-plugin-react-dom) for React-specific lint rules:

```js
// eslint.config.js
import reactX from "eslint-plugin-react-x";
import reactDom from "eslint-plugin-react-dom";

export default tseslint.config([
  globalIgnores(["dist"]),
  {
    files: ["**/*.{ts,tsx}"],
    extends: [
      // Other configs...
      // Enable lint rules for React
      reactX.configs["recommended-typescript"],
      // Enable lint rules for React DOM
      reactDom.configs.recommended,
    ],
    languageOptions: {
      parserOptions: {
        project: ["./tsconfig.node.json", "./tsconfig.app.json"],
        tsconfigRootDir: import.meta.dirname,
      },
      // other options...
    },
  },
]);
```

### Backend API Interaction

The frontend interacts with a Laravel backend API. The base URL for the API is `http://127.0.0.1:8000/api`. Authentication is handled via Laravel Sanctum using bearer tokens. Ensure the backend server is running before starting the frontend development server.

### Key Development Areas

- **Dynamic Form Rendering:** Develop components to interpret and render forms based on the JSON schema received from the backend. This includes handling various question types and their associated options.
- **Conditional Logic:** Implement the logic to dynamically show or hide questions based on previous answers, as defined in the form's JSON schema.
- **File Uploads:** Integrate file upload functionality for questions requiring file or image submissions.
- **Authentication Flow:** Implement the user login and logout processes, securely handling API tokens.
- **Supervisor Dashboard:** Build the UI for supervisors to view, filter, and export form responses.

### API Endpoints

The `frontend-react` application consumes the following API endpoints from the backend:

**Authentication**

- `POST /api/login`
- `POST /api/logout`
- `POST /api/register` (if applicable)

**Forms**

- `GET /api/forms` – list forms
- `POST /api/forms` – create form
- `GET /api/forms/{id}` – view form details
- `PUT /api/forms/{id}` – update form
- `DELETE /api/forms/{id}` – delete form

**Questions (if separate)**

- `GET /api/forms/{form_id}/questions`
- `POST /api/forms/{form_id}/questions`
- `PUT /api/questions/{id}`
- `DELETE /api/questions/{id}`

**Responses**

- `POST /api/responses/start` – initialize a response session
- `POST /api/responses/{session_token}/save` – save progress
- `POST /api/responses/{session_token}/submit` – submit completed form
- `GET /api/responses/{session_token}` – get saved answers to resume

**Supervisor**

- `GET /api/supervisor/responses` – list responses with filters
- `GET /api/supervisor/responses/{id}` – view single response
- `GET /api/supervisor/export` – export responses to CSV/Excel

### API Client

[Axios](https://axios-http.com/) is used as the HTTP client for making API requests to the backend. You can configure a base URL and interceptors for authentication or error handling. For example:

```typescript
import axios from "axios";

const apiClient = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
  headers: {
    "Content-Type": "application/json",
  },
});

// Example of adding an interceptor for authentication
apient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("authToken"); // Or wherever your token is stored
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

export default apiClient;
```

## Frontend Roadmap

This roadmap outlines the key features and development tasks for the `frontend-react` application.

### Phase 1: Core Functionality

- **User Authentication:**
  - Implement login and logout forms.
  - Integrate with backend authentication endpoints (`/api/login`, `/api/logout`).
  - Securely store and retrieve authentication tokens (e.g., using `localStorage`).
  - Implement route guards to protect authenticated routes.
- **Dynamic Form Rendering:**
  - Develop a robust component to parse and render forms based on the JSON schema received from the backend.
  - Support various question types (text, number, boolean, 1-10 scales, file uploads, photos).
  - Implement input validation based on schema definitions.
- **Form Filling and Submission:**
  - Allow users to fill out forms.
  - Implement saving progress (`/api/responses/{session_token}/save`).
  - Implement final form submission (`/api/responses/{session_token}/submit`).
  - Handle file uploads for relevant question types.
- **Conditional Logic:**
  - Implement frontend logic to dynamically show/hide questions based on previous answers as defined in the form's JSON schema.

### Phase 2: Supervisor Dashboard

- **Response Listing and Filtering:**
  - Develop a dashboard view to list all submitted responses (`/api/supervisor/responses`).
  - Implement filtering and searching capabilities (by form, date, user).
- **Single Response View:**
  - Create a detailed view for individual responses (`/api/supervisor/responses/{id}`).
  - Display all answers, including uploaded files/images.
- **Response Export:**
  - Integrate with the backend export endpoint (`/api/supervisor/export`) to allow supervisors to download responses (CSV/Excel).

### Phase 3: Enhancements and Refinements

- **Error Handling and User Feedback:**
  - Implement comprehensive error handling for API requests.
  - Provide clear and informative user feedback (e.g., loading states, success messages, error alerts).
- **UI/UX Improvements:**
  - Apply consistent styling and design principles across the application.
  - Improve responsiveness for various screen sizes.
- **Form Management (Admin/Supervisor):**
  - (If applicable and within scope) Implement UI for creating, editing, and deleting forms and questions, consuming the respective backend API endpoints.

## UI Components (shadcn/ui)

This project uses [shadcn/ui](https://ui.shadcn.com/) for its UI components. shadcn/ui provides a collection of re-usable components that you can copy and paste into your projects. They are built using Radix UI and Tailwind CSS.

### Adding Components

To add a new component from shadcn/ui, navigate to the `frontend-react` directory and use the following command:

```bash
npx shadcn-ui@latest add <component-name>
```

For example, to add the `Button` component:

```bash
npx shadcn-ui@latest add button
```

This will add the component's code to `src/components/ui/<component-name>.tsx` and install any necessary dependencies.
