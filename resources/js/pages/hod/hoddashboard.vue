<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import welcomeTab from '@/views/hod/welcomeTab.vue';
import datapengajuanTab from '@/views/hod/datapengajuanTab.vue';
import datadaftarTab from '@/views/hod/datadaftarTab.vue';
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


    <VCol
      cols="12"
      md="6"
      sm="6"
    >
      <datadaftarTab />
    </VCol>

    <!-- 👉 Tabs chart -->
    <VCol
      cols="12"
      md="6"
      sm="6"
    >
      <datapengajuanTab />
    </VCol>


  </VRow>
</template>
