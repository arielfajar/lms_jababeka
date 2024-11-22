<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios'; // Importing Axios
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue';
import logo from '@images/logo.png'; // Mengimpor logo PNG
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?url';
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?url';
import Swal from 'sweetalert2'; // Import SweetAlert2

const form = ref({
  email: '',
  password: '',
  remember: false,
});

const isPasswordVisible = ref(false);
const isLoading = ref(false); // Menambahkan loading state
const router = useRouter();



const user = ref(null); // Definisikan `user` sebagai ref atau reactive state

// Fungsi untuk mengambil data pengguna berdasarkan token yang ada di localStorage
const getUserData = async () => {
  try {
    const response = await axios.get('/api/user', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Gunakan token dari localStorage
      }
    });
    user.value = response.data; // Simpan data pengguna
    console.log('Data pengguna:', user.value);

    // Cek role_id dan arahkan halaman berdasarkan role
    if (user.value.role_id === '1') {
      router.push('/admin/dashboard'); // Arahkan ke admin dashboard jika role_id = 1
    } else if (user.value.role_id === '2') {
      router.push('/dashboard'); // Arahkan ke dashboard jika role_id = 2
    } else if (user.value.role_id === '3') {
      router.push('/hod/dashboard');
    } else {
      console.error('Unknown role_id:', user.value.role_id); // Jika role_id tidak dikenali
    }
  } catch (error) {
    console.error('Gagal mengambil data pengguna:', error);
    localStorage.removeItem('token'); // Jika gagal, hapus token dari localStorage
    router.push('/login'); // Redirect ke halaman login jika terjadi error
  }
};

// Cek apakah token sudah ada di localStorage, dan ambil data pengguna jika ada
onMounted(() => {
  const token = localStorage.getItem('token');
  if (token) {
    getUserData(); // Panggil fungsi untuk mengambil data pengguna berdasarkan token
  } else {
    router.push('/login'); // Redirect ke halaman login jika tidak ada token
  }
});

// Setelah login berhasil, panggil getUserData
const handleLogin = async () => {
  isLoading.value = true;
    try {
        const response = await axios.post('/api/login', {
            email: form.value.email,
            password: form.value.password,
            remember: form.value.remember,
        });

        localStorage.setItem('token', response.data.token); // Simpan token di localStorage

        Swal.fire({
            icon: 'success',
            title: `Hi, ${response.data.nama_lengkap}!`,
            text: 'Welcome back!',
        });

        // Ambil data pengguna dan lakukan pengalihan sesuai role_id
        await getUserData(); // Panggil fungsi untuk mengambil data pengguna

    } catch (error) {
        console.error('Login failed:', error.response.data.message || error.message);

        Swal.fire({
            icon: 'error',
            title: 'Login failed',
            text: error.response.data.error || 'An unknown error occurred. Please try again.',
        });
        } finally {
        isLoading.value = false;
    }
};


</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
      <VImg
        :src="authV1TopShape"
        class="text-primary auth-v1-top-shape d-none d-sm-block"
      />

      <VImg
        :src="authV1BottomShape"
        class="text-primary auth-v1-bottom-shape d-none d-sm-block"
      />

      <VCard
        class="auth-card"
        max-width="460"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-0'"
      >
        <VCardItem class="justify-center">
          <RouterLink to="/" class="app-logo">
            <img :src="logo" alt="Logo" class="logo-small" />
          </RouterLink>
        </VCardItem>

        <VCardText>
          <h4 class="text-h4 mb-1 text-bold text-center">
            LMS Training Jababeka
          </h4>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="handleLogin">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="form.email"
                  autofocus
                  label="Email or Username"
                  type="email"
                  placeholder="ariel@email.com"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="form.password"
                  label="Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <div class="d-flex align-center justify-space-between flex-wrap my-6">
                  <VCheckbox
                    v-model="form.remember"
                    label="Remember me"
                  />
                  <a class="text-primary" href="javascript:void(0)">
                    Forgot Password?
                  </a>
                </div>

                <VBtn block type="submit" :loading="isLoading"> <!-- Tambahkan loading prop -->
                  Login
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<style scoped lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
.auth-wrapper {
  background-image: url('https://www.jababeka.com/wp-content/uploads/2019/01/build100.jpg');
  background-size: cover; /* Memastikan gambar memenuhi seluruh elemen */
  background-position: center; /* Memposisikan gambar di tengah */
  background-repeat: no-repeat; /* Mencegah gambar diulang */
  height: 100vh; /* Mengatur tinggi penuh untuk latar belakang */
}
.logo-small {
  width: 120px; /* Ganti ukuran ini sesuai keinginan */
  height: auto;
}
.text-bold {
  font-weight: bold; /* Membuat teks lebih tebal */
}
</style>
