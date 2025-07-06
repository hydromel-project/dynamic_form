<script setup lang="ts">
import { useAuthStore } from './stores/auth';
import { useRouter } from 'vue-router';
import { useColors } from 'vuestic-ui';

const authStore = useAuthStore();
const router = useRouter();
const { applyPreset, currentPresetName } = useColors();

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};

const toggleTheme = () => {
  if (currentPresetName.value === 'light') {
    applyPreset('dark');
  } else {
    applyPreset('light');
  }
};
</script>

<template>
  <va-app-bar color="background2">
    <va-button icon="home" preset="secondary" :to="{ name: 'home' }" color="onSurface">
      Home
    </va-button>
    <va-button preset="secondary" :to="{ name: 'forms-list' }" color="onSurface">
      Forms
    </va-button>

    <va-spacer />

    <va-button preset="secondary" @click="toggleTheme" :icon="currentPresetName.value === 'light' ? 'dark_mode' : 'light_mode'" color="onSurface" />

    <va-button v-if="!authStore.isAuthenticated" preset="secondary" :to="{ name: 'login' }" color="onSurface">
      Login
    </va-button>
    <va-button v-else preset="secondary" @click="handleLogout" color="onSurface">
      Logout
    </va-button>
  </va-app-bar>

  <main class="container mx-auto p-4">
    <router-view />
  </main>
</template>

<style scoped>
.container {
  max-width: 960px;
  margin: 0 auto;
}
</style>
