<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card class="mx-auto" max-width="800">
          <v-card-title>
            <h1 class="text-h5">Add New Employee</h1>
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitForm">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.nik"
                    label="NIK"
                    required
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.nama_lengkap"
                    label="Full Name"
                    required
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.username"
                    label="Username"
                    required
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.email"
                    label="Email"
                    required
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.password"
                    label="Password"
                    required
                    type="password"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.role_id"
                    label="Role ID"
                    required
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.no_telp"
                    label="Phone Number"
                    required
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.divisi"
                    label="Divisi"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.approval_line"
                    label="Approval Line"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="employee.join_date"
                    label="Join Date"
                    type="date"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" class="d-flex align-center">
                  <v-file-input
                    v-model="employee.foto"
                    label="Profile Picture"
                    outlined
                    accept="image/*"
                    dense
                    @change="previewImage"
                  ></v-file-input>
                  <v-img
                    v-if="imagePreview"
                    :src="imagePreview"
                    class="rounded-circle ml-4"
                    max-width="120"
                    max-height="120"
                    contain
                  ></v-img>
                  <v-icon
                    v-else
                    class="rounded-circle ml-4 d-flex align-center justify-center"
                    style="border: 2px dashed #ccc; width: 120px; height: 120px;"
                  >
                    bx bx-user
                  </v-icon>
                </v-col>
              </v-row>
              <v-btn type="submit" color="success" class="mt-4" block>Add Employee</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import Swal from 'sweetalert2' // Import SweetAlert2

export default {
  data() {
    return {
      employee: {
        nik: '',
        nama_lengkap: '',
        email: '',
        password: '',
        role_id: '',
        no_telp: '',
        username: '',
        divisi: '',
        approval_line: '',
        join_date: '',
        foto: null, // Untuk menyimpan gambar yang di-upload
      },
      imagePreview: null, // For the image preview
    }
  },
  methods: {
    previewImage(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result; // Set the preview image
        };
        reader.readAsDataURL(file);
      } else {
        this.imagePreview = null; // Reset preview if no file
      }
    },
    async submitForm() {
      const formData = new FormData(); // Use FormData to send data
      for (const key in this.employee) {
        formData.append(key, this.employee[key]);
      }

      // Debugging: Log the formData content
      for (var pair of formData.entries()) {
        console.log(pair[0]+ ': ' + pair[1]); 
      }

      try {
        const response = await axios.post(
          '/api/user/add',
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data', // Important for file upload
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          }
        )
        console.log('Employee added:', response.data)

        // Show success alert
        Swal.fire({
          title: 'Success!',
          text: 'Employee added successfully!',
          icon: 'success',
          confirmButtonText: 'OK'
        }).then(() => {
          // After successful submission, navigate to the employee list
          this.$router.push('/admin/karyawan');
        });

      } catch (error) {
        console.error('Failed to add employee:', error.response.data);
        // Show error alert
        Swal.fire({
          title: 'Error!',
          text: 'Failed to add employee. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    },
  },
}
</script>

<style scoped>
.v-container {
  margin-top: 20px;
}

.v-card {
  padding: 20px;
}

.rounded-circle {
  border-radius: 50%; /* Makes the image circular */
  border: 2px solid #ccc; /* Optional border for the image */
}
</style>
