<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import welcomeTab from '@/views/admin/welcomeTab.vue';
import exploreTab from '@/views/admin/exploreTab.vue';
import pengajuanTab from '@/views/admin/pengajuanTab.vue';
import daftarTab from '@/views/admin/daftarTab.vue';
import { useRouter } from 'vue-router';

const userData = ref(null); // Ref untuk menyimpan data pengguna
const router = useRouter(); // Menggunakan router untuk navigasi

const getUserData = async () => {
    const token = localStorage.getItem('token'); // Ambil token dari localStorage
    try {
        const response = await axios.get('/api/user', {
            headers: {
                Authorization: `Bearer ${token}`, // Sertakan token di header
            },
        });
        userData.value = response.data; // Menyimpan data pengguna
    } catch (error) {
        console.error('Gagal mengambil data user:', error.response ? error.response.data : error.message);
    }
};

onMounted(() => {
    getUserData(); // Ambil data pengguna saat komponen dimount
});
</script>

<template>
  <VRow>
    <!-- 👉 Congratulations -->
    <VCol
      cols="12"
      md="12"
    >
      <welcomeTab />
    </VCol>

    <!-- 👉 Order Statistics -->
    <VCol
      cols="12"
      md="4"
      sm="6"
    >
      <exploreTab />
    </VCol>

    <!-- 👉 Tabs chart -->
    <VCol
      cols="12"
      md="4"
      sm="6"
    >
      <pengajuanTab />
    </VCol>

    <VCol
      cols="12"
      md="4"
      sm="6"
    >
      <daftarTab />
    </VCol>

    <!-- 👉 Transactions -->
    <VCol
      cols="12"
      md="4"
      sm="6"
    >
      <totalTab />
    </VCol>

    <!-- 👉 Profit Report (moved below Order Statistics) -->
    <VCol
      cols="12"
      md="12"
      sm="12"
    >
      <lanjutkanTab />
    </VCol>

  </VRow>
</template>
