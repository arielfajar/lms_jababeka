<template>
  <v-container>
    <v-card>
      <v-card-title>
        <h2>TTD Sertifikat</h2>
      </v-card-title>

      <v-card-text>
        <v-table>
          <thead>
            <tr>
              <th @click="sort('index')" style="cursor: pointer;">No</th>
              <th @click="sort('nama')" style="cursor: pointer;">Nama</th>
              <th @click="sort('jabatan')" style="cursor: pointer;">Jabatan</th>
              <th @click="sort('img')" style="cursor: pointer;">Image</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(ttd, index) in paginatedTtds" :key="ttd.id">
              <td>{{ (currentPage - 1) * entriesPerPage + index + 1 }}</td>
              <td>{{ ttd.nama }}</td>
              <td>{{ ttd.jabatan }}</td>
              <td>
                <v-img :src="`/storage/${ttd.img}`" max-width="100" max-height="100"></v-img>
              </td>
              <td>
                <v-btn icon @click="openEditModal(ttd)">
                  <v-icon>bx-pencil</v-icon>
                </v-btn>
                <v-btn icon @click="deleteTtd(ttd.id)">
                  <v-icon>bx-trash</v-icon>
                </v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>

        <v-pagination v-model="currentPage" :length="totalPages"></v-pagination>

        <!-- Edit Modal -->
        <v-dialog v-model="editDialog" max-width="600px">
          <v-card>
            <v-card-title>
              <h2>Edit TTD</h2>
            </v-card-title>
            <v-card-text>
              <v-form ref="editForm" v-model="editValid" lazy-validation>
                <v-text-field
                  v-model="editForm.nama"
                  :rules="[rules.required]"
                  label="Name"
                  required
                ></v-text-field>

                <v-text-field
                  v-model="editForm.jabatan"
                  :rules="[rules.required]"
                  label="Position"
                  required
                ></v-text-field>

                <v-file-input
                  v-model="editForm.img"
                  label="Upload New Image"
                  @change="onEditFileChange"
                ></v-file-input>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-btn @click="updateTtd" color="primary">Update</v-btn>
              <v-btn @click="editDialog = false">Cancel</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      ttds: [],
      currentPage: 1,
      entriesPerPage: 10,
      editDialog: false,
      editForm: {
        id: null,
        nama: '',
        jabatan: '',
        img: null,
      },
      editValid: false,
      rules: {
        required: value => !!value || 'Required.',
      },
    };
  },
  computed: {
    paginatedTtds() {
      const start = (this.currentPage - 1) * this.entriesPerPage;
      return this.ttds.slice(start, start + this.entriesPerPage);
    },
    totalPages() {
      return Math.ceil(this.ttds.length / this.entriesPerPage);
    },
  },
  methods: {
    async fetchTtds() {
      try {
        const response = await axios.get('/api/ttd');
        this.ttds = response.data;
      } catch (error) {
        console.error('Error fetching TTDs:', error);
      }
    },
    sort(field) {
      this.ttds.sort((a, b) => (a[field] > b[field] ? 1 : -1));
    },
    openEditModal(ttd) {
      this.editForm = { ...ttd };
      this.editDialog = true;
    },
    async updateTtd() {
      if (this.$refs.editForm.validate()) {
        const formData = new FormData();
        formData.append('id', this.editForm.ttd_id); // Include ID
        formData.append('nama', this.editForm.nama);
        formData.append('jabatan', this.editForm.jabatan);
        if (this.editForm.img) {
          formData.append('img', this.editForm.img);
        }

        try {
          await axios.post(`/api/ttd/update`, formData); // POST request to update
          this.fetchTtds();
          this.editDialog = false;
        } catch (error) {
          console.error('Error updating TTD:', error);
        }
      }
    },
    async deleteTtd(id) {
      if (confirm('Are you sure you want to delete this record?')) {
        try {
          await axios.delete(`/api/ttd/${id}`);
          this.fetchTtds();
        } catch (error) {
          console.error('Error deleting TTD:', error);
        }
      }
    },
    onEditFileChange(event) {
      this.editForm.img = event.target.files[0];
    },
  },
  mounted() {
    this.fetchTtds();
  },
};
</script>

<style scoped>
/* Add your styles here */
</style>
