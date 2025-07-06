import React from 'react';
import { LoginForm } from '@/components/login-form';

const LoginPage: React.FC = () => {
  return (
    <div className="flex min-h-screen items-center justify-center p-4">
      <LoginForm className="w-full max-w-md" />
    </div>
  );
};

export default LoginPage;
