<template>
  <va-card v-if="form">
    <va-card-title>{{ form.name }}</va-card-title>
    <va-card-content>
      <p>{{ form.description }}</p>

      <form @submit.prevent="submitForm">
        <div v-for="question in visibleQuestions" :key="question.id" class="form-group">
          <va-input
            v-if="question.type === 'text'"
            :label="question.question_text"
            v-model="answers[question.id]"
            :rules="question.required ? [(v: string) => !!v || 'Field is required'] : []"
          />
          <va-input
            v-else-if="question.type === 'number'"
            :label="question.question_text"
            v-model.number="answers[question.id]"
            type="number"
            :rules="question.required ? [(v: number) => v !== null || 'Field is required'] : []"
          />
          <va-switch
            v-else-if="question.type === 'boolean'"
            :label="question.question_text"
            v-model="answers[question.id]"
            true-inner-label="YES" 
            false-inner-label="NO"
          />
          <va-select
            v-else-if="question.type === 'select'"
            :label="question.question_text"
            v-model="answers[question.id]"
            :options="question.options || []"
            :rules="question.required ? [(v: string) => !!v || 'Field is required'] : []"
          />
          <div v-else-if="question.type === 'file'">
            <label>{{ question.question_text }}</label>
            <input type="file" @change="handleFileUpload($event, question.id)">
          </div>
        </div>
        <va-button type="submit">
          Submit
        </va-button>
      </form>
    </va-card-content>
  </va-card>
  <div v-else>
    Loading form...
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import type { Form, Question } from '../schemas';
import { FormSchema } from '../schemas';

const props = defineProps<{ formId: number }>();

const form = ref<Form | null>(null);
const answers = ref<{ [key: number]: any }>({});
const sessionToken = ref<string | null>(null);

const initializeAnswers = (questions: Question[]) => {
  questions.forEach((question: Question) => {
    if (question.type === 'boolean') {
      answers.value[question.id] = false;
    } else if (question.type === 'file') {
      answers.value[question.id] = null; // Store file object
    } else {
      answers.value[question.id] = '';
    }
  });
};

const evaluateCondition = (rule: any, currentAnswers: { [key: number]: any }) => {
  const targetAnswer = currentAnswers[rule.question_id];
  if (targetAnswer === undefined || targetAnswer === null) {
    return false; // If the target question is not answered, the condition is not met
  }

  // For boolean types, the answer is directly the boolean value
  // For other types, we assume the answer is stored directly or in a 'value' property
  const actualValue = typeof targetAnswer === 'object' && targetAnswer !== null && 'value' in targetAnswer ? targetAnswer.value : targetAnswer;

  switch (rule.operator) {
    case 'equals':
      return actualValue === rule.value;
    case 'not_equals':
      return actualValue !== rule.value;
    // Add more operators as needed
    default:
      return false;
  }
};

const visibleQuestions = computed(() => {
  if (!form.value) return [];

  const questions = form.value.json_schema as Question[];
  const currentAnswers = answers.value;

  return questions.filter(question => {
    if (!question.conditional_rules || !question.conditional_rules.show_if) {
      return true; // No conditional rules, so always visible
    }

    const rule = question.conditional_rules.show_if;
    return evaluateCondition(rule, currentAnswers);
  });
});

onMounted(async () => {
  try {
    const formResponse = await axios.get(`http://127.0.0.1:8000/api/forms/${props.formId}`);
    form.value = FormSchema.parse(formResponse.data);

    // Start a new response session
    const startResponse = await axios.post('http://127.0.0.1:8000/api/responses/start', {
      form_id: props.formId
    });
    sessionToken.value = startResponse.data.session_token;

    initializeAnswers(form.value.json_schema as Question[]);

  } catch (error) {
    console.error('Error fetching form or starting session:', error);
  }
});

const handleFileUpload = (event: Event, questionId: number) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    answers.value[questionId] = target.files[0];
  }
};

const submitForm = async () => {
  if (!sessionToken.value) {
    console.error('No session token available.');
    return;
  }

  const formData = new FormData();
  const answersData: { question_id: number; answer?: any; file?: File | null }[] = [];

  for (const question of visibleQuestions.value) { // Only submit visible questions
    const questionId = question.id;
    const answerValue = answers.value[questionId];

    if (question.type === 'file') {
      if (answerValue) {
        formData.append(`answers[${questionId}][file]`, answerValue);
        answersData.push({ question_id: questionId });
      }
    } else {
      answersData.push({ question_id: questionId, answer: answerValue });
    }
  }

  formData.append('answers', JSON.stringify(answersData));

  try {
    // Save progress first
    await axios.post(`http://127.0.0.1:8000/api/responses/${sessionToken.value}/save`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    // Then submit
    const submitResponse = await axios.post(`http://127.0.0.1:8000/api/responses/${sessionToken.value}/submit`);
    console.log('Form submitted successfully:', submitResponse.data);
    alert('Form submitted successfully!');
  } catch (error) {
    console.error('Error submitting form:', error);
    alert('Error submitting form. Check console for details.');
  }
};
</script>

<style scoped>
.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

input[type="text"],
input[type="number"],
select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

input[type="checkbox"] {
  margin-top: 0.5rem;
}

button {
  background-color: #007bff;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 1rem;
}

button:hover {
  background-color: #0056b3;
}
</style>
