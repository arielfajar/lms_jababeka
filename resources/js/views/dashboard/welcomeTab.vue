<script setup>
import { ref, onMounted, computed } from 'vue'
import { useTheme } from 'vuetify'
import axios from 'axios'
import illustrationJohnDark from '@images/cards/illustration-john-dark.png'
import illustrationJohnLight from '@images/cards/illustration-john-light.png'
import LogoLMS from '@images/cards/logo_lms.png'

const user = ref(null)
const { global } = useTheme()
const illustrationJohn = computed(() => global.name.value === 'dark' ? illustrationJohnDark : illustrationJohnLight)

// Mengambil data pengguna
const getUserData = async () => {
    try {
        const response = await axios.get('/api/user', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}` // Ganti 'token' dengan nama yang sesuai jika berbeda
            }
        })
        user.value = response.data
    } catch (error) {
        console.error('Gagal mengambil data pengguna:', error)
    }
}

onMounted(() => {
    getUserData()
})
</script>

<template>
  <VCard class="text-center text-sm-start" :style="{ borderRadius: '30px' }">
    <VRow no-gutters>
      <VCol
        cols="12"
        sm="8"
        order="2"
        order-sm="1"
      >
        <VCardItem class="pb-3">
          <VCardTitle class="text-primary font-weight-bold display-1 mb-1">
            <strong>Hi, {{ user ? user.nama_lengkap : 'Pengguna' }}! </strong>
          </VCardTitle>
        </VCardItem>

        <VCardText>
          Selamat datang di website LMS Training. Kami siap membantu Anda mempelajari hal baru!
          <br>
          <br>
        </VCardText>
      </VCol>

      <VCol
        cols="12"
        sm="4"
        order="1"
        order-sm="2"
        class="text-center"
      >
        <img
          :src="LogoLMS"
          :height="$vuetify.display.xs ? '250' : '292'"
          :class="$vuetify.display.xs ? 'mt-6 mb-n2' : 'position-absolute'"
          class="john-illustration flip-in-rtl"
        >
      </VCol>
    </VRow>
  </VCard>
</template>

<style lang="scss" scoped>
.john-illustration {
  inset-block-end: -6rem;
  inset-inline-end: 3.5rem;
}
</style>
