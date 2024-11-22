<template>
  <v-container fluid class="pa-5">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card>
          <v-card-title class="text-h5">Tambah Materi</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitForm">
              <div v-for="(materi, index) in form.materi" :key="index" class="mb-4">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" v-model="materi.judul" id="judul" class="form-control mb-2" required />

                <label for="jenis" class="form-label">Jenis</label>
                <select v-model="materi.jenis" id="jenis" class="form-select mb-2" @change="handleJenisChange(index)" required>
                  <option value="pdf">PDF</option>
                  <!-- <option value="ppt">PPT</option> -->
                  <option value="youtube">YouTube</option>
                </select>

                <div v-if="materi.jenis === 'pdf' || materi.jenis === 'ppt'" class="mb-4">
                  <label for="link" class="form-label">Upload File</label>
                  <input type="file" @change="handleFileUpload($event, index)" class="form-control-file" />
                </div>

                <div v-if="materi.jenis === 'youtube'" class="mb-4">
                  <label for="link" class="form-label">YouTube Link</label>
                  <input type="url" v-model="materi.link" id="link" class="form-control" required />
                </div>

                <v-btn @click="removeMateri(index)" color="red" class="mt-2">Hapus Materi</v-btn>
                <v-divider class="my-4"></v-divider>
              </div>

              <v-btn @click="addMateri" color="secondary" class="mb-4">+ Tambah Materi</v-btn>
              <v-btn type="submit" color="primary" class="mt-3" block>Submit</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        content_id: this.$route.query.content_id || '',
        materi: [
          {
            judul: '',
            jenis: 'pdf', // default to pdf
            link: '',
            file: null
          }
        ]
      }
    };
  },
  methods: {
    handleJenisChange(index) {
      this.form.materi[index].link = '';
      this.form.materi[index].file = null;
    },
    handleFileUpload(event, index) {
      this.form.materi[index].file = event.target.files[0];
    },
    addMateri() {
      this.form.materi.push({
        judul: '',
        jenis: 'pdf', // default to pdf
        link: '',
        file: null
      });
    },
    removeMateri(index) {
      this.form.materi.splice(index, 1);
    },
    async submitForm() {
      try {
        let promises = this.form.materi.map(async (materi) => {
          let formData = new FormData();
          formData.append('content_id', this.form.content_id);
          formData.append('judul', materi.judul);
          formData.append('jenis', materi.jenis);

          if (materi.jenis === 'pdf' || materi.jenis === 'ppt') {
            formData.append('link', materi.file);
          } else if (materi.jenis === 'youtube') {
            formData.append('link', materi.link);
          }

          await axios.post('/api/materi', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
        });

        await Promise.all(promises);
        this.$router.push('/admin/content');
      } catch (error) {
        console.error('Error submitting form:', error);
      }
    }
  }
};
</script>

<style scoped>
.v-container {
  max-width: 2000px;
  margin: 0 auto;
}
.form-label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
.form-select, .form-control {
  width: 100%;
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
.form-select:focus, .form-control:focus {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
