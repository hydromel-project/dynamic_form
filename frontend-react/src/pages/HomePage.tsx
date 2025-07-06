import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const HomePage: React.FC = () => {
  return (
    <div className="flex h-full items-center justify-center">
      <Card className="w-full max-w-md text-center">
        <CardHeader>
          <CardTitle>Welcome to the Home Page!</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-muted-foreground">Your journey begins here.</p>
        </CardContent>
      </Card>
    </div>
  );
};

export default HomePage;