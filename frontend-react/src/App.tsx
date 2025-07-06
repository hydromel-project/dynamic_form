import React from 'react';
import { Routes, Route } from 'react-router-dom';
import LoginPage from '@/pages/LoginPage';
import HomePage from '@/pages/HomePage';
import Layout from '@/components/layout'; // Import the new Layout component
import { isAuthenticated } from '@/services/session';

const App: React.FC = () => {
  return (
    <Layout>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route
          path="/"
          element={isAuthenticated() ? <HomePage /> : <LoginPage />}
        />
        {/* Add other routes here, e.g., for forms, supervisor dashboard */}
      </Routes>
    </Layout>
  );
};

export default App;
