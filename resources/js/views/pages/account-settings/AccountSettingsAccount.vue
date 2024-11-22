<script setup>
import { ref, onMounted } from 'vue'
import { useTheme } from 'vuetify'
import axios from 'axios'
import Swal from 'sweetalert2'
import avatar1 from '@images/avatars/avatar-1.png'

const accountData = {
  nik: '',
  role_id: '',
  nama_lengkap: '',
  username: '',
  email: '',
  password: '',
  foto: '',
  divisi: '',
  approval_line: '',
  join_date: '',
  no_telp: '',
}

const refInputEl = ref()
const accountDataLocal = ref(structuredClone(accountData))
const isAccountDeactivated = ref(false)

// Reset form
const resetForm = () => {
  accountDataLocal.value = structuredClone(accountData)
}

// Mengganti avatar menggunakan FileReader
const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string')
        accountDataLocal.value.foto = fileReader.result
    }
  }
}

// Mengembalikan avatar ke yang sebelumnya
const resetAvatar = () => {
  accountDataLocal.value.foto = accountData.foto
}

// Mendapatkan data user dari API
const getUserData = async () => {
  try {
    const response = await axios.get('/api/user', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    accountDataLocal.value = response.data
  } catch (error) {
    console.error('Gagal mengambil data pengguna:', error)
  }
}

const saveUserData = async () => {
  // Check if new password and confirmation match
  if (accountDataLocal.value.password !== accountDataLocal.value.password_confirmation) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Password dan konfirmasi password harus sama.',
    });
    return; // Exit the function if passwords do not match
  }
  try {
    const formData = new FormData();
    
    // Append the form data
    formData.append('nik', accountDataLocal.value.nik);
    formData.append('role_id', accountDataLocal.value.role_id);
    formData.append('nama_lengkap', accountDataLocal.value.nama_lengkap);
    formData.append('username', accountDataLocal.value.username);
    formData.append('email', accountDataLocal.value.email);
    formData.append('no_telp', accountDataLocal.value.no_telp);
    formData.append('divisi', accountDataLocal.value.divisi);
    // formData.append('approval_line', accountDataLocal.value.approval_line);
    formData.append('join_date', accountDataLocal.value.join_date);

    // Append the file (avatar image)
    if (refInputEl.value.files[0]) {
      formData.append('foto', refInputEl.value.files[0]); // Attach the file only if it exists
    }
    // Append the password and confirmation
    if (accountDataLocal.value.password) {
      formData.append('password', accountDataLocal.value.password);
      formData.append('password_confirmation', accountDataLocal.value.password_confirmation);
    }

    const response = await axios.post('/api/user/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      }
    });

    // Update local state with the response
    Object.assign(accountDataLocal.value, response.data);

    Swal.fire({
      icon: 'success',
      title: 'Data Berhasil Diubah',
      showConfirmButton: false,
      timer: 1500
    });
  } catch (error) {
    console.error('Error saving user data:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Gagal menyimpan data pengguna. Silakan coba lagi.',
    });
  }
};
const units = ref([])

// Ambil data unit dari API
const getUnits = async () => {
  try {
    const response = await axios.get('/api/units')
    units.value = response.data
  } catch (error) {
    console.error('Gagal mengambil data unit:', error)
  }
}

onMounted(() => {
  getUserData()
  getUnits()
})
</script>

<template>
  <VRow justify="center">
    <VCol cols="12">
      <VCard title="Account Details">
        <VCardText class="d-flex">
          <!-- Avatar -->
          <VAvatar
            rounded="lg"
            size="100"
            class="me-6"
            :image="accountDataLocal.foto ? `/storage/${accountDataLocal.foto}` : avatar1"
          />

          <!-- Upload Photo -->
          <form class="d-flex flex-column justify-center gap-5" @submit.prevent="saveUserData" enctype="multipart/form-data">
            <div class="d-flex flex-wrap gap-2">
              <VBtn
                color="primary"
                @click="refInputEl?.click()"
              >
                <VIcon
                  icon="bx-cloud-upload"
                  class="d-sm-none"
                />
                <span class="d-none d-sm-block">Upload new photo</span>
              </VBtn>

              <input
                ref="refInputEl"
                type="file"
                name="file"
                accept=".jpeg,.png,.jpg,GIF"
                hidden
                @input="changeAvatar"
              >

              <VBtn
                type="reset"
                color="error"
                variant="tonal"
                @click="resetAvatar"
              >
                <span class="d-none d-sm-block">Reset</span>
                <VIcon
                  icon="bx-refresh"
                  class="d-sm-none"
                />
              </VBtn>
            </div>

            <p class="text-body-1 mb-0">
              Allowed JPG, GIF or PNG. Max size of 800K
            </p>
          </form>
        </VCardText>

        <VDivider />

        <VCardText>
          <!-- Form -->
          <VForm class="mt-6">
            <VRow>
              <VCol md="6" cols="12">
                <VTextField v-model="accountDataLocal.nik" placeholder="1234567890" label="NIK" />
              </VCol>

              <!-- <VCol md="6" cols="12">
                <VSelect v-model="accountDataLocal.role_id" label="Role ID" placeholder="Select Role" :items="['1', '2', '3', '4', '5']" />
              </VCol> -->

              <VCol md="6" cols="12">
                <VTextField v-model="accountDataLocal.nama_lengkap" placeholder="John Doe" label="Nama Lengkap" />
              </VCol>

              <VCol md="6" cols="12">
                <VTextField v-model="accountDataLocal.username" placeholder="johndoe" label="Username" />
              </VCol>

              <VCol cols="12" md="6">
                <VTextField v-model="accountDataLocal.email" label="E-mail" placeholder="johndoe@example.com" type="email" />
              </VCol>

              <VCol cols="12" md="6">
                <VTextField v-model="accountDataLocal.no_telp" label="No Telp" placeholder="+1 (917) 543-9876" />
              </VCol>

              <VCol cols="12" md="6">
                <VSelect
                  v-model="accountDataLocal.divisi"
                  label="Divisi"
                  :items="units.map(unit => unit.divisi)"
                  item-value="unit_nama"
                  item-text="unit_nama"
                  placeholder="Pilih Divisi"
                />
              </VCol>

              <VCol cols="12" md="6">
                <VTextField v-model="accountDataLocal.approval_line" label="Approval Line" placeholder="123" />
              </VCol>

              <VCol cols="12" md="6">
                <VTextField v-model="accountDataLocal.join_date" label="Join Date" placeholder="2022-01-01" type="date" />
              </VCol>
              <VCol cols="12" md="6">
              <VTextField
                  v-model="accountDataLocal.password"
                  label="New Password"
                  placeholder="Enter new password"
                  type="password"
              />
              </VCol>

              <VCol cols="12" md="6">
                  <VTextField
                      v-model="accountDataLocal.password_confirmation"
                      label="Confirm Password"
                      placeholder="Confirm new password"
                      type="password"
                  />
              </VCol>

              <VCol cols="12" class="d-flex flex-wrap gap-4">
                <VBtn @click="saveUserData">Save changes</VBtn>
                <VBtn color="secondary" variant="tonal" type="reset" @click.prevent="resetForm">Reset</VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>
