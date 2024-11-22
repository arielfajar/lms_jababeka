<template>
  <v-container class="py-5" max-width="600px">
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card outlined>
          <v-card-title class="text-h5">Form Pengajuan</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitPengajuan">
              <v-row>
                <!-- Step 1 -->
                <v-col v-if="step === 1" cols="12">
                  <v-select
                    label="Pengajuan Untuk *"
                    :items="['Diri Sendiri', 'Orang Lain']"
                    v-model="formData.registration_for"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-select>

                  <v-text-field
                    label="NIK *"
                    v-model="formData.nik"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    label="Nama *"
                    v-model="formData.nama"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    label="Divisi *"
                    v-model="formData.divisi"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    label="Jabatan *"
                    v-model="formData.jabatan"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    v-model="formData.tanggal_masuk"
                    label="Tanggal Masuk *"
                    type="date"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    v-if="formData.registration_for === 'Orang Lain'"
                    label="Tanda Tangan (TTD) *"
                    type="file"
                    @change="handleFileUpload"
                    outlined
                    dense
                    class="mb-4"
                  ></v-text-field>

                  <v-text-field
                    label="Email Address *"
                    v-model="formData.email"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-btn color="primary" @click="nextStep">Selanjutnya</v-btn>
                </v-col>

                <!-- Step 2 -->
                <v-col v-if="step === 2" cols="12">
                  <v-text-field
                    label="Judul Training *"
                    v-model="formData.judul_training"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    label="Penyelenggara *"
                    v-model="formData.penyelenggara"
                    outlined
                    dense
                    required
                    class="mb-4"
                  ></v-text-field>

                                    <v-text-field
                    label="Biaya *"
                    v-model="formData.biaya"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    v-model="formData.hari_tanggal"
                    label="Hari/Tanggal *"
                    type="date"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    v-model="formData.jam"
                    label="Jam *"
                    type="time"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    label="Tempat *"
                    v-model="formData.tempat"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <!-- Replace Vuetify checkboxes with native HTML checkboxes -->
                  <div class="checkbox-group mb-4">
                    <label>Tujuan Training *</label>
                    <div>
                      <label>
                        <input
                          type="checkbox"
                          value="Pembekalan untuk Promosi Karyawan"
                          @change="handleCheckboxChange"
                        />
                        Pembekalan untuk Promosi Karyawan
                      </label>
                    </div>
                    <div>
                      <label>
                        <input
                          type="checkbox"
                          value="Pengembangan kompetensi/keahlian karyawan di posisi saat ini."
                          @change="handleCheckboxChange"
                        />
                        Pengembangan kompetensi/keahlian karyawan di posisi saat ini.
                      </label>
                    </div>
                    <div>
                      <label>
                        <input
                          type="checkbox"
                          value="Lain-lain"
                          @change="handleCheckboxChange"
                        />
                        Lain-lain:
                      </label>
                    </div>

                    <!-- Show input for other reasons if "Lain-lain" is checked -->
                    <v-text-field
                      v-if="formData.isLainLainChecked"
                      label="Tujuan Lain-lain"
                      v-model="formData.tujuan_lain"
                      outlined
                      dense
                      class="mb-4"
                    ></v-text-field>
                  </div>

                  <v-text-field
                    label="Apa yang diharapkan dari Training - Harapan ke-1"
                    v-model="formData.harapan_training[0]"
                    outlined
                    dense
                    class="mb-4"
                    required
                  ></v-text-field>

                  <v-text-field
                    label="Apa yang diharapkan dari Training - Harapan ke-2"
                    v-model="formData.harapan_training[1]"
                    outlined
                    dense
                    class="mb-4"
                  ></v-text-field>

                  <v-text-field
                    label="Apa yang diharapkan dari Training - Harapan ke-3"
                    v-model="formData.harapan_training[2]"
                    outlined
                    dense
                    class="mb-4"
                  ></v-text-field>



                  <v-row justify="space-between" class="mt-4">
                    <v-btn text @click="prevStep">Kembali</v-btn>
                    <v-btn color="primary" type="submit">Kirim Pengajuan</v-btn>
                  </v-row>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
  data() {
    return {
      step: 1, 
      formData: {
        registration_for: "Diri Sendiri",
        user_id: null,
        nik: "",
        nama: "",
        divisi: "",
        jabatan: "",
        tanggal_masuk: null,
        email: "",
        judul_training: "",
        penyelenggara: "",
        tujuan_training: [], 
        tujuan_lain: "",
        harapan_training: ["", "", ""], 
        biaya: "",
        hari_tanggal: null,
        jam: null,
        tempat: "",
        ttd: null, 
        approval_line: null,
        status: 'pending',
        isLainLainChecked: false // New boolean flag for "Lain-lain"
      },
    };
  },
  created() {
    this.getUserData();
  },
  watch: {
    'formData.registration_for'(newValue) {
      if (newValue === 'Orang Lain') {
        this.clearForm();
      } else {
        this.getUserData();
      }
    },
  },
  methods: {
    async getUserData() {
      try {
        const userResponse = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.formData.user_id = userResponse.data.user_id;
        this.formData.approval_line = userResponse.data.approval_line;
        this.formData.nik = userResponse.data.nik;
        this.formData.nama = userResponse.data.nama_lengkap;
        this.formData.divisi = userResponse.data.divisi;
        this.formData.jabatan = userResponse.data.jabatan;
        this.formData.tanggal_masuk = userResponse.data.join_date;
        this.formData.email = userResponse.data.email;
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    },

    clearForm() {
      this.formData.nik = "";
      this.formData.nama = "";
      this.formData.divisi = "";
      this.formData.jabatan = "";
      this.formData.tanggal_masuk = null;
      this.formData.email = "";
    },

    handleFileUpload(event) {
      const file = event.target.files[0];
      this.formData.ttd = file;
    },

    handleCheckboxChange(event) {
      const value = event.target.value;

      if (value === "Lain-lain") {
        if (!event.target.checked) {
          // Jika "Lain-lain" tidak dicentang, reset tujuan_lain dan hapus dari tujuan_training
          this.formData.tujuan_lain = "";
          this.formData.tujuan_training = this.formData.tujuan_training.filter(
            (item) => item !== "Lain-lain"
          );
          this.formData.isLainLainChecked = false;
        } else {
          // Jika dicentang, tambahkan "Lain-lain" ke tujuan_training
          this.formData.tujuan_training.push("Lain-lain");
          this.formData.isLainLainChecked = true;
        }
      } else {
        if (event.target.checked) {
          this.formData.tujuan_training.push(value);
        } else {
          this.formData.tujuan_training = this.formData.tujuan_training.filter(
            (item) => item !== value
          );
        }
      }
    },

    nextStep() {
      this.step++;
    },

    prevStep() {
      this.step--;
    },

    async submitPengajuan() {
       // Cek apakah "Lain-lain" dipilih dan field tujuan_lain diisi
      if (this.formData.isLainLainChecked && this.formData.tujuan_lain.trim() !== "") {
        const index = this.formData.tujuan_training.indexOf("Lain-lain");
        if (index !== -1) {
          // Ganti "Lain-lain" dengan nilai input tujuan_lain
          this.formData.tujuan_training[index] = this.formData.tujuan_lain;
        }
      }
      const formData = new FormData();

      for (const key in this.formData) {
        if (Array.isArray(this.formData[key])) {
          this.formData[key].forEach((item) => {
            formData.append(`${key}[]`, item);
          });
        } else {
          formData.append(key, this.formData[key]);
        }
      }

      try {
        const response = await axios.post('/api/pengajuan', formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data',
          },
        });

        Swal.fire({
          title: 'Pengajuan Berhasil!',
          text: response.data.message,
          icon: 'success',
          confirmButtonText: 'OK',
        }).then(() => {
          this.$router.push('/pengajuan'); // Redirect to /pengajuan
        });
      } catch (error) {
        if (error.response && error.response.data.errors) {
          console.error("Validation errors:", error.response.data.errors);
          Swal.fire({
            title: 'Pengajuan Gagal!',
            text: 'Cek form Anda!',
            icon: 'error',
            confirmButtonText: 'OK',
          });
        } else {
          console.error("Error submitting pengajuan:", error);
          Swal.fire({
            title: 'Terjadi Kesalahan!',
            text: 'Terjadi kesalahan dalam pengajuan!',
            icon: 'error',
            confirmButtonText: 'OK',
          });
        }
      }
    },
  },
};
</script>

<style scoped>
.checkbox-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
</style>
