import React from 'react';
import { Routes, Route } from 'react-router-dom';
import LoginPage from '@/pages/LoginPage';
import HomePage from '@/pages/HomePage';
import Layout from '@/components/layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import FormsListPage from '@/pages/FormsListPage';
import FormPage from '@/pages/FormPage';
import useSessionChecker from '@/hooks/useSessionChecker';

const App: React.FC = () => {
  useSessionChecker(); // Call the session checker hook

  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route
        path="/*"
        element={
          <ProtectedRoute>
            <Layout>
              <Routes>
                <Route path="/" element={<HomePage />} />
                <Route path="/forms" element={<FormsListPage />} />
                <Route path="/forms/:formId" element={<FormPage />} />
                {/* Add other protected routes here, e.g., for supervisor dashboard */}
              </Routes>
            </Layout>
          </ProtectedRoute>
        }
      />
    </Routes>
  );
};

export default App;
