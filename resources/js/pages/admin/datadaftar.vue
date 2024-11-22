<template>
  <div>
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
          <th style="text-align: center;">Status</th>
          <th style="text-align: center;">Progress</th>
          <th style="text-align: center;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in paginatedItems" :key="item.daftar_id">
          <td>{{ (currentPage - 1) * entriesPerPage + index + 1 }}</td>
          <td>{{ item.nama_lengkap }}</td>
          <td>{{ item.title }}</td>
          <td style="text-align: center;">
            <span :class="getStatusClass(item.status)" class="status-text">
              {{ getStatusText(item.status) }}
            </span>
          </td>
          <td>
            <div class="custom-progress">
              <div class="progress-bar" :style="{ width: getProgress(item) + '%' }"></div>
              <span class="progress-text">{{ getProgress(item) }}%</span>
            </div>
          </td>
          <td style="text-align: center;">
            <v-btn icon @click="confirmApprove(item)">
              <v-icon>bx-check</v-icon>
            </v-btn>
            <v-btn icon @click="confirmReject(item)">
              <v-icon>bx-x</v-icon>
            </v-btn>
            <v-btn icon @click="confirmDelete(item.daftar_id)">
              <v-icon>bx-trash</v-icon>
            </v-btn>
          </td>
        </tr>
      </tbody>
    </v-table>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      items: [],
      paginatedItems: [],
      currentPage: 1,
      entriesPerPage: 10,
      sortKey: '',
      sortOrder: 1,
      loading: true, // Add loading state
    };
  },
  methods: {
  getStatusText(status) {
    const intStatus = parseInt(status, 10);
    switch (intStatus) {
      case 0:
        return 'Pending';
      case 1:
        return 'Approved';
      case 2:
        return 'Rejected';
      default:
        return 'Unknown';
    }
  },
  getStatusClass(status) {
    const intStatus = parseInt(status, 10);
    switch (intStatus) {
      case 0:
        return 'status-pending';
      case 1:
        return 'status-approved';
      case 2:
        return 'status-rejected';
      default:
        return '';
    }
  },
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

          const completedCount = contents.reduce((count, content) => {
            const progressItem = progressData.find(
              progress => Number(progress.content_id) === Number(content.content_id) && Number(progress.progress) === 1
            );
            return count + (progressItem ? 1 : 0);
          }, 0);

          const progressPercentage = contents.length > 0 ? Math.round((completedCount / contents.length) * 100) : 0;
          return { ...item, progressPercentage };
        });

        this.paginate();
      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        this.loading = false; // End loading
      }
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
      return item.progressPercentage || 0;
    },
    async confirmApprove(item) {
        try {
          await axios.patch(`/api/daftar/${item.daftar_id}/status`, { status: 1 });
          item.status = 1; // Perbarui status lokal
          await Swal.fire('Approved!', 'The entry has been approved.', 'success');
        } catch (error) {
          console.error('Error approving entry:', error);
          await Swal.fire('Error!', 'There was a problem approving the entry.', 'error');
        }
      },

  async confirmReject(item) {
    try {
      await axios.patch(`/api/daftar/${item.daftar_id}/status`, { status: 2 });
      item.status = 2; // Perbarui status lokal
      await Swal.fire('Rejected!', 'The entry has been rejected.', 'success');
    } catch (error) {
      console.error('Error rejecting entry:', error);
      await Swal.fire('Error!', 'There was a problem rejecting the entry.', 'error');
    }
  },
    async confirmDelete(id) {
      const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this entry?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/daftar/${id}`);
          await Swal.fire('Deleted!', 'Your entry has been deleted.', 'success');
          this.fetchItems();
        } catch (error) {
          console.error('Error deleting entry:', error);
          await Swal.fire('Error!', 'There was a problem deleting the entry.', 'error');
        }
      }
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
.status-pending {
  background-color: #ffcc00; /* Kuning untuk Pending */
  color: white;
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 12px;
}

.status-approved {
  background-color: #4caf50; /* Hijau untuk Approved */
  color: white;
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 12px;
}

.status-rejected {
  background-color: #f44336; /* Merah untuk Rejected */
  color: white;
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 12px;
}

.status-text {
  display: inline-block;
}

</style>
