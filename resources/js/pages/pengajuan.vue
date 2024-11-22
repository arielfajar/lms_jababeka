<template>
  <v-container>
    <v-row>
      <!-- Table Container -->
      <v-col cols="12" md="8">
        <v-card class="pa-3">
          <v-card-title>
            <h2 class="text-h7">Pengajuan Training Anda</h2>
            <v-spacer></v-spacer>
            <v-text-field
              v-model="searchQuery"
              append-icon="bx-search"
              label="Search"
              single-line
              hide-details
              class="search-field"
              cols="12" md="4" xs="12"
            ></v-text-field>
          </v-card-title>

          <!-- Responsive Table -->
          <div class="table-responsive">
            <v-simple-table class="custom-table">
              <thead>
                <tr>
                <th class="col-no">No</th>
                <th class="col-nama">Nama</th>
                <th class="col-judul">Judul Training</th>
                <th class="col-vendor">Vendor</th>
                <th class="col-tempat">Tempat</th>
                <th class="col-jam">Jam</th>
                <th class="col-harga">Harga</th>
                <th class="col-status">Status</th>
                <th class="col-action">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredList.length === 0">
                  <td colspan="9" class="text-center">
                    Belum Ada Pengajuan yang Tersedia
                  </td>
                </tr>
                 <tr v-else v-for="(item, index) in filteredList" :key="item.id">
                  <td>{{ index + 1 + (currentPage - 1) * itemsPerPage }}</td>
                  <td>{{ item.nama }}</td>
                  <td>{{ item.judul_training }}</td>
                  <td>{{ item.penyelenggara }}</td>
                  <td>{{ item.tempat }}</td>
                  <td>{{ item.jam }}</td>
                  <td>{{ item.biaya }}</td>
                    <td style="text-align: center;">
                        <span :class="getStatusClass(item.status)" class="status-text">
                        {{ getStatusText(item.status) }}
                        </span>
                    </td>
                  <td>
                    <v-btn icon small @click="editPengajuan(item)">
                      <i class="bx bx-pencil"></i>
                    </v-btn>
                    <v-btn icon small @click="printPengajuan(item.pengajuan_id)">
                      <i class="bx bxs-printer"></i>
                    </v-btn>
                    <v-btn icon small @click="deletePengajuan(item)">
                      <i class="bx bx-trash"></i>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </v-simple-table>
          </div>

          <!-- Pagination and Items Per Page -->
          <v-row class="mt-4" align="center">
            <v-col cols="auto">
              <v-pagination
                v-model="currentPage"
                :length="pageCount"
                class="pagination"
              ></v-pagination>
            </v-col>
            <v-col cols="auto">
              <v-select
                v-model="itemsPerPage"
                :items="[5, 10, 15]"
                label="Items per page"
                class="items-per-page"
              ></v-select>
            </v-col>
          </v-row>
        </v-card>
      </v-col>

      <!-- Add Container -->
      <v-col cols="12" md="4">
        <v-card class="pa-3 text-center">
          <p class="add-training-text">Tambahkan Pengajuan Training Sesuai Keinginan Anda</p>
          <v-btn block color="primary" @click="navigateToForm">
            + Tambah
          </v-btn>
        </v-card>
      </v-col>
    </v-row>
  </v-container>

  <!-- Modal Edit Pengajuan -->
<v-dialog v-model="dialogEdit" max-width="600px">
  <v-card>
    <v-card-title>
      <span class="text-h6">Edit Pengajuan Training</span>
    </v-card-title>

    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="12" sm="6" md="6">
            <v-text-field
              label="Nama"
              v-model="editedItem.nama"
              :rules="[rules.required]"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <v-text-field
              label="Judul Training"
              v-model="editedItem.judul_training"
              :rules="[rules.required]"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <v-text-field
              label="Vendor"
              v-model="editedItem.penyelenggara"
              :rules="[rules.required]"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <v-text-field
              label="Tempat"
              v-model="editedItem.tempat"
              :rules="[rules.required]"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <v-text-field
              label="Tanggal Pelaksanaan"
              v-model="editedItem.hari_tanggal"
              :rules="[rules.required]"
              type="date"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <v-text-field
              label="Jam"
              v-model="editedItem.jam"
              :rules="[rules.required]"
              type="time"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <v-text-field
              label="Biaya"
              v-model="editedItem.biaya"
              :rules="[rules.required, rules.number]"
              type="number"
            ></v-text-field>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>

    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="dialogEdit = false">Batal</v-btn>
      <v-btn color="blue darken-1" text @click="saveEdit">Simpan</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>

</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      pengajuanList: [],
      currentPage: 1,
      itemsPerPage: 5,
      searchQuery: '',
      userId: null, // Store user ID here
      dialogEdit: false,
      editedItem: {
        nama: '',
        judul_training: '',
        penyelenggara: '',
        tempat: '',
        hari_tanggal: '',
        jam: '',
        biaya: '',
        status: ''
      },
      rules: {
        required: (value) => !!value || 'Field ini wajib diisi',
        number: (value) => !isNaN(value) || 'Harus berupa angka',
      }

    };
  },
  computed: {
    pageCount() {
      return Math.ceil(this.filteredList.length / this.itemsPerPage);
    },
    filteredList() {
      const search = this.searchQuery.toLowerCase();
      return this.pengajuanList
        .filter(item =>
          item.nama.toLowerCase().includes(search) ||
          item.judul_training.toLowerCase().includes(search) ||
          item.penyelenggara.toLowerCase().includes(search) ||
          item.tempat.toLowerCase().includes(search)
        )
        .filter(item => item.user_id === this.userId); // Filter by user_id
    }
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
    navigateToForm() {
      this.$router.push({ path: '/pengajuan/form' });
    },
    editPengajuan(item) {
      this.editedItem = { ...item };
      this.dialogEdit = true;
    },
    saveEdit() {
      axios.put(`/api/pengajuan/${this.editedItem.pengajuan_id}`, this.editedItem)
        .then((response) => {
          this.dialogEdit = false;
          Swal.fire('Berhasil', 'Data pengajuan telah diperbarui', 'success');
          this.fetchPengajuan();
        })
        .catch((error) => {
          Swal.fire('Gagal', 'Tidak dapat menyimpan perubahan', 'error');
        });
    },
    printPengajuan(pengajuan_id) {
      this.$router.push({ path: `/pengajuan/cetak/${pengajuan_id}` });
    },
    deletePengajuan(item) {
        Swal.fire({
            title: 'Anda yakin?',
            text: "Data pengajuan ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/api/pengajuan/${item.pengajuan_id}`)
                    .then(response => {
                        // Show success alert
                        Swal.fire(
                            'Terhapus!',
                            response.data.message,
                            'success'
                        );

                        // Refresh the pengajuan list
                        this.fetchPengajuan(); // Call the fetch function to reload data
                    })
                    .catch(error => {
                        // Show error alert
                        Swal.fire(
                            'Gagal!',
                            'Ada kesalahan saat menghapus pengajuan.',
                            'error'
                        );
                    });
            }
        });
    },
    fetchPengajuan() {
      axios.get('/api/pengajuan')
        .then(response => {
          this.pengajuanList = response.data;
        })
        .catch(error => {
          console.error('There was an error fetching the pengajuan!', error);
        });
    },
    getUserId() {
      axios.get('/api/user', {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      })
      .then(response => {
        this.userId = response.data.user_id; // Store the user ID
        this.fetchPengajuan(); // Fetch pengajuan after getting user ID
      })
      .catch(error => {
        console.error('Error fetching user ID:', error);
      });
    }
  },
  mounted() {
    this.getUserId(); // Fetch user ID when component is mounted
  }
};
</script>

<style scoped>
/* Mengecilkan ukuran font dan padding */
.custom-table {
  border-collapse: collapse;
  width: 100%;
  font-size: 14px; /* Ukuran font lebih kecil */
}

.custom-table th,
.custom-table td {
  border: 1px solid #ddd;
  padding: 6px; /* Padding lebih kecil */
  text-align: left;
}

.custom-table th {
  background: rgba(1, 228, 0, 0.1);
  font-weight: bold;
  text-align: center;
}

.table-responsive {
  overflow-x: auto;
  margin-top: 20px;
}

.v-card {
  margin-top: 20px;
  font-size: 19px; /* Ukuran font untuk konten card */
}

.v-card-title {
  font-size: 14px; /* Ukuran font untuk judul card */
}

.bx {
  font-size: 16px; /* Mengecilkan ukuran ikon */
}

.mt-4 {
  margin-top: 1rem !important;
}

.pagination,
.items-per-page {
  display: flex;
  align-items: center;
}

.items-per-page {
  margin-left: 16px;
}

.search-field {
  width: 40%;
  max-width: 100%; /* Ensure full width on smaller screens */
}

/* Additional responsive styling */
@media (max-width: 600px) {
  .v-card-title {
    flex-direction: column; /* Stack elements on small screens */
  }
  .search-field {
    width: 70%;
    margin-top: 10px; /* Add spacing for readability */
  }
}

.add-training-text {
  font-size: 14px; /* Mengecilkan ukuran teks deskripsi */
}

.v-card-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Scroll horizontal jika datanya banyak */
.table-responsive {
  max-width: 100%;
  overflow-x: scroll;
}

.custom-table {
  width: 100%;
  table-layout: fixed; /* Menjaga lebar kolom tetap proporsional */
}

.custom-table th,
.custom-table td {
  white-space: nowrap; /* Agar teks tidak terpotong */
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

.col-no {
  width: 2 %;
}
.col-nama {
  width: 15%;
}
.col-judul {
  width: 20%;
}
.col-vendor {
  width: 15%;
}
.col-tempat {
  width: 15%;
}
.col-jam {
  width: 10%;
}
.col-harga {
  width: 10%;
}
.col-status {
  width: 5%;
}
.col-action {
  width: 5%;
}
</style>
