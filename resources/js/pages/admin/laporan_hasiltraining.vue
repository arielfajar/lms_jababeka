<template>
  <div>
    <v-btn color="primary" @click="exportToExcel">
      <i class="bx bx-download" style="margin-right: 8px;"></i> Export to Excel
    </v-btn>

    <!-- Loading Spinner -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
    </div>

    <!-- Data Table (only shows when loading is false) -->
    <v-table v-else>
      <thead>
        <tr>
          <th @click="sort('index')" style="cursor: pointer; text-align: center;">No</th>
          <th @click="sort('nama_lengkap')" style="cursor: pointer; text-align: center;">Nama Lengkap</th>
          <th @click="sort('title')" style="cursor: pointer; text-align: center;">Judul</th>
          <th style="text-align: center;">Nilai Rata-Rata</th>
          <th style="text-align: center;">Progress</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in paginatedItems" :key="item.daftar_id">
          <td>{{ (currentPage - 1) * entriesPerPage + index + 1 }}</td>
          <td>{{ item.nama_lengkap }}</td>
          <td>{{ item.title }}</td>
          <td style="text-align: center;">{{ item.averageScore.toFixed(2) }}</td>
          <td>
            <div class="custom-progress">
              <div 
                class="progress-bar" 
                :style="getProgress(item)">
              </div>
              <span class="progress-text">{{ item.progressPercentage }}%</span>
            </div>
          </td>
        </tr>
      </tbody>
    </v-table>
  </div>
</template>

<script>
import axios from 'axios';
import * as XLSX from 'xlsx';

export default {
  data() {
    return {
      items: [],
      paginatedItems: [],
      currentPage: 1,
      entriesPerPage: 10,
      sortKey: '',
      sortOrder: 1,
      loading: true, // Loading state
    };
  },
  methods: {
  async fetchItems() {
    this.loading = true; // Start loading

    try {
      const daftarResponse = await axios.get('/api/daftar-all');
      const allItems = daftarResponse.data;

      const contentRequests = allItems.map(item => axios.get(`/api/content/${item.explore_id}`));
      const contentResponses = await Promise.all(contentRequests);
      const allContents = contentResponses.flatMap(response => response.data);

      const progressRequests = allItems.map(item => axios.get(`/api/nilai?user_id=${item.user_id}`));
      const progressResponses = await Promise.all(progressRequests);

      this.items = allItems.map((item, index) => {
        const contents = allContents.filter(content => content.explore_id === item.explore_id);
        const progressData = progressResponses[index].data;

        // Hitung progress tanpa filter berdasarkan jenis konten
        const completedCount = contents.reduce((count, content) => {
          const progressItem = progressData.find(
            progress => Number(progress.content_id) === Number(content.content_id) && Number(progress.progress) === 1
          );
          return count + (progressItem ? 1 : 0);
        }, 0);

        const progressPercentage = contents.length > 0 ? Math.round((completedCount / contents.length) * 100) : 0;

        // Hitung nilai rata-rata hanya untuk jenis konten tertentu
        const scores = progressData
          .filter(progress => 
            contents.some(content => Number(content.content_id) === Number(progress.content_id)) &&
            [1, 2, 4].includes(Number(progress.jenis)) // Hanya untuk jenis 1, 2, dan 4
          )
          .map(progress => {
            return Number(progress.nilai);  // Nilai harus dikonversi menjadi angka
          })
          .filter(score => score !== null); // Hapus nilai null yang dihasilkan dari filter

        const averageScore = scores.length > 0 ? scores.reduce((sum, score) => sum + score, 0) / scores.length : 0;

        return { ...item, progressPercentage, averageScore };
      });

      this.paginate();
    } catch (error) {
      console.error('Error fetching data:', error);
    } finally {
      this.loading = false; // End loading
    }
  },

      exportToExcel() {
      // Siapkan data untuk diekspor
      const data = this.items.map((item, index) => ({
        No: index + 1 + (this.currentPage - 1) * this.entriesPerPage,
        Nama_Lengkap: item.nama_lengkap,
        Judul: item.title,
        Nilai_Rata_Rata: item.averageScore.toFixed(2),
        Progress: `${item.progressPercentage}%`
      }));

      // Buat worksheet dari data
      const worksheet = XLSX.utils.json_to_sheet(data);

      // Buat workbook dan tambahkan worksheet
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Data Hasil Rekap Training");

      // Ekspor workbook ke file Excel
      XLSX.writeFile(workbook, "Data_Hasil_Training.xlsx");
    },

    paginate() {
      const start = (this.currentPage - 1) * this.entriesPerPage;
      this.paginatedItems = this.items.slice(start, start + this.entriesPerPage);
    },

    sort(key) {
      this.sortKey = key;
      this.sortOrder *= -1;
      this.items.sort((a, b) => {
        if (a[key] < b[key]) return -1 * this.sortOrder;
        if (a[key] > b[key]) return 1 * this.sortOrder;
        return 0;
      });
      this.paginate();
    },

    getProgress(item) {
      const progress = item.progressPercentage || 0;
      let progressColor = '';

      if (progress >= 80) {
        progressColor = '#1976d2'; // Blue (default color)
      } else if (progress >= 50) {
        progressColor = '#ffc107'; // Yellow
      } else {
        progressColor = '#dc3545'; // Red
      }

      return {
        width: `${progress}%`,
        backgroundColor: progressColor
      };
    }
  },

  watch: {
    currentPage() {
      this.paginate();
    },
  },

  mounted() {
    this.fetchItems();
  },
};
</script>

<style scoped>
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 200px; /* Adjust height as needed */
}

.loading-spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.custom-progress {
  background: #e0e0e0;
  border-radius: 5px;
  height: 15px;
  position: relative;
  overflow: hidden;
}

.progress-bar {
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
</style>
