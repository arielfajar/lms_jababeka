<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const startPengajuan = () => {
  router.push({ path: '/pengajuan' }) // Adjust the path as per your router configuration
}

const pengajuanInfo = {
  title: 'Pengajuan Training',
  subtitle: 'Anda ingin mengajukan training diluar Jababeka?',
  icon: 'bx bxs-calendar-plus',
  buttonIcon: 'bx bxs-customize',
}
</script>

<template>
  <v-card :style="{ borderRadius: '30px' }">
    <v-card-item>
      <v-card-title class="text-center font-weight-bold">
        {{ pengajuanInfo.title }}
      </v-card-title>
      <v-card-subtitle class="text-center subtitle" style="font-size: 14px;">
        {{ pengajuanInfo.subtitle }}
      </v-card-subtitle>
    </v-card-item>

    <v-card-text class="d-flex flex-column align-center">
      <!-- Center the icon with a large size -->
      <v-icon :icon="pengajuanInfo.icon" size="100" class="mb-4" />

      <!-- Button with icon -->
      <v-btn color="primary" @click="startPengajuan">
        <v-icon :icon="pengajuanInfo.buttonIcon" size="20" class="me-2" />
        Ajukan Training
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
