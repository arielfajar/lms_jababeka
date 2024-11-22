<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue';
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue';
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue'
import { useRouter } from 'vue-router'; 

const router = useRouter();
const user = ref(null);  // Store user data
const roleId = ref(null); // Store role_id

// Function to get user data from the API
const getUserData = async () => {
    try {
        const response = await axios.get('/api/user', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}` // Use the correct token
            }
        });
        user.value = response.data;
        roleId.value = Number(user.value.role_id);  // Convert role_id to a number

    } catch (error) {
        console.error('Failed to fetch user data:', error);
    }
};

// Logout function with confirmation
const handleLogout = () => {
    Swal.fire({
        title: 'Konfirmasi Logout',
        text: 'Apakah Anda yakin ingin logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Logout!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem('token');  // Remove the token from localStorage
            router.push('/login');  // Redirect to the login page
        }
    });
};

// Fetch user data when the component is mounted
onMounted(() => {
    getUserData();
});

</script>

<template>
  <!-- Menu for role_id = 2 (User) -->
    <VerticalNavLink
    v-if="roleId === 3"
    :item="{
      title: 'Dashboard',
      to: '/hod/dashboard',
      icon: 'bx-home-smile',
    }"
  />

  <VerticalNavLink
    v-if="roleId === 2"
    :item="{
      title: 'Dashboard',
      to: '/dashboard',
      icon: 'bx-home-smile',
    }"
  />

  <!-- Menu for role_id = 1 (Admin) -->
  <VerticalNavLink
    v-if="roleId === 1"
    :item="{
      title: 'Dashboard',
      to: '/admin/dashboard',
      icon: 'bx-home-smile',
    }"
  />
    <VerticalNavSectionTitle
    v-if="roleId === 1"
    :item="{
      heading: 'kelola Training',
    }"
  />
  <VerticalNavLink
    v-if="roleId === 1"
    :item="{
      title: 'Explore Training',
      icon: 'bx-file-find',
      to: '/admin/explore',
    }"
  />
  <VerticalNavLink
    v-if="roleId === 1"
    :item="{
      title: 'Konten Training',
      icon: 'bx-book-content',
      to: '/admin/content',
    }"
  />
  <VerticalNavSectionTitle
    v-if="roleId === 1"
    :item="{
      heading: 'data & approve',
    }"
  />
  <VerticalNavLink
    v-if="roleId === 1"
    :item="{
      title: 'Daftar Training',
      icon: 'bx-bxs-report',
      to: '/admin/datadaftar',
    }"
  />
  <VerticalNavLink
    v-if="roleId === 1"
    :item="{
      title: 'Pengajuan Training',
      icon: 'bx-bxs-customize',
      to: '/admin/datapengajuan',
    }"
  />
   <VerticalNavSectionTitle
    v-if="roleId === 1"
    :item="{
      heading: 'cetak',
    }"
  />
  <VerticalNavGroup 
  v-if="roleId === 1" 
  :item="{ 
    title: 'Laporan',
    icon: 'bx-printer',
     }">
    <VerticalNavLink
      :item="{
        title: 'Daftar Training',
        to: '/admin/listtraining',
      }"
    />
    
    <VerticalNavLink
      :item="{
        title: 'Pengajuan Training',
        to: '/admin/reportpengajuan',
      }"
    />

    <VerticalNavLink
      :item="{
        title: 'Hasil Training',
        to: '/admin/hasiltraining',
      }"
    />
  </VerticalNavGroup>

   <VerticalNavSectionTitle
    v-if="roleId === 1"
    :item="{
      heading: 'other',
    }"
  />

   <VerticalNavGroup 
  v-if="roleId === 1" 
  :item="{ 
    title: 'Update',
    icon: 'bx-cog',
     }">
    <VerticalNavLink
      :item="{
        title: 'Karyawan',
        to: '/admin/karyawan',
      }"
    />
    
    <VerticalNavLink
      :item="{
        title: 'Department',
        to: '/admin/department',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Ttd Sertifikat',
        to: '/admin/ttd/show',
      }"
    />

  </VerticalNavGroup>

  <!-- User-Specific Links -->
  <VerticalNavSectionTitle
    v-if="roleId === 2"
    :item="{
      heading: 'Training Saya',
    }"
  />

  <VerticalNavLink
    v-if="roleId === 2"
    :item="{
      title: 'Explore Training',
      icon: 'bx-file-find',
      to: '/explore',
    }"
  />

  <VerticalNavLink
    v-if="roleId === 2"
    :item="{
      title: 'My Training',
      icon: 'bx-user',
      to: '/training-mandiri',
    }"
  />

  <VerticalNavLink
    v-if="roleId === 2"
    :item="{
      title: 'Pengajuan Training',
      icon: 'bx-bxs-customize',
      to: '/pengajuan',
    }"
  />

  <!-- Admin-Specific Links -->
  <VerticalNavSectionTitle
    v-if="roleId === 2"
    :item="{
      heading: 'Pencapaian',
    }"
  />

  <VerticalNavLink
    v-if="roleId === 2"
    :item="{
      title: 'Sertifikat',
      icon: 'bx-award',
      to: '/sertifikat',
    }"
  />

  <VerticalNavSectionTitle
   v-if="roleId === 2"
    :item="{
      heading: '',
    }"
  />

    <!-- User-Specific Links -->
  <VerticalNavSectionTitle
    v-if="roleId === 3"
    :item="{
      heading: 'Data & approve',
    }"
  />


  <VerticalNavLink
    v-if="roleId === 3"
    :item="{
      title: 'Daftar Training',
      icon: 'bxs-carousel',
      to: '/hod/datadaftar',
    }"
  />

  <VerticalNavLink
    v-if="roleId === 3"
    :item="{
      title: 'Pengajuan Training',
      icon: 'bx-bxs-customize',
      to: '/hod/datapengajuan',
    }"
  />

  <VerticalNavSectionTitle
   v-if="roleId === 3"
    :item="{
      heading: '',
    }"
  />
  <!-- Log-out link -->
  <VerticalNavLink
    :item="{
      title: 'Log-out',
      icon: 'bx-log-out',
    }"
    @click="handleLogout"
  />
</template>

