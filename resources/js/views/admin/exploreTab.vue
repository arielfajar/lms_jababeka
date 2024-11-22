<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const startTraining = () => {
  router.push({ path: '/explore' }) // Ensure this path is correct
}

const trainingInfo = {
  title: 'Explore Training',
  subtitle: 'Mulai Kelola Training untuk menambahkan konten terbaru',
  icon: 'bx bxs-carousel',
  buttonIcon: 'bx-file-find',
}
</script>

<template>
  <v-card>
    <v-card-item>
      <v-card-title class="text-center">
        {{ trainingInfo.title }}
      </v-card-title>
      <v-card-subtitle class="text-center subtitle">
        {{ trainingInfo.subtitle }}
      </v-card-subtitle>
    </v-card-item>

    <v-card-text class="d-flex flex-column align-center">
      <v-icon :icon="trainingInfo.icon" size="100" class="mb-4" />
      <v-btn color="primary" @click="startTraining">
       <v-icon :icon="trainingInfo.buttonIcon" size="20" class="me-2" />
        Kelola Training
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
