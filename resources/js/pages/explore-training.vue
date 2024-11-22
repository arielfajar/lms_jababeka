<template>
  <v-container>
<v-row class="d-flex justify-center">
  <v-col cols="12" md="16">
    <v-container class="training-title-container">
      <v-row align="center" justify="space-between">
        <h1 class="training-title">
          <i class="bx bx-add-to-queue"></i>Explore Training
        </h1>
        <Breadcrumb :items="breadcrumbItems" class="breadcrumb-style" />
      </v-row>
    </v-container>
  </v-col>
</v-row>

    <!-- <v-row>
      <v-col cols="12" md="2">
        <v-select
          v-model="category"
          :items="categoryOptions"
          label="Filter by category"
          class="small-select"
        ></v-select>
      </v-col>
      <v-col cols="12" md="4">
        <v-select
          v-model="month"
          :items="monthOptions"
          label="Filter by month"
          class="small-select"
        ></v-select>
      </v-col>
    </v-row> -->
    <v-row>
      <v-col cols="12" md="4" v-for="item in paginatedItems" :key="item.explore_id">
        <v-card @click="goToContentExplore(item)">
          <v-img :src="getImageUrl(item.img)" height="200px"></v-img>
          <v-card-title>{{ item.judul }}</v-card-title>
        </v-card>
      </v-col>
    </v-row>
    <v-row class="d-flex justify-space-between align-center">
      <v-col cols="auto">
        <v-pagination
          v-model="currentPage"
          :length="pageCount"
          :total-visible="5"
        ></v-pagination>
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
import axios from 'axios';
import Breadcrumb from '@/views/dashboard/Breadcrumb.vue';

export default {
  components: {
    Breadcrumb, // Register the Breadcrumb component
  },
  data() {
    return {
       breadcrumbItems: [
        { text: 'Dashboard', link: '/dashboard' },
        { text: 'Explore Training', link: '/explore' },
      ],
      entriesPerPage: 6,
      currentPage: 1,
      category: 'Semua',
      month: 'Semua',
      entryOptions: [3, 6, 12, 18, 24],
      categoryOptions: ['Semua', 'Internal', 'External'],
      monthOptions: ['Semua', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
      items: [],
    };
  },
  computed: {
    filteredItems() {
      return this.items
        .filter(item => this.category === 'Semua' || item.category === this.category)
        .filter(item => this.month === 'Semua' || item.month === this.month);
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
        const response = await axios.get('/api/explore'); // Make sure to update this endpoint
        this.items = response.data;
        // await this.fetchItems();
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },
   goToContentExplore(item) {
  this.$router.push({
    path: 'explore/content',
    query: {
      title: item.judul,
      image: item.img,
      explore_id: item.explore_id // Include explore_id here
    }
  });
},
    getImageUrl(imagePath) {
      // Construct the full URL for the image
      return `/storage/${imagePath}`;
    },
  },
  created() {
    this.fetchItems();
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
.training-title i {
  margin-right: 10px; /* Berikan jarak 10px antara ikon dan teks */
}
</style>
