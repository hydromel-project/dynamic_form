<template>
  <va-card class="login-card">
    <va-card-title>Login</va-card-title>
    <va-card-content>
      <form @submit.prevent="login">
        <va-input
          v-model="email"
          label="Email"
          type="email"
          class="mb-4"
          required
        />
        <va-input
          v-model="password"
          label="Password"
          type="password"
          class="mb-4"
          required
        />
        <va-button type="submit" class="w-full">
          Login
        </va-button>
      </form>
    </va-card-content>
  </va-card>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

export default defineComponent({
  name: 'LoginView',
  setup() {
    const email = ref('')
    const password = ref('')
    const authStore = useAuthStore()
    const router = useRouter()

    const login = async () => {
      try {
        await authStore.login(email.value, password.value)
        router.push('/') // Redirect to home page
      } catch (error) {
        console.error('Login failed:', error)
        alert('Login failed. Please check your credentials.')
      }
    }

    return {
      email,
      password,
      login
    }
  }
})
</script>

<style scoped>
.login-card {
  max-width: 400px;
  margin: 50px auto;
}
</style>
