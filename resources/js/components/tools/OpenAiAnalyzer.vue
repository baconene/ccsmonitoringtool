<template>
  <div class="p-6 space-y-4">
    <div class="space-y-2">
      <input type="file" @change="handleFileUpload($event, 1)" />
      <input type="file" @change="handleFileUpload($event, 2)" />
    </div>

    <button
      :disabled="loading"
      @click="analyze"
      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:opacity-50"
    >
      {{ loading ? 'Analyzing...' : 'Analyze with ChatGPT' }}
    </button>

    <div v-if="aiAnalysis" class="bg-gray-100 p-4 rounded whitespace-pre-wrap text-sm">
      {{ aiAnalysis }}
    </div>

    <transition name="fade">
      <div
        v-if="errorMessage"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
        role="alert"
      >
        <strong class="font-bold">Error: </strong>
        <span class="block sm:inline">{{ errorMessage }}</span>
        <span
          class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer"
          @click="errorMessage = ''"
        >
          <svg
            class="fill-current h-6 w-6 text-red-500"
            role="button"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
          >
            <title>Close</title>
            <path
              d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 00-1.414 1.414L8.586 10l-2.934 2.934a1 1 0 101.414 1.414L10 11.414l2.934 2.934a1 1 0 001.414-1.414L11.414 10l2.934-2.934a1 1 0 000-1.414z"
            />
          </svg>
        </span>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'OpenAiAnalyzerLayout',
  data() {
    return {
      json1: null,
      json2: null,
      aiAnalysis: '',
      errorMessage: '',
      loading: false,
    };
  },
  methods: {
    handleFileUpload(event, fileNum) {
      const file = event.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = () => {
        try {
          const json = JSON.parse(reader.result);
          if (fileNum === 1) {
            this.json1 = json;
          } else {
            this.json2 = json;
          }
          this.errorMessage = '';
        } catch (err) {
          this.errorMessage = 'Invalid JSON file format.';
        }
      };
      reader.readAsText(file);
    },

async analyze() {
  if (!this.json1 || !this.json2) {
    this.errorMessage = 'Please upload both JSON files.';
    return;
  }

  this.loading = true;
  this.errorMessage = '';
  this.aiAnalysis = '';

  try {
    const response = await fetch('/api/tools/openai/analyze', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        json1: this.json1,
        json2: this.json2,
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.errorMessage || data.message || 'Request failed');
    }

    this.aiAnalysis =
      data.choices?.[0]?.message?.content || data.message || 'No analysis returned.';
  } catch (err) {
    console.error(err);
    this.errorMessage = err.message || 'Error during analysis. Please try again.';
  } finally {
    this.loading = false;
  }
}

  },
};
</script>

<style scoped>
input {
  display: block;
  margin-bottom: 8px;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
