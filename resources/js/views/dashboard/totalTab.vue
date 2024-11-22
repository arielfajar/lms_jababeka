<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

const dataList = ref([
  {
    title: 'Total Daftar',
    value: 0,
    icon: 'bx bx-list-check',
    color: 'primary',
  },
  {
    title: 'Total Pengajuan',
    value: 0,
    icon: 'bx bx-clipboard',
    color: 'success',
  },
  {
    title: 'Sertifikat',
    value: 0,
    icon: 'bx bx-award',
    color: 'error',
  },
])

// Function to fetch the total counts based on user_id
async function fetchTotals(userId) {
  try {
    // Fetch daftar count
    const daftarResponse = await axios.get(`/api/daftar/${userId}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    dataList.value[0].value = daftarResponse.data.length || 0

    // Fetch pengajuan count filtered by user_id
    const pengajuanResponse = await axios.get(`/api/pengajuan`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
      params: { user_id: userId },
    })
    const userPengajuan = pengajuanResponse.data.filter(item => item.user_id === userId) // Filter for matching user_id
    dataList.value[1].value = userPengajuan.length || 0

   // Fetch sertifikat count, filtered by user_id

const sertifikatResponse = await axios.get(`/api/sertifikat/count`, {
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`,
  },
  params: { user_id: userId },
})
dataList.value[2].value = sertifikatResponse.data.count || 0

  } catch (error) {
    console.error("Error fetching totals:", error)
  }
}


// Retrieve user_id from the user data API
async function getUserId() {
  try {
    const response = await axios.get('/api/user', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    const userId = response.data.user_id  // Confirm the correct key here
    if (userId) {
      await fetchTotals(userId)
    } else {
      console.error("User ID not found in response:", response.data)
    }
  } catch (error) {
    console.error("Error fetching user ID:", error)
  }
}

// Fetch data on component mount
onMounted(getUserId)
</script>

<template>
  <v-card :style="{ borderRadius: '30px' }">
    <v-card-text>
      <v-list class="summary-list">
        <v-list-item
          v-for="item in dataList"
          :key="item.title"
        >
          <template #prepend>
            <v-avatar
              rounded
              variant="tonal"
              :color="item.color"
              size="40"
            >
              <v-icon :icon="item.icon" size="30" />
            </v-avatar>
          </template>

          <v-list-item-title>
            {{ item.title }}
          </v-list-item-title>
          
          <template #append>
            <v-list-item-action>
              <span class="me-2">{{ item.value }}</span>
            </v-list-item-action>
          </template>
        </v-list-item>
      </v-list>
    </v-card-text>
  </v-card>
</template>

<style lang="scss" scoped>
.summary-list {
  --v-card-list-gap: 1.5rem;
}

.v-avatar {
  display: flex;
  justify-content: center;
  align-items: center;
}

.v-icon {
  font-size: 30px;
}
</style>
