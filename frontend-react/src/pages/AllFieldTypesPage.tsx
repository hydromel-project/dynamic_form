import React from 'react';
import { useParams } from 'react-router-dom';
import DynamicForm from '@/components/DynamicForm';
import { getFormById } from '@/services/formService';
import { Form } from '@/types/form';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { useEffect, useState } from 'react';

const AllFieldTypesPage: React.FC = () => {
  const formId = 2; // Assuming the All Input Types Demo Form has ID 2
  const [form, setForm] = useState<Form | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchForm = async () => {
      try {
        const fetchedForm = await getFormById(formId);
        setForm(fetchedForm);
      } catch (err) {
        setError('Failed to load demo form.');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchForm();
  }, [formId]);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-full">
        <Card className="w-full max-w-md text-center">
          <CardHeader>
            <CardTitle>Loading Demo Form...</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">Please wait while the demo form is being loaded.</p>
            <Skeleton className="h-4 w-[250px] mt-4" />
            <Skeleton className="h-4 w-[200px] mt-2" />
          </CardContent>
        </Card>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex items-center justify-center h-full">
        <Card className="w-full max-w-md text-center">
          <CardHeader>
            <CardTitle className="text-destructive">Error</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-destructive">{error}</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (!form) {
    return (
      <div className="flex items-center justify-center h-full">
        <Card className="w-full max-w-md text-center">
          <CardHeader>
            <CardTitle>Demo Form Not Found</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">The demo form could not be found. Ensure it's seeded.</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="flex justify-center items-center py-8">
      <div className="w-full max-w-2xl">
        <DynamicForm form={form} />
      </div>
    </div>
  );
};

export default AllFieldTypesPage;
