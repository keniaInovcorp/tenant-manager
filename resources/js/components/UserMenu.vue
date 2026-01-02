<template>
  <div class="relative inline-block" ref="dropdownRef">
    <button
      @click.stop="showDropdown = !showDropdown"
      class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
      <span class="font-medium text-gray-700 dark:text-gray-300">{{ userName }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div
      v-show="showDropdown"
      class="absolute top-full right-0 mt-2 w-48 bg-gray-800 dark:bg-gray-800 border border-gray-700 dark:border-gray-700 rounded-lg shadow-lg z-50"
    >
      <div class="p-2">
        <!-- Logout -->
        <form method="POST" action="/logout" class="w-full">
          <input type="hidden" name="_token" :value="csrfToken" />
          <button
            type="submit"
            class="w-full px-3 py-2 text-left text-sm text-white hover:bg-gray-700 rounded-md transition-colors flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="text-white">Logout</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
  userName: {
    type: String,
    required: true
  }
});

const showDropdown = ref(false);
const dropdownRef = ref(null);

const csrfToken = computed(() => {
  return document.querySelector('meta[name="csrf-token"]')?.content || '';
});

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    showDropdown.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>


