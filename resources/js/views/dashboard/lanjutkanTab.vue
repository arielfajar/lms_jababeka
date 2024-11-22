<template>
  <v-card :style="{ borderRadius: '30px' }" class="pa-4">
    <v-container class="container-bg">
      <!-- Title Section -->
      <v-row>
        <v-col cols="12">
          <h3 class="text-h5 mb-4 font-weight-bold">Lanjutkan Training</h3>
        </v-col>
      </v-row>

      <!-- No Data Message -->
      <v-row v-if="filteredItems.length === 0" class="justify-center">
        <v-col cols="12" class="text-center">
          <p>Belum ada training yang bisa dilanjutkan, Silahkan Cari Training yang tersedia</p>
          <v-btn @click="goToExplore" color="primary" class="d-flex align-center mx-auto">
            <v-icon left>bx bx-search</v-icon>
            Cari Training
          </v-btn>
        </v-col>
      </v-row>

      <!-- Training Cards Section -->
      <v-row v-else>
        <v-col cols="12" md="4" v-for="item in paginatedItems" :key="item.explore_id">
          <v-card @click="goToContentExplore(item)" class="hover-card" :style="{ borderRadius: '30px',  border: '1px solid rgba(0, 0, 0, 0.2)', backgroundColor: 'rgba(128, 128, 128, 0.1)' }">
            <v-img :src="getImageUrl(item.image)" height="200px"></v-img>
            <v-card-title class="d-flex justify-center">{{ item.title }}</v-card-title>
            <!-- Progress bar for content below title -->
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

      <!-- Pagination Section -->
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
  </v-card>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      entriesPerPage: 6,
      currentPage: 1,
      month: 'Semua',
      entryOptions: [3, 6, 12, 18, 24],
      monthOptions: ['Semua', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
      items: [],
      contents: [],
      progressData: [],
    };
  },
  computed: {
  filteredItems() {
    return this.items.filter(item => {
      if (this.month !== 'Semua' && item.month !== this.month) {
        return false;
      }
      
      const progress = this.getContentProgress(item);
      return progress < 100; // Only show items with progress less than 100%
    });
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

        const daftarResponse = await axios.get(`/api/daftar/${user_id}`);
        this.items = daftarResponse.data;

        const contentRequests = this.items.map(item => axios.get(`/api/content/${item.explore_id}`));
        const contentResponses = await Promise.all(contentRequests);
        this.contents = contentResponses.flatMap(response => response.data);

        const scoreResponse = await axios.get(`/api/nilai?user_id=${user_id}`);
        this.progressData = scoreResponse.data;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
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
      localStorage.setItem('contentTitle', item.title);
      localStorage.setItem('contentImage', this.getImageUrl(item.image));
      localStorage.setItem('exploreId', item.explore_id);
      this.$router.push('training-mandiri/content');
    },
    goToExplore() {
      this.$router.push('/explore');
    },
    getImageUrl(imagePath) {
      return `/storage/${imagePath}`;
    },
  },
  mounted() {
    this.fetchItems();
  },
};
</script>

<style scoped>
:root {
  --light-bg: #f7f7f7;
  --dark-bg: #424242;
}

.container-bg {
  background-color: var(--light-bg);
  border-radius: 8px;
}

.v-card {
  margin-bottom: 20px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.v-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
.v-card-title {
  font-weight: bold;
  text-align: center;
}
.small-select {
  max-width: 200px;
}

.custom-progress {
  background: #e0e0e0;
  border-radius: 5px;
  height: 15px;
  position: relative;
  overflow: hidden;
}

.progress-bar {
  background: #1976d2;
  height: 100%;
  transition: width 0.3s;
}
.progress-text {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  line-height: 15px;
  font-weight: bold;
}
.progress-bar[data-progress="0"] {
  background: #e0e0e0;
}
.progress-bar[data-progress="1"] {
  background: #ff0000;
}
.progress-bar[data-progress="2"] {
  background: #ff9800;
}
.progress-bar[data-progress="3"] {
  background: #ffeb3b;
}
.progress-bar[data-progress="4"] {
  background: #1976d2;
}
</style>
