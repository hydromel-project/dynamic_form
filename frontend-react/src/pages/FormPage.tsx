import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import DynamicForm from "../components/DynamicForm";
import { getFormById } from "../services/formService";
import type { Form } from "../types/form";

const FormPage: React.FC = () => {
  const { formId } = useParams<{ formId: string }>();
  const [form, setForm] = useState<Form | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchForm = async () => {
      if (!formId) {
        setError("Form ID is missing.");
        setLoading(false);
        return;
      }
      try {
        const fetchedForm = await getFormById(parseInt(formId));
        setForm(fetchedForm);
      } catch (err) {
        setError("Failed to load form.");
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchForm();
  }, [formId]);

  if (loading) {
    return <div className="text-center text-text">Loading form...</div>;
  }

  if (error) {
    return <div className="text-center text-red-500">Error: {error}</div>;
  }

  if (!form) {
    return <div className="text-center text-text">Form not found.</div>;
  }

  return (
    <div className="flex justify-center items-center py-8">
      <div className="w-full max-w-2xl">
        <DynamicForm form={form} />
      </div>
    </div>
  );
};

export default FormPage;
