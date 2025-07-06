import React from 'react';
import { Link } from 'react-router-dom';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const FormsListPage: React.FC = () => {
  return (
    <div className="flex items-center justify-center h-full">
      <Card className="w-full max-w-md text-center">
        <CardHeader>
          <CardTitle>Forms List Page</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <p className="text-muted-foreground">
            This page will display a list of all available forms.
          </p>
          <p className="text-muted-foreground">
            For now, you can navigate to a specific form using its ID.
          </p>
          <Link to="/forms/1">
            <Button variant="outline">Go to Form 1 (Example)</Button>
          </Link>
        </CardContent>
      </Card>
    </div>
  );
};

export default FormsListPage;