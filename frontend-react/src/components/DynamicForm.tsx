import React, { useState, useEffect } from "react";
import QuestionRenderer from "./QuestionRenderer";
import { submitResponse } from "@/services/formService";
import { Button } from "@/components/ui/button";
import type { Form, Question } from "../types/form";

interface DynamicFormProps {
  form: Form;
}

const DynamicForm: React.FC<DynamicFormProps> = ({ form }) => {
  const [answers, setAnswers] = useState<Record<number, any>>({});
  const [visibleQuestions, setVisibleQuestions] = useState<
    Record<number, boolean>
  >({});
  const [submissionStatus, setSubmissionStatus] = useState<
    "idle" | "submitting" | "success" | "error"
  >("idle");

  useEffect(() => {
    // Initialize visibility for all questions
    const initialVisibility: Record<number, boolean> = {};
    form.json_schema.questions.forEach((q) => {
      initialVisibility[q.id] = true; // Assume visible by default
    });
    setVisibleQuestions(initialVisibility);
  }, [form]);

  useEffect(() => {
    // Re-evaluate visibility when answers change
    const newVisibleQuestions: Record<number, boolean> = {
      ...visibleQuestions,
    };
    form.json_schema.questions.forEach((q) => {
      if (q.conditional_rules) {
        const rule = q.conditional_rules.show_if;
        const dependentQuestionAnswer = answers[rule.question_id];
        let isConditionMet = false;

        switch (rule.operator) {
          case "equals":
            isConditionMet = dependentQuestionAnswer === rule.value;
            break;
          case "not_equals":
            isConditionMet = dependentQuestionAnswer !== rule.value;
            break;
          case "greater_than":
            isConditionMet = dependentQuestionAnswer > rule.value;
            break;
          case "less_than":
            isConditionMet = dependentQuestionAnswer < rule.value;
            break;
          default:
            isConditionMet = true; // Should not happen with defined operators
        }
        newVisibleQuestions[q.id] = isConditionMet;
      } else {
        newVisibleQuestions[q.id] = true; // Always visible if no rules
      }
    });
    setVisibleQuestions(newVisibleQuestions);
  }, [answers, form.json_schema.questions]);

  const handleAnswerChange = (questionId: number, value: any) => {
    setAnswers((prevAnswers) => ({
      ...prevAnswers,
      [questionId]: value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmissionStatus("submitting");

    // Filter answers to only include visible and required questions
    const answersToSubmit: Record<number, any> = {};
    let isValid = true;

    form.json_schema.questions.forEach((q) => {
      if (visibleQuestions[q.id]) {
        if (
          q.required &&
          (answers[q.id] === undefined ||
            answers[q.id] === null ||
            answers[q.id] === "")
        ) {
          isValid = false; // Mark form as invalid if a required visible question is not answered
          // Optionally, add visual feedback for missing required fields
        }
        answersToSubmit[q.id] = answers[q.id];
      }
    });

    if (!isValid) {
      setSubmissionStatus("error");
      alert("Please fill in all required fields.");
      return;
    }

    try {
      await submitResponse(form.id, answersToSubmit);
      setSubmissionStatus("success");
      setAnswers({}); // Clear form
      alert("Form submitted successfully!");
    } catch (error) {
      setSubmissionStatus("error");
      alert("Failed to submit form. Please try again.");
    }
  };

  return (
    <form
      onSubmit={handleSubmit}
      className="space-y-6 p-6 bg-card rounded-lg shadow-lg"
    >
      <h2 className="text-2xl font-bold text-card-foreground mb-4">
        {form.name}
      </h2>
      <p className="text-muted-foreground mb-6">{form.description}</p>

      {form.json_schema.questions.map((question) => (
        <QuestionRenderer
          key={question.id}
          question={question}
          value={answers[question.id]}
          onChange={(value) => handleAnswerChange(question.id, value)}
          isVisible={visibleQuestions[question.id]}
        />
      ))}

      <Button
        type="submit"
        className="w-full"
        disabled={submissionStatus === "submitting"}
      >
        {submissionStatus === "submitting" ? "Submitting..." : "Submit Form"}
      </Button>
      {submissionStatus === "success" && (
        <p className="text-green-500 text-center mt-2">
          Form submitted successfully!
        </p>
      )}
      {submissionStatus === "error" && (
        <p className="text-red-500 text-center mt-2">
          Failed to submit form. Please check your answers.
        </p>
      )}
    </form>
  );
};

export default DynamicForm;

