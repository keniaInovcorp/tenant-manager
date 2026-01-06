<template>
  <div class="relative" v-if="tenants.length > 0" ref="dropdownRef">
    <button
      @click.stop="showDropdown = !showDropdown"
      class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <span class="font-medium text-gray-700 dark:text-gray-300">{{ currentTenant?.name || 'Selecionar Tenant' }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div
      v-show="showDropdown"
      class="absolute top-full left-0 mt-2 w-64 bg-gray-800 dark:bg-gray-800 border border-gray-700 dark:border-gray-700 rounded-lg shadow-lg z-50"
    >
      <div class="p-2">
        <div class="px-3 py-2 text-xs font-semibold text-white uppercase">
          Meus Tenants
        </div>

        <div
          v-for="tenant in tenants"
          :key="tenant.id"
          @click="switchTenant(tenant.id)"
          :class="[
            'px-3 py-2 rounded-md cursor-pointer transition-colors text-white',
            currentTenant?.id === tenant.id
              ? 'bg-blue-900/20 text-blue-300'
              : 'hover:bg-gray-700'
          ]"
        >
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-1">
              <span v-if="tenant.plan" :class="[
                'px-2 py-0.5 text-xs font-semibold rounded',
                tenant.plan === 'Free' ? 'bg-gray-600 text-gray-200' : '',
                tenant.plan === 'Pro' ? 'bg-blue-600 text-white' : '',
                tenant.plan === 'Enterprise' ? 'bg-purple-600 text-white' : ''
              ]">
                {{ tenant.plan }}
              </span>
              <div class="flex-1">
                <div class="font-medium text-white">{{ tenant.name }}</div>
                <div class="text-xs text-gray-400">{{ tenant.slug }}</div>
              </div>
            </div>
            <svg
              v-if="currentTenant?.id === tenant.id"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-blue-300 flex-shrink-0"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </div>

        <div class="border-t border-gray-700 mt-2 pt-2">
          <a
            :href="'/tenants/onboarding'"
            class="block w-full px-3 py-2 text-left text-sm text-white hover:bg-gray-700 rounded-md transition-colors"
          >
            + Criar Novo Tenant
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const tenants = ref([]);
const currentTenant = ref(null);
const showDropdown = ref(false);
const dropdownRef = ref(null);

const fetchTenants = async () => {
  try {
    const response = await axios.get('/api/tenants');
    tenants.value = response.data.tenants;
    currentTenant.value = response.data.current;
  } catch (error) {
    console.error('Erro ao carregar tenants:', error);
  }
};

const switchTenant = async (tenantId) => {
  try {
    await axios.post('/api/tenants/switch', { tenant_id: tenantId });
    showDropdown.value = false;
    window.location.reload();
  } catch (error) {
    console.error('Erro ao alternar tenant:', error);
    alert('Erro ao alternar tenant. Tente novamente.');
  }
};

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    showDropdown.value = false;
  }
};

onMounted(() => {
  fetchTenants();
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
