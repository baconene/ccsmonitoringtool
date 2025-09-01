<script setup>
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";

// Props
const props = defineProps({
  products: Array,
});

const search = ref("");
const filtered = computed(() =>
  props.products.filter((p) =>
    p.product_name.toLowerCase().includes(search.value.toLowerCase())
  )
);

// Add form
const addForm = useForm({
  product_name: "",
  sku: "",
  stock: 0,
  price: 0,
});

const addProduct = () => {
  addForm.post(route("inventory.store"), {
    onSuccess: () => addForm.reset(),
  });
};
// Barcode scanner
const requestCamera = async () => {
  try {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      alert("❌ Camera API not supported in this browser or connection must be HTTPS/localhost.");
      return;
    }

    await navigator.mediaDevices.getUserMedia({ video: true });
    console.log("✅ Camera permission granted");
    startScanner();
  } catch (err) {
    alert("❌ Camera access denied: " + err.message);
  }
};


const scanning = ref(false);
let html5QrCode = null;

const startScanner = async () => {
  try {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      alert("Camera API not available. Try HTTPS or a supported browser.");
      return;
    }

    await navigator.mediaDevices.getUserMedia({ video: true });

    scanning.value = true;

    if (!html5QrCode) {
      const { Html5Qrcode } = await import("html5-qrcode");
      html5QrCode = new Html5Qrcode("reader");
    }

    html5QrCode.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: { width: 250, height: 100 } },
      (decodedText) => {
        addForm.sku = decodedText;
        stopScanner();
      },
      (error) => console.warn("QR scan error:", error)
    );
  } catch (err) {
    alert("Camera access was denied: " + err.message);
    console.error(err);
  }
};


const stopScanner = () => {
  if (html5QrCode) {
    html5QrCode.stop().then(() => {
      scanning.value = false;
    });
  }
};
// Edit form
const showEditModal = ref(false);
const editForm = useForm({
  id: null,
  product_name: "",
  sku: "",
  stock: 0,
  price: 0,
});

const openEdit = (p) => {
  editForm.id = p.id;
  editForm.product_name = p.product_name;
  editForm.sku = p.sku;
  editForm.stock = p.stock;
  editForm.price = p.price;
  showEditModal.value = true;
};

const updateProduct = () => {
  editForm.put(route("inventory.update", editForm.id), {
    onSuccess: () => {
      showEditModal.value = false;
    },
  });
};

// Delete
const deleteProduct = (id) => {
  if (confirm("Are you sure you want to delete this product?")) {
    router.delete(route("inventory.destroy", id));
  }
};
</script>

<template>
  <AppLayout>
    <Head title="Inventory" />

    <div class="p-4 md:p-6">
      <h1 class="text-2xl font-bold mb-4">📦 Inventory</h1>

      <!-- Add Product -->
      <div class="mb-6 p-4 border rounded bg-gray-50">
        <h2 class="text-lg font-semibold mb-3">Add Product</h2>

        <form @submit.prevent="addProduct" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input v-model="addForm.product_name" type="text" placeholder="Product Name" class="border p-2 rounded w-full" />
          
          <div class="flex items-center gap-2">
            <input v-model="addForm.sku" type="text" placeholder="SKU" class="border p-2 rounded w-full" />
            <button
              type="button"
              @click="requestCamera"
              class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm"
            >
              Scan
            </button>
          </div>

          <input v-model="addForm.stock" type="number" placeholder="Stock" class="border p-2 rounded w-full" />
          <input v-model="addForm.price" type="number" step="0.01" placeholder="Price" class="border p-2 rounded w-full" />

          <button
            type="submit"
            class="col-span-1 md:col-span-2 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 w-full"
            :disabled="addForm.processing"
          >
            Add Product
          </button>
        </form>
      </div>

      <!-- Barcode Scanner Modal -->
      <div v-if="scanning" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
        <div class="bg-white p-4 rounded-lg shadow w-11/12 md:w-1/2">
          <h3 class="text-lg font-semibold mb-2">Scan Barcode</h3>
          <div id="reader" class="w-full h-64 border rounded"></div>
          <div class="mt-4 flex justify-end">
            <button @click="stopScanner" class="px-4 py-2 bg-red-600 text-white rounded">Cancel</button>
          </div>
        </div>
      </div>

      <!-- Search -->
      <input v-model="search" type="text" placeholder="Search products..." class="border rounded p-2 w-full mb-4" />

      <!-- Products Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full border text-sm md:text-base">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-2 py-2 border">ID</th>
              <th class="px-2 py-2 border">Product</th>
              <th class="px-2 py-2 border">SKU</th>
              <th class="px-2 py-2 border">Stock</th>
              <th class="px-2 py-2 border">Price</th>
              <th class="px-2 py-2 border">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in filtered" :key="p.id">
              <td class="border px-2 py-1">{{ p.id }}</td>
              <td class="border px-2 py-1">{{ p.product_name }}</td>
              <td class="border px-2 py-1">{{ p.sku }}</td>
              <td class="border px-2 py-1">{{ p.stock }}</td>
              <td class="border px-2 py-1">₱{{ p.price }}</td>
              <td class="border px-2 py-1 space-x-1">
                <button @click="openEdit(p)" class="px-2 py-1 bg-yellow-500 text-white rounded text-xs">Edit</button>
                <button @click="deleteProduct(p.id)" class="px-2 py-1 bg-red-600 text-white rounded text-xs">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Edit Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
      <div class="bg-white rounded shadow-lg p-6 w-11/12 md:w-1/3">
        <h2 class="text-lg font-bold mb-4">Edit Product</h2>
        <form @submit.prevent="updateProduct" class="grid gap-4">
          <input v-model="editForm.product_name" type="text" placeholder="Product Name" class="border p-2 rounded" />
          <input v-model="editForm.sku" type="text" placeholder="SKU" class="border p-2 rounded" />
          <input v-model="editForm.stock" type="number" placeholder="Stock" class="border p-2 rounded" />
          <input v-model="editForm.price" type="number" step="0.01" placeholder="Price" class="border p-2 rounded" />

          <div class="flex justify-end space-x-2">
            <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-400 text-white rounded">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded" :disabled="editForm.processing">
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
