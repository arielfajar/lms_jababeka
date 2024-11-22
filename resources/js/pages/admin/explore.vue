<template>
  <v-container>
  <v-col>
        <v-btn color="primary" @click="goToAddExplore">
          <i class='bx bx-plus'></i> Add New Explore
        </v-btn>
      </v-col>
    <v-table>
      <thead>
        <tr>
          <th @click="sort('index')" style="cursor: pointer;">No</th>
          <th @click="sort('judul')" style="cursor: pointer;">Judul</th>
          <th @click="sort('tentang')" style="cursor: pointer;">Tentang</th>
          <th @click="sort('objective')" style="cursor: pointer;">Objective</th>
          <th @click="sort('course')" style="cursor: pointer;">Course</th>
          <th @click="sort('trainer')" style="cursor: pointer;">Trainer</th>
          <th @click="sort('img')" style="cursor: pointer;">Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(explore, index) in paginatedExplores" :key="explore.id">
          <td>{{ (currentPage - 1) * entriesPerPage + index + 1 }}</td>
          <td>{{ explore.judul }}</td>
          <td class="truncate-text">{{ explore.tentang }}</td>
          <td class="truncate-text">{{ explore.objective }}</td>
          <td class="truncate-text">{{ explore.course }}</td>
          <td class="truncate-text">{{ explore.trainer }}</td>
          <td>
            <img :src="getImageUrl(explore.img)" alt="Explore Image" width="50" />
          </td>
          <td>
            <!-- Trigger modal for detailed view -->
            <v-btn icon @click="openDetailModal(explore)">
              <v-icon>bx-search-alt</v-icon>
            </v-btn>
            <v-btn icon @click="openEditModal(explore)">
                <v-icon>bx-edit</v-icon>
            </v-btn>
            <!-- Trigger delete confirmation with SweetAlert -->
            <v-btn icon @click="confirmDelete(explore.explore_id)">
              <v-icon>bx-trash</v-icon>
            </v-btn>
          </td>
        </tr>
      </tbody>
    </v-table>

     <!-- Edit Modal -->
    <v-dialog v-model="isEditModalOpen" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">Edit Explore Entry</v-card-title>
        <v-card-text>
          <v-form ref="editForm" v-model="valid">
            <v-text-field v-model="editExplore.judul" label="Judul" class="mb-4" required></v-text-field>
            <v-textarea v-model="editExplore.tentang" label="Tentang" class="mb-4" required></v-textarea>
            <v-textarea v-model="editExplore.objective" label="Objective" class="mb-4" required></v-textarea>
            <v-textarea v-model="editExplore.course" label="Course" class="mb-4" required></v-textarea>
            <v-textarea v-model="editExplore.trainer" label="Trainer" class="mb-4" required></v-textarea>
            <v-file-input
              v-model="editImage"
              label="Image"
              accept="image/*"
              @change="previewImage"
              required
            ></v-file-input>
            <v-img v-if="previewImageUrl" :src="previewImageUrl" class="mt-4" width="100%" height="auto"></v-img>          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" @click="updateExplore" :disabled="!valid">Save</v-btn>
          <v-btn color="grey" text @click="isEditModalOpen = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Pagination -->
    <v-pagination
      v-model="currentPage"
      :length="pageCount"
      :total-visible="5"
    ></v-pagination>

    <!-- Modal for detailed view -->
    <v-dialog v-model="isDetailModalOpen" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">Explore Details</v-card-title>
        <v-card-text>
          <p><strong>Judul:</strong> {{ selectedExplore.judul }}</p>
          <p><strong>Tentang:</strong> {{ selectedExplore.tentang }}</p>
          <p><strong>Objective:</strong> {{ selectedExplore.objective }}</p>
          <p><strong>Course:</strong> {{ selectedExplore.course }}</p>
          <p><strong>Trainer:</strong> {{ selectedExplore.trainer }}</p>
          <img :src="getImageUrl(selectedExplore.img)" alt="Explore Image" width="100%" />
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" text @click="isDetailModalOpen = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      explores: [],
      currentPage: 1,
      entriesPerPage: 10,
      sortKey: 'index',
      sortDirection: 'asc',
      isDetailModalOpen: false,
      isEditModalOpen: false,
      selectedExplore: {}, // for viewing details
       editExplore: {}, // Data for editing
      editImage: null,
      previewImageUrl: null, // For storing the selected image
      valid: false,
    };
  },
  computed: {
    paginatedExplores() {
      const start = (this.currentPage - 1) * this.entriesPerPage;
      const end = this.currentPage * this.entriesPerPage;
      return this.explores.slice(start, end);
    },
    pageCount() {
      return Math.ceil(this.explores.length / this.entriesPerPage);
    },
  },
  methods: {
    async fetchExplores() {
      try {
        const response = await axios.get('/api/explore');
        this.explores = response.data;
      } catch (error) {
        console.error('Error fetching explore data:', error);
      }
    },
    openEditModal(explore) {
      this.editExplore = { ...explore }; // Copy the explore entry for editing
      this.isEditModalOpen = true;
      this.editImage = null; // Reset the image input
       this.previewImageUrl = this.getImageUrl(explore.img);
    },
    async updateExplore() {
      try {
        const formData = new FormData();
        formData.append('judul', this.editExplore.judul);
        formData.append('tentang', this.editExplore.tentang);
        formData.append('objective', this.editExplore.objective);
        formData.append('course', this.editExplore.course);
        formData.append('trainer', this.editExplore.trainer);
        if (this.editImage) {
          formData.append('image', this.editImage); // Add image if it was updated
        }

        await axios.post(`/api/explore/${this.editExplore.explore_id}`, formData);
        this.fetchExplores(); // Refresh the list
        this.isEditModalOpen = false; // Close the modal
        Swal.fire('Success!', 'Explore entry updated successfully.', 'success');
      } catch (error) {
        console.error('Error updating explore entry:', error);
        Swal.fire('Error!', 'There was a problem updating this entry.', 'error');
      }
    },
    previewImage(event) {
      const file = event.target.files[0];
      if (file) {
        this.editImage = file;
        this.previewImageUrl = URL.createObjectURL(file); // Create a preview URL
      }
    },
    sort(key) {
      if (this.sortKey === key) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortDirection = 'asc';
      }

      this.explores.sort((a, b) => {
        let result = 0;
        if (a[key] < b[key]) result = -1;
        if (a[key] > b[key]) result = 1;
        return result * (this.sortDirection === 'asc' ? 1 : -1);
      });
    },
    getImageUrl(imagePath) {
      return `/storage/${imagePath}`;
    },
    openDetailModal(explore) {
      this.selectedExplore = explore;
      this.isDetailModalOpen = true;
    },
    goToAddExplore() {
      this.$router.push('./explore/add');
    },
    confirmDelete(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.deleteExplore(id);
        }
      });
    },
    async deleteExplore(id) {
      try {
        await axios.delete(`/api/explore/${id}`);
        await this.fetchExplores();
        Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
      } catch (error) {
        Swal.fire('Error!', 'There was a problem deleting this entry.', 'error');
        console.error('Error deleting explore entry:', error);
      }
    },
  },
  mounted() {
    this.fetchExplores();
  },
};
</script>

<style scoped>
v-table {
  max-height: 400px;
  overflow-y: auto;
}

img {
  border-radius: 4px;
}

.truncate-text {
  max-width: 150px; /* Adjust the width as needed */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
