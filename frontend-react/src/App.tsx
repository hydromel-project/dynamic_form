import React from 'react';
import { Routes, Route } from 'react-router-dom';
import LoginPage from './pages/LoginPage';
import HomePage from './pages/HomePage';
import Layout from './components/Layout';
import { isAuthenticated } from './services/session';
import FormPage from './pages/FormPage';
import FormsListPage from './pages/FormsListPage';

const App: React.FC = () => {
  return (
    <Layout>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route
          path="/"
          element={isAuthenticated() ? <HomePage /> : <LoginPage />}
        />
        <Route path="/forms" element={isAuthenticated() ? <FormsListPage /> : <LoginPage />} />
        <Route path="/forms/:formId" element={isAuthenticated() ? <FormPage /> : <LoginPage />} />
        {/* Add other routes here, e.g., for supervisor dashboard */}
      </Routes>
    </Layout>
  );
};

export default App;