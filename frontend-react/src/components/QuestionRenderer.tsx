import React from "react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Checkbox } from "@/components/ui/checkbox";
import type { Question } from "../types/form";

interface QuestionRendererProps {
  question: Question;
  value: any;
  onChange: (value: any) => void;
  isVisible: boolean; // Prop to control visibility based on conditional logic
}

const QuestionRenderer: React.FC<QuestionRendererProps> = ({
  question,
  value,
  onChange,
  isVisible,
}) => {
  if (!isVisible) {
    return null; // Don't render if not visible
  }

  const renderInput = () => {
    switch (question.type) {
      case "text":
        return (
          <Input
            type="text"
            value={value || ""}
            onChange={(e) => onChange(e.target.value)}
            required={question.required}
          />
        );
      case "number":
        return (
          <Input
            type="number"
            value={value || ""}
            onChange={(e) => onChange(e.target.value)}
            required={question.required}
          />
        );
      case "boolean":
        return (
          <div className="flex items-center space-x-2">
            <Checkbox
              id={`checkbox-${question.id}`}
              checked={!!value}
              onCheckedChange={onChange}
            />
            <Label htmlFor={`checkbox-${question.id}`}>Yes</Label>
          </div>
        );
      case "1-10_scale":
        return (
          <Input
            type="range"
            min="1"
            max="10"
            value={value || 1}
            onChange={(e) => onChange(parseInt(e.target.value))}
            className="mt-1 block w-full"
          />
        );
      case "file":
      case "photo":
        return (
          <Input
            type="file"
            onChange={(e) =>
              onChange(e.target.files ? e.target.files[0] : null)
            }
            required={question.required}
          />
        );
      default:
        return (
          <p className="text-red-500">
            Unsupported question type: {question.type}
          </p>
        );
    }
  };

  return (
    <div className="mb-4 p-4 border rounded-md shadow-sm bg-card text-card-foreground">
      <Label
        htmlFor={`question-${question.id}`}
        className="block text-sm font-medium mb-2"
      >
        {question.question_text}
        {question.required && <span className="text-destructive"> *</span>}
      </Label>
      {renderInput()}
    </div>
  );
};

export default QuestionRenderer;

