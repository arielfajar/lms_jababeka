<template>
  <v-container fluid class="pa-5" style="background-color: #f5f5f5;">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card>
          <v-card-title class="text-h5">Add New Explore Entry</v-card-title>
          <v-card-text>
            <v-form>
              <template v-if="step === 1">
                <v-text-field
                  v-model="exploreForm.judul"
                  label="Title"
                  required
                  outlined
                  class="mb-4"
                ></v-text-field>

                <v-textarea
                  v-model="exploreForm.tentang"
                  label="About"
                  required
                  outlined
                  class="mb-4"
                ></v-textarea>

                <v-textarea
                  v-model="exploreForm.objective"
                  label="Objective"
                  required
                  outlined
                  class="mb-4"
                ></v-textarea>

                <v-textarea
                  v-model="exploreForm.course"
                  label="Course Content"
                  required
                  outlined
                  class="mb-4"
                ></v-textarea>

                <v-text-field
                  v-model="exploreForm.trainer"
                  label="Trainer"
                  required
                  outlined
                  class="mb-4"
                ></v-text-field>

                <v-file-input
                  v-model="exploreForm.image"
                  label="Upload Image"
                  required
                  outlined
                  class="mb-4"
                  @change="previewImage"
                ></v-file-input>

                <div class="image-preview-container">
                  <v-img
                    v-if="imagePreview"
                    :src="imagePreview"
                    max-width="400"
                    aspect-ratio="1"
                  ></v-img>
                </div>

                <v-btn @click="saveExplore" color="primary" class="mt-3" block>Next</v-btn>
              </template>

              <template v-else>
                <div v-for="(content, index) in contents" :key="index" class="mb-4">
                  <v-text-field
                    v-model="content.judul"
                    label="Judul"
                    required
                    outlined
                    class="mb-4"
                  ></v-text-field>

                  <label for="jenis">Jenis</label>
                  <select v-model="content.jenis" id="jenis" class="form-select" required>
                    <option value="1">Pre test</option>
                    <option value="2">Soal</option>
                    <option value="3">Materi</option>
                    <option value="4">Post Test</option>
                  </select>

                  <v-btn @click="removeContent(index)" color="info" class="mt-3" >
                    <i class='bx bx-minus-circle'></i> Remove
                  </v-btn>
                </div>

               <v-btn @click="addContent" color="secondary">
                  <i class='bx bx-plus-circle'></i> Add Content
                </v-btn>
                <v-btn @click="submitContents" color="primary" class="mt-8" block>Submit All</v-btn>
              </template>
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
      step: 1,
      exploreForm: {
        judul: '',
        tentang: '',
        objective: '',
        course: '',
        trainer: '',
        image: null
      },
      contents: [
        { judul: '', jenis: '' },
      ],
      imagePreview: null,
      explore_id: null,
    };
  },
  methods: {
    previewImage(event) {
      const file = event.target.files[0];
      if (file) {
        this.exploreForm.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    async saveExplore() {
      const formData = new FormData();
      Object.entries(this.exploreForm).forEach(([key, value]) => {
        formData.append(key, value);
      });

      try {
        const response = await axios.post('/api/explore', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        if (response.data && response.data.explore_id) {
          this.explore_id = response.data.explore_id;
          console.log('Explore ID saved:', this.explore_id);
          this.step = 2;  // Move to the next step
        } else {
          console.error('Invalid response:', response);
        }

      } catch (error) {
        console.error('Error saving explore:', error);
      }
    },
    addContent() {
      this.contents.push({ judul: '', jenis: '' });
    },
    removeContent(index) {
      this.contents.splice(index, 1);
    },
    async submitContents() {
      try {
        for (let content of this.contents) {
          const formData = {
            judul: content.judul,
            jenis: content.jenis,
            explore_id: this.explore_id
          };

          console.log('Submitting content with explore_id:', this.explore_id);

          await axios.post('/api/content', formData);
        }
        this.$router.push({ path: '/admin/explore' });
      } catch (error) {
        console.error('Error submitting contents:', error);
      }
    }
  }
};
</script>

<style scoped>
.v-container {
  background-color: #f5f5f5;
}
.v-card {
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.image-preview-container {
  display: flex;
  justify-content: center;
  margin-bottom: 16px;
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
</style>
