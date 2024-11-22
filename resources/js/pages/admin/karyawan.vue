<template>
  <v-container>
    <h1 class="title">LIST KARYAWAN JABABEKA</h1>
    <v-row>
      <v-col>
        <v-btn color="primary" @click="goToAddEmployee">
          <i class='bx bx-plus'></i> Add New Employee
        </v-btn>
      </v-col>
      <v-col>
        <v-text-field
          v-model="searchQuery"
          label="Search by name, email, or role..."
          solo
          clearable
        ></v-text-field>
      </v-col>
      <v-col class="d-flex justify-end align-center">
        <v-select
          v-model="entriesPerPage"
          :items="entriesOptions"
          label="Entries per page"
          @change="updateEntriesPerPage"
          style="max-width: 80px;"
        ></v-select>
      </v-col>
    </v-row>

    <!-- Employee data table -->
    <v-table>
      <thead>
        <tr>
          <th @click="sort('index')" style="cursor: pointer;">No</th>
          <th @click="sort('nik')" style="cursor: pointer;">NIK</th>
          <th @click="sort('nama_lengkap')" style="cursor: pointer;">Full Name</th>
          <th @click="sort('username')" style="cursor: pointer;">Username</th>
          <th @click="sort('email')" style="cursor: pointer;">Email</th>
          <th @click="sort('divisi')" style="cursor: pointer;">Divisi</th>
          <th @click="sort('jabatan')" style="cursor: pointer;">Jabatan</th>
          <th @click="sort('approval_line')" style="cursor: pointer;">Approval Line</th>
          <th @click="sort('role_id')" style="cursor: pointer;">Role</th>
          <th @click="sort('no_telp')" style="cursor: pointer;">Phone Number</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(employee, index) in paginatedEmployees" :key="employee.user_id">
          <td>{{ (currentPage - 1) * entriesPerPage + index + 1 }}</td>
          <td>{{ employee.nik }}</td>
          <td>{{ employee.nama_lengkap }}</td>
          <td>{{ employee.username }}</td>
          <td>{{ employee.email }}</td>
          <td>{{ employee.divisi }}</td>
          <td>{{ employee.jabatan }}</td>
          <td>{{ employee.approval_line }}</td>
          <td>{{ employee.role_id}}</td>
          <td>{{ employee.no_telp }}</td>
          <td>
            <v-btn icon @click="openEditModal(employee)">
              <v-icon>bx-pencil</v-icon>
            </v-btn>
            <v-btn icon @click="deleteEmployee(employee.user_id)">
              <v-icon>bx-trash</v-icon>
            </v-btn>
          </td>
        </tr>
      </tbody>
    </v-table>

    <!-- Pagination -->
    <v-pagination
      v-model="currentPage"
      :length="totalPages"
      @input="fetchEmployees"
    ></v-pagination>

    <!-- Edit Employee Modal -->
    <v-dialog v-model="isEditModalVisible" max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Edit Employee</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="editForm">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.nik"
                  label="NIK"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.nama_lengkap"
                  label="Full Name"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.username"
                  label="Username"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.email"
                  label="Email"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.divisi"
                  label="Divisi"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.jabatan"
                  label="Jabatan"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.approval_line"
                  label="Approval Line"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.role_id"
                  label="Role"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentEmployee.no_telp"
                  label="Phone Number"
                  required
                ></v-text-field>
              </v-col>
                <v-col cols="12" md="6">
                <v-text-field
                  v-model="currentPassword"
                  label="Ganti Password"
                  type="password"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6" class="d-flex justify-center">
                <v-img
                  :src="photoPreview || `/storage/${currentEmployee.foto}`"
                  height="100"
                  width="100"
                  v-if="photoPreview || currentEmployee.foto"
                ></v-img>
              </v-col>

                            <v-col cols="12" md="6">
                <v-file-input
                  v-model="photo"
                  label="Change Photo"
                  prepend-icon="bx-image"
                  @change="previewPhoto"
                ></v-file-input>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" @click="updateEmployee">Save Changes</v-btn>
          <v-btn @click="closeEditModal">Cancel</v-btn>
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
      employees: [],
      searchQuery: '',
      sortKey: '',
      sortOrder: 'asc',
      currentPage: 1,
      entriesPerPage: 5,
      entriesOptions: [5, 10, 15, 20],
      isEditModalVisible: false,
      currentEmployee: {},
      photo: null, // Variable to hold the new photo
      photoPreview: '', // Variable for the photo preview
      currentPassword: '', // Variable for current password

    };
  },
  mounted() {
    this.getEmployeesData();
  },
  computed: {
    filteredEmployees() {
      return this.employees
        .filter(employee => {
          return (
            employee.nama_lengkap.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            employee.username.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            employee.email.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            employee.role_id.toLowerCase().includes(this.searchQuery.toLowerCase())
          );
        })
        .sort((a, b) => {
          if (this.sortKey) {
            const modifier = this.sortOrder === 'asc' ? 1 : -1;
            if (a[this.sortKey] < b[this.sortKey]) return -1 * modifier;
            if (a[this.sortKey] > b[this.sortKey]) return 1 * modifier;
          }
          return 0;
        });
    },
    paginatedEmployees() {
      const start = (this.currentPage - 1) * this.entriesPerPage;
      return this.filteredEmployees.slice(start, start + this.entriesPerPage);
    },
    totalPages() {
      return Math.ceil(this.filteredEmployees.length / this.entriesPerPage);
    },
  },
  methods: {
    async getEmployeesData() {
      try {
        const response = await axios.get('/api/user?list=true', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.employees = response.data;
      } catch (error) {
        console.error('Error fetching employee data:', error);
      }
    },
    goToAddEmployee() {
      this.$router.push('./addkaryawan');
    },
    openEditModal(employee) {
      this.currentEmployee = { ...employee }; // Create a copy of the employee data
      this.photoPreview = ''; // Reset the preview
      this.isEditModalVisible = true; // Show the modal
    },
    closeEditModal() {
      this.isEditModalVisible = false; // Hide the modal
      this.resetForm(); // Reset the form fields
    },
    resetForm() {
      this.photo = null; // Reset photo input
      this.photoPreview = ''; // Reset photo preview
      this.currentPassword = ''; // Reset password input
      this.confirmPassword = ''; // Reset confirm password input
    },
    previewPhoto(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.photoPreview = e.target.result; // Set the preview to the file data URL
        };
        reader.readAsDataURL(file);
      }
    },
    async updateEmployee() {
      const formData = new FormData();
      formData.append('nik', this.currentEmployee.nik);
      formData.append('nama_lengkap', this.currentEmployee.nama_lengkap);
      formData.append('username', this.currentEmployee.username);
      formData.append('email', this.currentEmployee.email);
      formData.append('divisi', this.currentEmployee.divisi);
      formData.append('jabatan', this.currentEmployee.jabatan);
      formData.append('approval_line', this.currentEmployee.approval_line);
      formData.append('role_id', this.currentEmployee.role_id);
      formData.append('no_telp', this.currentEmployee.no_telp);

      if (this.photo) {
        formData.append('foto', this.photo); // Append the new photo if provided
      }

      if (this.currentPassword) {
    formData.append('password', this.currentPassword); // Append new password if provided
  }

      try {
        await axios.post(`/api/user/${this.currentEmployee.user_id}`, formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data',
          },
        });

        Swal.fire('Updated!', 'Employee data has been updated.', 'success');
        this.getEmployeesData(); // Refresh employee data
        this.closeEditModal(); // Close modal
      } catch (error) {
        console.error('Error updating employee:', error);
        Swal.fire('Error!', 'There was a problem updating the employee data.', 'error');
      }
    },
    async deleteEmployee(userId) {
      const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'You won’t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/user/${userId}`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          });
          this.employees = this.employees.filter(emp => emp.user_id !== userId); // Remove deleted employee from the list
          Swal.fire('Deleted!', 'Employee data has been deleted.', 'success');
        } catch (error) {
          console.error('Error deleting employee:', error);
          Swal.fire('Error!', 'There was a problem deleting the employee data.', 'error');
        }
      }
    },
    updateEntriesPerPage() {
      this.currentPage = 1; // Reset to first page on entries change
    },
    sort(key) {
      this.sortKey = key;
      this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
    },
  },
};
</script>

<style scoped>
.title {
  margin-bottom: 20px;
}
.v-table th {
  background: rgba(1, 228, 0, 0.1);
  text-align: left;
}
</style>
