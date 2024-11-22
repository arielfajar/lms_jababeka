<template>
  <v-container>
    <v-card>
      <v-card-title>
        <h2>Add TTD</h2>
      </v-card-title>

      <v-card-text>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-text-field
            v-model="form.nama"
            :rules="[rules.required]"
            label="Name"
            required
          ></v-text-field>

          <v-text-field
            v-model="form.jabatan"
            :rules="[rules.required]"
            label="Position"
            required
          ></v-text-field>

          <v-file-input
            v-model="form.img"
            :rules="[rules.required]"
            label="Upload Image"
            required
            @change="onFileChange"
          ></v-file-input>

          <v-btn @click="submit" color="primary">Submit</v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      valid: false,
      form: {
        nama: '',
        jabatan: '',
        img: null,
      },
      rules: {
        required: value => !!value || 'Required.',
      },
    };
  },
  methods: {
    onFileChange(event) {
      this.form.img = event.target.files[0];
    },
    async submit() {
      if (this.$refs.form.validate()) {
        const formData = new FormData();
        formData.append('nama', this.form.nama);
        formData.append('jabatan', this.form.jabatan);
        formData.append('img', this.form.img);

        try {
          await axios.post('/api/ttd', formData);
          this.$router.push('/somewhere'); // Redirect or notify user
        } catch (error) {
          console.error('Error:', error);
          // Handle error
        }
      }
    },
  },
};
</script>

<style scoped>
/* Add your styles here */
</style>
