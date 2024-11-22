<template>
  <v-container fluid class="pa-5">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card>
          <v-card-title class="text-h5">Tambah Soal</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitForm">
              <div v-for="(soal, index) in form.soal" :key="index">
                <v-textarea
                  v-model="soal.pertanyaan"
                  :label="`Pertanyaan ${index + 1}`"
                  required
                  outlined
                  auto-grow
                  rows="1"
                  class="mb-4"
                ></v-textarea>

                <div v-for="(jawaban, jIndex) in soal.jawaban" :key="jIndex" class="mb-4">
                  <v-textarea
                    v-model="soal.jawaban[jIndex]"
                    :label="`Jawaban ${jIndex + 1}`"
                    required
                    outlined
                    rows="1"
                    auto-grow
                  ></v-textarea>
                </div>

                <v-btn @click="addJawaban(index)" color="green" class="mb-4">Tambah Pilihan Jawaban</v-btn>

                <label :for="`jawaban_benar_${index}`" class="form-label">Jawaban Benar</label>
                <select v-model="soal.jawaban_benar" :id="`jawaban_benar_${index}`" class="form-select" required>
                  <option v-for="jawaban in soal.jawaban" :key="jawaban" :value="jawaban">
                    {{ jawaban }}
                  </option>
                </select>

                <v-btn @click="removeSoal(index)" color="red" class="mb-4 mt-2">Hapus Soal</v-btn>

                <!-- <v-divider class="mb-4"></v-divider> -->
                <div class="bottom-border"></div>
              </div>

              <v-btn @click="addSoal" color="secondary" class="mb-4">+ Tambah Soal</v-btn>

              <v-text-field
                v-model="form.durasi"
                label="Durasi (Menit)"
                type="number"
                required
                outlined
                class="mb-4"
              ></v-text-field>

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
        soal: [
          {
            pertanyaan: '',
            jawaban: ['', ''],
            jawaban_benar: '',
          }
        ],
        durasi: ''
      }
    };
  },
  methods: {
    addJawaban(index) {
      this.form.soal[index].jawaban.push('');
    },
    addSoal() {
      this.form.soal.push({
        pertanyaan: '',
        jawaban: ['', ''],
        jawaban_benar: '',
      });
    },
    removeSoal(index) {
      this.form.soal.splice(index, 1);
    },
    async submitForm() {
      try {
        const promises = this.form.soal.map(async (soal) => {
          const payload = {
            content_id: this.form.content_id,
            pertanyaan: soal.pertanyaan,
            jawaban: soal.jawaban,
            jawaban_benar: soal.jawaban_benar,
            durasi: this.form.durasi
          };
          await axios.post('/api/soal', payload);
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
}
.form-select {
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
.form-select:focus {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.bottom-border {
  border-bottom: 2px solid #008000;
  margin-top: 20px;
  margin-bottom: 20px;
}
</style>
