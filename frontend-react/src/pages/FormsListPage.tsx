import React from 'react';

const FormsListPage: React.FC = () => {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      <h1 className="text-4xl font-bold text-gray-800">Forms List Page (Coming Soon!)</h1>
      <p className="text-lg text-gray-600 mt-4">You can navigate to a specific form using /forms/:formId</p>
      <p className="text-lg text-gray-600 mt-4">Example: <a href="/forms/1" className="text-blue-500">/forms/1</a></p>
    </div>
  );
};

export default FormsListPage;
