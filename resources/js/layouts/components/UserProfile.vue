<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useTheme } from 'vuetify'
import axios from 'axios'
import Swal from 'sweetalert2'
import avatar1 from '@images/avatars/avatar-1.png'
import illustrationJohnDark from '@images/cards/illustration-john-dark.png'
import illustrationJohnLight from '@images/cards/illustration-john-light.png'

const user = ref(null)
const accountDataLocal = ref({ foto: null }) // Initialize accountDataLocal
const { global } = useTheme()
const illustrationJohn = computed(() => global.name.value === 'dark' ? illustrationJohnDark : illustrationJohnLight)

const router = useRouter()

// Mengambil data pengguna
const getUserData = async () => {
    try {
        const response = await axios.get('/api/user', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}` // Ganti 'token' dengan nama yang sesuai jika berbeda
            }
        })
        user.value = response.data
        accountDataLocal.value = response.data // Assuming you want to store user data here as well
    } catch (error) {
        console.error('Gagal mengambil data pengguna:', error)
    }
}

const logout = async () => {
    // Menampilkan konfirmasi SweetAlert
    const result = await Swal.fire({
        title: 'Anda yakin ingin melakukan logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, logout!',
        cancelButtonText: 'Tidak'
    })

    // Jika pengguna mengkonfirmasi logout
    if (result.isConfirmed) {
        try {
            // Panggil API untuk logout jika ada endpointnya
            await axios.post('/api/logout', {}, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
            })
        } catch (error) {
            console.error('Logout gagal:', error)
        } finally {
            // Hapus token dan redirect ke halaman login
            localStorage.removeItem('token')
            router.push('/login')
        }
    }
}

onMounted(() => {
    getUserData()
})
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VImg :src="accountDataLocal.foto ? `/storage/${accountDataLocal.foto}` : avatar1" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="accountDataLocal.foto ? `/storage/${accountDataLocal.foto}` : avatar1" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ user ? user.nama_lengkap : 'Pengguna' }}
            </VListItemTitle>
            <VListItemSubtitle>{{ user ? user.role : 'Role' }}</VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <VListItem :to="{ path: '/account-settings' }" link>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-user"
                size="22"
              />
            </template>

            <VListItemTitle>Profile</VListItemTitle>
          </VListItem>

          <!-- 👉 FAQ -->
          <VListItem link>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-help-circle"
                size="22"
              />
            </template>

            <VListItemTitle>FAQ</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem @click="logout"> <!-- Panggil fungsi logout saat di klik -->
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-log-out"
                size="22"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>

<style lang="scss" scoped>
.john-illustration {
  inset-block-end: -0.125rem;
  inset-inline-end: 3.5rem;
}
</style>
