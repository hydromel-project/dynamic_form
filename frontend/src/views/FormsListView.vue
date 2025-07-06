<template>
  <va-card>
    <va-card-title>Available Forms</va-card-title>
    <va-card-content>
      <va-list>
        <va-list-item
          v-for="form in forms"
          :key="form.id"
          :to="{ name: 'form-renderer', params: { formId: form.id } }"
        >
          <va-list-item-section>
            <va-list-item-title>{{ form.name }}</va-list-item-title>
            <va-list-item-description>{{ form.description }}</va-list-item-description>
          </va-list-item-section>
        </va-list-item>
      </va-list>
      <div v-if="forms.length === 0">
        <p>No forms available.</p>
      </div>
    </va-card-content>
  </va-card>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import type { Form } from '../schemas';
import { FormSchema } from '../schemas';

const forms = ref<Form[]>([]);

onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/forms');
    forms.value = response.data.map((form: any) => FormSchema.parse(form));
  } catch (error) {
    console.error('Error fetching forms:', error);
  }
});
</script>

<style scoped>
</style>
