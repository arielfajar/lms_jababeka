<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const startDaftar = () => {
  router.push({ path: '/pengajuan' }) // Adjust the path as per your router configuration
}

const daftarInfo = {
  title: 'Daftar Training',
  subtitle: 'Mulai kelola daftar training yang ada',
  icon: 'bx-spreadsheet',
  buttonIcon: 'bx bxs-report',
}
</script>

<template>
  <v-card>
    <v-card-item>
      <v-card-title class="text-center">
        {{ daftarInfo.title }}
      </v-card-title>
      <v-card-subtitle class="text-center subtitle">
        {{ daftarInfo.subtitle }}
      </v-card-subtitle>
    </v-card-item>

    <v-card-text class="d-flex flex-column align-center">
      <!-- Center the icon with a large size -->
      <v-icon :icon="daftarInfo.icon" size="100" class="mb-4" />

      <!-- Button with icon -->
      <v-btn color="primary" @click="startDaftar">
        <v-icon :icon="daftarInfo.buttonIcon" size="20" class="me-2" />
        Kelola Daftar Training
      </v-btn>
    </v-card-text>
  </v-card>
</template>

<style scoped lang="scss">
.v-card-text {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.v-icon {
  font-size: 100px; /* Enlarges the icon */
}

.v-btn {
  margin-top: 20px;
}

.subtitle {
  white-space: normal; /* Allows the text to wrap to the next line */
  word-wrap: break-word; /* Ensures long words break properly */
  text-align: center; /* Keeps the text centered */
  max-width: 90%; /* Restricts the width so it doesn’t stretch too much on wide screens */
  margin: 0 auto; /* Centers the text horizontally */
}
</style>
