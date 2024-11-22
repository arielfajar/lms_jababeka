<template>
  <v-container>
    <v-row class="d-flex justify-center">
      <v-col cols="12" md="16">
        <v-container class="training-title-container">
          <v-row align="center" justify="space-between">
            <h1 class="training-title">
              <i class="bx bxs-graduation training-icon"></i>Lanjutkan Belajar
            </h1>
            <Breadcrumb :items="breadcrumbItems" class="breadcrumb-style" />
          </v-row>
        </v-container>
      </v-col>
    </v-row>

    <!-- No Data Message -->
    <v-row v-if="filteredItems.length === 0" class="justify-center">
      <v-col cols="12" class="text-center">
        <p>Anda Belum Mendaftar Training, Silahkan Cari Training yang tersedia</p>
        <v-btn @click="goToExplore" color="primary" class="d-flex align-center mx-auto">
          <v-icon left>bx bx-search</v-icon>
          Cari Training
        </v-btn>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="4" v-for="item in paginatedItems" :key="item.title">
        <v-card @click="goToContentExplore(item)">
          <v-img :src="getImageUrl(item.image)" height="200px"></v-img>
          <v-card-title>{{ item.title }}</v-card-title>
          <!-- Progress bar untuk konten di bawah title -->
          <div class="custom-progress">
            <div
              class="progress-bar"
              :style="{ width: getContentProgress(item) + '%' }"
              :data-progress="getContentProgress(item) < 25 ? 1 : getContentProgress(item) < 50 ? 2 : getContentProgress(item) < 80 ? 3 : 4"
            ></div>
            <span class="progress-text">{{ getContentProgress(item) }}%</span>
          </div>

        </v-card>
      </v-col>
    </v-row>

    <v-row class="d-flex justify-space-between align-center">
      <v-col cols="auto">
        <v-pagination v-model="currentPage" :length="pageCount" :total-visible="5"></v-pagination>
      </v-col>
      <v-col cols="auto">
        <v-select
          v-model="entriesPerPage"
          :items="entryOptions"
          label="Show entries"
          class="small-select"
        ></v-select>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Breadcrumb from '@/views/dashboard/Breadcrumb.vue';
import axios from 'axios';

export default {
  components: {
    Breadcrumb, // Register the Breadcrumb component
  },
  data() {
    return {
      breadcrumbItems: [
        { text: 'Dashboard', link: '/dashboard' },
        { text: 'My Training', link: '/training-mandiri' },
      ],
      entriesPerPage: 6,
      currentPage: 1,
      month: 'Semua',
      entryOptions: [3, 6, 12, 18, 24],
      monthOptions: ['Semua', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
      items: [], // Initialize as empty array
      contents: [], // Store content details
      progressData: [], // Store progress data
      overallProgress: 0, // Track overall progress percentage
    };
  },
  computed: {
    filteredItems() {
      return this.items.filter(item => this.month === 'Semua' || item.month === this.month);
    },
    paginatedItems() {
      const start = (this.currentPage - 1) * this.entriesPerPage;
      const end = start + this.entriesPerPage;
      return this.filteredItems.slice(start, end);
    },
    pageCount() {
      return Math.ceil(this.filteredItems.length / this.entriesPerPage);
    },
  },
  methods: {
    async fetchItems() {
      try {
        const userResponse = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        const user_id = userResponse.data.user_id;

        // Fetch the user's trainings
        const daftarResponse = await axios.get(`/api/daftar/${user_id}`);
        this.items = daftarResponse.data;

        // Fetch content for each explore_id
        const contentRequests = this.items.map(item => axios.get(`/api/content/${item.explore_id}`));
        const contentResponses = await Promise.all(contentRequests);
        this.contents = contentResponses.flatMap(response => response.data);

        // Fetch progress data
        const scoreResponse = await axios.get(`/api/nilai?user_id=${user_id}`);
        this.progressData = scoreResponse.data;


        // Calculate overall progress
        this.calculateOverallProgress();
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },
    calculateOverallProgress() {
      const totalContents = this.contents.length;
      const completedContents = this.contents.filter(content => {
        const progressItem = this.progressData.find(progress => Number(progress.content_id) === Number(content.content_id)); // Pastikan pencocokan tipe data
        if (!progressItem) {
          console.log(`Progress item not found for Content ID: ${content.content_id}`); // Log tambahan
        }
        return progressItem && Number(progressItem.progress) === 1; // Pastikan progress-nya 1 dan diubah ke number
      }).length;

      this.overallProgress = totalContents > 0 ? Math.round((completedContents / totalContents) * 100) : 0;
    },
getContentProgress(item) {
  const contents = this.contents.filter(content => content.explore_id === item.explore_id);
  const completedCount = contents.reduce((count, content) => {
    const progressItem = this.progressData.find(progress => Number(progress.content_id) === Number(content.content_id));
    return count + (progressItem && Number(progressItem.progress) === 1 ? 1 : 0);
  }, 0);

  const progressPercentage = contents.length > 0 ? Math.round((completedCount / contents.length) * 100) : 0;

  return progressPercentage;
},

    goToContentExplore(item) {
      // Store data in localStorage
      localStorage.setItem('contentTitle', item.title);
      localStorage.setItem('contentImage', this.getImageUrl(item.image));
      localStorage.setItem('exploreId', item.explore_id);
      this.$router.push('training-mandiri/content');
    },
    getImageUrl(imagePath) {
      return `/storage/${imagePath}`;
    },
  },
  mounted() {
    this.fetchItems(); // Fetch items when the component is mounted
  },
};
</script>

<style scoped>
.v-card {
  margin-bottom: 20px;
  cursor: pointer;
  border: 1px solid rgba(0, 0, 0, 0.2); /* warna border abu-abu */
  border-radius: 20px; /* opsional: untuk membuat border sedikit melengkung */
}

.small-select {
  max-width: 200px;
}
.training-icon {
  font-size: 36px; /* Sesuaikan ukuran ikon dengan teks */
}
.custom-progress {
  background: #e0e0e0;
  border-radius: 5px;
  height: 15px;
  position: relative;
  overflow: hidden;
}

.progress-bar {
  background: #1976d2; /* Ganti dengan warna yang diinginkan */
  height: 100%;
  transition: width 0.3s;
}
.progress-text {
  position: absolute;
  top: 0; /* Sesuaikan jika perlu */
  left: 50%;
  transform: translateX(-50%); /* Memusatkan teks */
  color: white; /* Warna teks */
  line-height: 15px; /* Sesuaikan dengan tinggi progress bar */
  font-weight: bold; /* Jika Anda ingin teks lebih tebal */
}
.progress-bar[data-progress="0"] {
  background: #e0e0e0;
}

.progress-bar[data-progress="1"] {
  background: #ff0000; /* Merah jika progress 1-25% */
}

.progress-bar[data-progress="2"] {
  background: #ff9800; /* Oranye jika progress 26-50% */
}

.progress-bar[data-progress="3"] {
  background: #ffeb3b; /* Kuning jika progress 51-80% */
}

.progress-bar[data-progress="4"] {
  background: #1976d2; /* Biru jika progress 81-100% */
}

</style>
